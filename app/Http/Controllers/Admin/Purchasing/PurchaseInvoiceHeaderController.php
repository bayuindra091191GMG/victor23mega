<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 14/03/2018
 * Time: 14:38
 */

namespace App\Http\Controllers\Admin\Purchasing;


use App\Http\Controllers\Controller;
use App\Libs\Utilities;
use App\Models\NumberingSystem;
use App\Models\PermissionMenu;
use App\Models\PurchaseInvoiceDetail;
use App\Models\PurchaseInvoiceHeader;
use App\Models\PurchaseInvoiceRepayment;
use App\Models\PurchaseOrderHeader;
use App\Models\Site;
use App\Transformer\Purchasing\PurchaseInvoiceHeaderTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade as PDF;
use PDF3;

class PurchaseInvoiceHeaderController extends Controller
{
    public function index(){

        return View('admin.purchasing.purchase_invoices.index');
    }

    public function show(PurchaseInvoiceHeader $purchase_invoice){
        $user = \Auth::user();

        // Check menu permission
        $roleId = $user->roles->pluck('id')[0];
        if(!PermissionMenu::where('role_id', $roleId)->where('menu_id', 27)->first()){
            Session::flash('error', 'Level Akses anda tidak mencukupi!');
            return redirect()->back();
        }

        $header = $purchase_invoice;
        $repayment = PurchaseInvoiceRepayment::where('purchase_invoice_header_id', $header->id)->get();

        // Get total discount;
        $individualDiscount = $header->total_discount ?? 0;
        $extraDiscount = $header->extra_discount ?? 0;
        $totalDiscount = $individualDiscount + $extraDiscount;
        $totalDiscountStr = number_format($totalDiscount, 2, ",", ".");

        // Get MR Type
        $mrType = $purchase_invoice->purchase_order_header->purchase_request_header->material_request_header->type;

        $data = [
            'header'            => $header,
            'repayment'         => $repayment,
            'totalDiscountStr'  => $totalDiscountStr,
            'mrType'            => $mrType
        ];

        return View('admin.purchasing.purchase_invoices.show')->with($data);
    }

    public function beforeCreate(){
        return View('admin.purchasing.purchase_invoices.before_create');
    }

    public function create(){
        if(empty(request()->po)){
            return redirect()->route('admin.purchase_invoices.before_create');
        }

        $purchaseOrder = PurchaseOrderHeader::find(request()->po);

        // Numbering System
        $user = Auth::user();
        $sysNo = NumberingSystem::where('doc_id', '6')->first();
        $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumber($docCode, $sysNo->next_no);

        // Get MR Type
        $mrType = $purchaseOrder->purchase_request_header->material_request_header->type;

        $data = [
            'purchaseOrder'   => $purchaseOrder,
            'autoNumber'        => $autoNumber
        ];

        if($mrType === 4){
            return View('admin.purchasing.purchase_invoices.create_service')->with($data);
        }

        return View('admin.purchasing.purchase_invoices.create')->with($data);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'code'      => 'max:45|regex:/^\S*$/u',
            'date'      => 'required'
        ],[
            'code.regex'    => 'Nomor Invoice harus tanpa spasi!'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate invoice number
        if(!$request->filled('auto_number') && (!$request->filled('code') || $request->input('code') == "")){
            return redirect()->back()->withErrors('Nomor Invoice wajib diisi!', 'default')->withInput($request->all());
        }

        // Get PO id
        $poId = $request->input('po_id');
        $poHeader = PurchaseOrderHeader::find($poId);

        // Validate details
        $items = $request->input('item_value');
        $qtys = $request->input('qty');
        $prices = $request->input('price');
        $discounts = $request->input('discount');
        $valid = true;
        $i = 0;
        foreach($items as $item){
            if(empty($item)) $valid = false;
            if(empty($qtys[$i]) || $qtys[$i] == '0') $valid = false;
            if(empty($prices[$i]) || $prices[$i] == '0') $valid = false;

            // Validate discount
            $priceVad = Utilities::toFloat($prices[$i]);
            $discountVad = Utilities::toFloat($discounts[$i]);
            if( $discountVad > ( $priceVad * (double) $qtys[$i])) return redirect()->back()->withErrors('Diskon tidak boleh melebihi harga!', 'default')->withInput($request->all());

            $i++;
        }

        if(!$valid){
            return redirect()->back()->withErrors('Detail inventory, jumlah dan harga wajib diisi!', 'default')->withInput($request->all());
        }

        // Check duplicate inventory
        $valid = Utilities::arrayIsUnique($items);
        if(!$valid){
            return redirect()->back()->withErrors('Detail inventory tidak boleh kembar!', 'default')->withInput($request->all());
        }

        // Validate PO relationship
        $validItem = true;
        $validQty = true;
        $validReceived = true;
        $i = 0;
        $purchaseOrder = PurchaseOrderHeader::find($poId);
        foreach($items as $item){
            if(!empty($item)){
                $poDetail = $purchaseOrder->purchase_order_details->where('item_id', $item)->first();
                if(empty($poDetail)){
                    $validItem = false;
                    break;
                }
                else{
                    $qtyInt = (int) $qtys[$i];
                    $qtyResult = $poDetail->quantity - $poDetail->quantity_invoiced;
                    if($qtyInt > $qtyResult){
                        $validQty = false;
                        break;
                    }

                    $qtyReceived = $poDetail->received_quantity;
                    if($qtyInt > $qtyReceived){
                        $validReceived = false;
                        break;
                    }
                }
                $i++;
            }
        }

        if(!$validItem){
            return redirect()->back()->withErrors('Inventory tidak ada dalam PO!', 'default')->withInput($request->all());
        }

        if(!$validQty){
            return redirect()->back()->withErrors('Kuantitas inventory melebihi kuantitas inventory sudah di-invoice pada PO!', 'default')->withInput($request->all());
        }

        if(!$validReceived){
            return redirect()->back()->withErrors('Kuantitas inventory melebihi kuantitas inventory sudah diterima pada PO!', 'default')->withInput($request->all());
        }

        $user = Auth::user();

        // Generate auto number
        $invCode = 'default';
        if($request->filled('auto_number')){
            $sysNo = NumberingSystem::where('doc_id', '6')->first();
            $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
            $invCode = Utilities::GenerateNumberPurchaseOrder($docCode, $sysNo->next_no);

            // Check existing number
            if(PurchaseInvoiceHeader::where('code', $invCode)->exists()){
                return redirect()->back()->withErrors('Nomor Invoice sudah terdaftar!', 'default')->withInput($request->all());
            }

            $sysNo->next_no++;
            $sysNo->save();
        }
        else{
            $invCode = $request->input('code');

            // Check existing number
            if(PurchaseInvoiceHeader::where('code', $invCode)->exists()){
                return redirect()->back()->withErrors('Nomor Invoice sudah terdaftar!', 'default')->withInput($request->all());
            }
        }

        $now = Carbon::now('Asia/Jakarta');

        $invHeader = PurchaseInvoiceHeader::create([
            'code'                  => $invCode,
            'purchase_order_id'     => $poId,
            'total_discount'        => 0,
            'extra_discount'        => 0,
            'pph_amount'            => 0,
            'ppn_amount'            => 0,
            'repayment_amount'      => 0,
            'is_retur'              => 0,
            'status_id'             => 3,
            'created_by'            => $user->id,
            'created_at'            => $now->toDateTimeString(),
            'updated_by'            => $user->id,
            'updated_at'            => $now->toDateTimeString(),
            'site_id'               => $poHeader->purchase_request_header->material_request_header->site_id,
            'mr_type'               => $poHeader->purchase_request_header->material_request_header->type
        ]);

        $extraDiscount = 0;
        if($request->filled('extra_discount')){
            $extraDiscount = Utilities::toFloat($request->input('extra_discount'));
            $invHeader->extra_discount = $extraDiscount;
        }

        $delivery = 0;
        if($request->filled('delivery_fee') && $request->input('delivery_fee') != '0'){
            $delivery = Utilities::toFloat($request->input('delivery_fee'));
            $invHeader->delivery_fee = $delivery;
        }

        if($request->filled('payment_term')){
            $invHeader->payment_term = $request->input('payment_term');
        }

        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');
        $invHeader->date = $date->toDateTimeString();

        $invHeader->save();

        // Create po detail
        $totalPrice = 0;
        $totalDiscount = 0;
        $totalPayment = 0;
        $remarks = $request->input('remark');
        $idx = 0;

        foreach($items as $item){
            if(!empty($item)){
                $price = Utilities::toFloat($prices[$idx]);
                $qty = (double) $qtys[$idx];
                $invDetail = PurchaseInvoiceDetail::create([
                    'header_id'         => $invHeader->id,
                    'item_id'           => $item,
                    'quantity'          => $qty,
                    'quantity_retur'    => 0,
                    'price'             => $price
                ]);

                // Check discount
                if(!empty($discounts[$idx]) && $discounts[$idx] !== '0'){
                    $discount = Utilities::toFloat($discounts[$idx]);

                    $invDetail->discount = $discount;
                    $invDetail->subtotal = ($qty * $price) - $discount;

                    // Accumulate total price
                    $totalPrice += $qty * $price;

                    // Accumulate total discount
                    $totalDiscount += $discount;
                }
                else{
                    $invDetail->subtotal = $qty * $price;
                    $totalPrice += $qty * $price;
                }

                if(!empty($remarks[$idx])) $invDetail->remark = $remarks[$idx];
                $invDetail->save();

                // Accumulate subtotal
                $totalPayment += $invDetail->subtotal;
            }
            $idx++;
        }

        if($totalDiscount > 0) $invHeader->total_discount = $totalDiscount;
        $invHeader->total_price = $totalPrice;

        // Save total payment without tax
        $invHeader->total_payment_before_tax = $totalPayment - $extraDiscount;

        // Get PPN & PPh
        $ppnAmount = 0;
        if($request->filled('ppn') && $request->input('ppn') != '0'){
            $ppnAmount = $totalPayment * (10 / 100);
            $invHeader->ppn_percent = 10;
            $invHeader->ppn_amount = $ppnAmount;
        }
        $pphAmount = 0;
        if($request->filled('pph') && $request->input('pph') != '0'){
            $pphAmount = Utilities::toFloat($request->input('pph'));
            $invHeader->pph_amount = $pphAmount;
        }

        $invHeader->total_payment = $totalPayment + $delivery + $ppnAmount - $pphAmount;
        $invHeader->save();

        // Check PO
        $purchaseOrder = $invHeader->purchase_order_header;
        $idx = 0;
        $isAllInvoicedPO = true;
        foreach($items as $item){
            if(!empty($item))
            {
                $isAllInvoicedPO = true;
                $qty = (double) $qtys[$idx];

                foreach($purchaseOrder->purchase_order_details as $detail){
                    // Update PO
                    if($detail->item_id == $item){
                        $poDetail = $detail;
                        $poDetail->quantity_invoiced += $qty;
                        $poDetail->save();
                    }

                    // Check invoiced qty
                    if($detail->quantity > $detail->quantity_invoiced){
                        $isAllInvoicedPO = false;
                    }
                }
            }
            $idx++;
        }

        // Close PO
        if($isAllInvoicedPO){
            $purchaseOrder->is_all_invoiced = 1;
            $purchaseOrder->status_id = 4;
            $purchaseOrder->closing_date = $now->toDateString();
            $purchaseOrder->save();

            // Check PR
            $purchaseRequest = $purchaseOrder->purchase_request_header;
            $idx = 0;
            $isInvoicedPR = true;
            foreach($items as $item){
                if(!empty($item))
                {
                    $isInvoicedPR = true;
                    $qty = (double) $qtys[$idx];

                    foreach($purchaseRequest->purchase_request_details as $detail){
                        // Update PO
                        if($detail->item_id == $item){
                            $prDetail = $detail;
                            $prDetail->quantity_invoiced += $qty;
                            $prDetail->save();
                        }

                        // Check invoiced qty
                        if($detail->quantity != $detail->quantity_invoiced){
                            $isInvoicedPR = false;
                        }
                    }
                }
                $idx++;
            }

            if($isInvoicedPR){
                $purchaseRequest->status_id = 4;
                $purchaseRequest->closed_at = $now->toDateString();
                $purchaseRequest->save();
            }
        }
        else{
            $purchaseOrder->is_all_invoiced = 2;
            $purchaseOrder->save();
        }

        Session::flash('message', 'Berhasil membuat Purchase Invoice!');

        return redirect()->route('admin.purchase_invoices.show', ['purchase_invoice' => $invHeader]);
    }

    public function storeService(Request $request){
        $validator = Validator::make($request->all(),[
            'code'      => 'max:45|regex:/^\S*$/u',
            'date'      => 'required'
        ],[
            'code.regex'    => 'Nomor Invoice harus tanpa spasi!'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate invoice number
        if(!$request->filled('auto_number') && (!$request->filled('code') || $request->input('code') == "")){
            return redirect()->back()->withErrors('Nomor Invoice wajib diisi!', 'default')->withInput($request->all());
        }

        // Get PO id
        $poId = $request->input('po_id');
        $poHeader = PurchaseOrderHeader::find($poId);

        $user = Auth::user();

        // Generate auto number
        $invCode = 'default';
        if($request->filled('auto_number')){
            $sysNo = NumberingSystem::where('doc_id', '6')->first();
            $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
            $invCode = Utilities::GenerateNumberPurchaseOrder($docCode, $sysNo->next_no);

            // Check existing number
            if(PurchaseInvoiceHeader::where('code', $invCode)->exists()){
                return redirect()->back()->withErrors('Nomor Invoice sudah terdaftar!', 'default')->withInput($request->all());
            }

            $sysNo->next_no++;
            $sysNo->save();
        }
        else{
            $invCode = $request->input('code');

            // Check existing number
            if(PurchaseInvoiceHeader::where('code', $invCode)->exists()){
                return redirect()->back()->withErrors('Nomor Invoice sudah terdaftar!', 'default')->withInput($request->all());
            }
        }

        $now = Carbon::now('Asia/Jakarta');

        $invHeader = PurchaseInvoiceHeader::create([
            'code'                  => $invCode,
            'purchase_order_id'     => $poId,
            'total_discount'        => 0,
            'extra_discount'        => 0,
            'pph_amount'            => 0,
            'ppn_amount'            => 0,
            'repayment_amount'      => 0,
            'is_retur'              => 0,
            'status_id'             => 3,
            'created_by'            => $user->id,
            'created_at'            => $now->toDateTimeString(),
            'updated_by'            => $user->id,
            'updated_at'            => $now->toDateTimeString(),
            'site_id'               => $poHeader->purchase_request_header->material_request_header->site_id,
            'mr_type'               => $poHeader->purchase_request_header->material_request_header->type
        ]);

        $extraDiscount = 0;
        if($request->filled('extra_discount')){
            $extraDiscount = Utilities::toFloat($request->input('extra_discount'));
            $invHeader->extra_discount = $extraDiscount;
        }

        $delivery = 0;
        if($request->filled('delivery_fee') && $request->input('delivery_fee') != '0'){
            $delivery = Utilities::toFloat($request->input('delivery_fee'));
            $invHeader->delivery_fee = $delivery;
        }

        if($request->filled('payment_term')){
            $invHeader->payment_term = $request->input('payment_term');
        }

        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');
        $invHeader->date = $date->toDateTimeString();

        $invHeader->save();

        // Create po detail
        $purchaseOrder = $invHeader->purchase_order_header;

        $totalPrice = 0;
        $totalDiscount = 0;
        $totalPayment = 0;
        $remarks = $request->input('remark');
        $idx = 0;

        $price = $purchaseOrder->purchase_order_details->first()->price;
        $totalPayment = $price;
        $totalPrice = $price;
        $invDetail = PurchaseInvoiceDetail::create([
            'header_id'         => $invHeader->id,
            'item_id'           => 0,
            'quantity'          => 1,
            'quantity_retur'    => 0,
            'price'             => $price,
            'discount'          => 0,
            'subtotal'          => $price,
            'remark'            => $purchaseOrder->purchase_order_details->first()->remark
        ]);

        if($totalDiscount > 0) $invHeader->total_discount = $totalDiscount;
        $invHeader->total_price = $totalPrice;

        // Save total payment without tax
        $totalPayment -= $extraDiscount;
        $invHeader->total_payment_before_tax = $totalPayment;

        // Get PPN & PPh
        $ppnAmount = 0;
        if($request->filled('ppn') && $request->input('ppn') != '0'){
            $ppnAmount = $totalPayment * (10 / 100);
            $invHeader->ppn_percent = 10;
            $invHeader->ppn_amount = $ppnAmount;
        }
        $pphAmount = 0;
        if($request->filled('pph') && $request->input('pph') != '0'){
            $pphAmount = Utilities::toFloat($request->input('pph'));
            $invHeader->pph_amount = $pphAmount;
        }

        $invHeader->total_payment = $totalPayment + $delivery + $ppnAmount - $pphAmount;
        $invHeader->save();

        // Check PO
        $poDetail = $purchaseOrder->purchase_order_details->first();
        $poDetail->quantity_invoiced = 1;
        $poDetail->save();

        // Close PO
        $purchaseOrder->is_all_invoiced = 1;
        $purchaseOrder->status_id = 4;
        $purchaseOrder->closing_date = $now->toDateString();
        $purchaseOrder->save();

        // Check PR
        $purchaseRequest = $purchaseOrder->purchase_request_header;
        $prDetail = $purchaseRequest->purchase_request_details->first();
        $prDetail->quantity_invoiced = 1;
        $prDetail->save();

        $purchaseRequest->status_id = 4;
        $purchaseRequest->closed_at = $now->toDateString();
        $purchaseRequest->save();

        Session::flash('message', 'Berhasil membuat Purchase Invoice Servis!');

        return redirect()->route('admin.purchase_invoices.show', ['purchase_invoice' => $invHeader]);
    }

    public function edit(PurchaseInvoiceHeader $purchase_invoice){
        $header = $purchase_invoice;
        $date = Carbon::parse($purchase_invoice->date)->format('d M Y');

        $data = [
            'header'    => $header,
            'date'      => $date
        ];

        return View('admin.purchasing.purchase_invoices.edit')->with($data);
    }

    public function update(Request $request, PurchaseInvoiceHeader $purchase_invoice){
        $validator = Validator::make($request->all(),[
            'date'      => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

//        if($request->filled('po_code')) $purchase_invoice->purchase_order_id = $request->input('po_code');

        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');
        $purchase_invoice->date = $date->toDateTimeString();

        if($request->filled('payment_term')){
            $purchase_invoice->payment_term = $request->input('payment_term');
        }
        else{
            $purchase_invoice->payment_term = null;
        }

        $totalPaymentWithoutTax = $purchase_invoice->total_payment_before_tax;

        $newDelivery = 0;
        if($request->filled('delivery_fee') && $request->input('delivery_fee') != '0'){
            $newDelivery = Utilities::toFloat($request->input('delivery_fee'));
            $purchase_invoice->delivery_fee = $newDelivery;
        }
        else{
            $purchase_invoice->delivery_fee = null;
        }

        $oldExtraDiscount = $purchase_invoice->extra_discount ?? 0;
        $newExtraDiscount = 0;
        if($request->filled('extra_discount') && $request->input('extra_discount') != '0'){
            $newExtraDiscount = Utilities::toFloat($request->input('extra_discount'));
            $purchase_invoice->extra_discount = $newExtraDiscount;
        }
        else{
            $purchase_invoice->extra_discount = null;
        }

        $totalPayment = $totalPaymentWithoutTax - $oldExtraDiscount + $newExtraDiscount;
        $purchase_invoice->total_payment_before_tax = $totalPayment;

        // Get PPN & PPh
        $ppnAmount = 0;
        if($request->filled('ppn')){
            $ppnAmount = $totalPayment * (10 / 100);
            $purchase_invoice->ppn_percent = 10;
            $purchase_invoice->ppn_amount = $ppnAmount;
        }
        else{
            $purchase_invoice->ppn_percent = null;
            $purchase_invoice->ppn_amount = null;
        }

        $pphAmount = 0;
        if($request->filled('pph')){
            $pphAmount = Utilities::toFloat($request->input('pph'));
            $purchase_invoice->pph_amount = $pphAmount;
        }
        else{
            $purchase_invoice->pph_percent = null;
            $purchase_invoice->pph_amount = null;
        }

        $purchase_invoice->total_payment = $totalPayment + $newDelivery + $ppnAmount - $pphAmount;
        $purchase_invoice->save();

        Session::flash('message', 'Berhasil ubah purchase invoice!');

        return redirect()->route('admin.purchase_invoices.show', ['purchase_invoice' => $purchase_invoice]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getIndex(Request $request){
        try{
            $mode = 'default';
            if($request->filled('mode')){
                $mode = $request->input('mode');
            }

            if($request->filled('supplier')){
                $supplier = $request->input('supplier');
                $temp = PurchaseInvoiceHeader::dateDescending()->get();
                $purchaseInvoices = $temp->where('purchase_order_header.supplier_id', $supplier);
            }
            else{
                $purchaseInvoices = PurchaseInvoiceHeader::query();
            }

            return DataTables::of($purchaseInvoices)
                ->setTransformer(new PurchaseInvoiceHeaderTransformer($mode))
                ->addIndexColumn()
                ->make(true);
        }
        catch(\Exception $ex){
            error_log($ex);
        }
    }

    public function getPurchaseInvoices(Request $request){
        $term = trim($request->q);

        $invoices = null;
        if(!empty($request->supplier)){
            $supplierId = $request->supplier;
            $invoices = PurchaseInvoiceHeader::whereHas('purchase_order_header', function($query) use($supplierId){
                    $query->where('vendor_id', $supplierId);
                })
                ->where('code', 'LIKE', '%'. $term. '%')
                    ->get();
        }
        else{
            $invoices = PurchaseInvoiceHeader::where('code', 'LIKE', '%'. $term. '%')
                ->get();
        }

        $formatted_tags = [];

        foreach ($invoices as $invoice) {
            $formatted_tags[] = ['id' => $invoice->id, 'text' => $invoice->code];
        }

        return \Response::json($formatted_tags);
    }

    public function report(){
        return View('admin.purchasing.purchase_invoices.report');
    }

    public function downloadReport(Request $request) {
        $validator = Validator::make($request->all(),[
            'start_date'        => 'required',
            'end_date'          => 'required',
        ],[
            'start_date.required'   => 'Dari Tanggal wajib diisi!',
            'end_date.required'     => 'Sampai Tanggal wajib diisi!',

        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $start = Carbon::createFromFormat('d M Y', $request->input('start_date'), 'Asia/Jakarta');
        $end = Carbon::createFromFormat('d M Y', $request->input('end_date'), 'Asia/Jakarta');

        // Validate date
        if($start->gt($end)){
            return redirect()->back()->withErrors('Dari Tanggal tidak boleh lebih besar dari Sampai Tanggal!', 'default')->withInput($request->all());
        }

        $start = $start->addDays(-1);
        $end = $end->addDays(1);

        $data = PurchaseInvoiceHeader::whereBetween('date', array($start, $end))->get();

        // Check Data
        if($data == null || $data->count() == 0){
            return redirect()->back()->withErrors('Data Tidak Ditemukan!', 'default')->withInput($request->all());
        }

        if($request->input('is_preview') === 'false'){
            $data =[
                'data'         => $data,
                'start_date'   => $request->input('start_date'),
                'finish_date'  => $request->input('end_date')
            ];

            //return view('documents.material_requests.material_requests_pdf')->with($data);

            $now = Carbon::now('Asia/Jakarta');
            $filename = 'PURCHASE_INVOICE_REPORT_' . $now->toDateTimeString();

            $pdf = PDF3::loadView('documents.purchase_invoices.purchase_invoices_pdf', $data)
                ->setOption('footer-right', '[page] of [toPage]');

            return $pdf->download($filename.'.pdf');
        }
        else{
            $data =[
                'data'         => $data,
                'start_date'   => $request->input('start_date'),
                'finish_date'  => $request->input('end_date')
            ];

            return view('documents.purchase_invoices.purchase_invoices_pdf_preview')->with($data);
        }
    }

    public function reportSite(){
        $sites = Site::orderBy('name')->get();

        return View('admin.purchasing.purchase_invoices.report_site', compact('sites'));
    }

    public function downloadReportSite(Request $request) {
        $validator = Validator::make($request->all(),[
            'start_date'        => 'required',
            'end_date'          => 'required',
        ],[
            'start_date.required'   => 'Dari Tanggal wajib diisi!',
            'end_date.required'     => 'Sampai Tanggal wajib diisi!',

        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $start = Carbon::createFromFormat('d M Y', $request->input('start_date'), 'Asia/Jakarta');
        $end = Carbon::createFromFormat('d M Y', $request->input('end_date'), 'Asia/Jakarta');

        // Validate date
        if($start->gt($end)){
            return redirect()->back()->withErrors('Dari Tanggal tidak boleh lebih besar dari Sampai Tanggal!', 'default')->withInput($request->all());
        }

        $start = $start->addDays(-1);
        $end = $end->addDays(1);

        $piHeaders = PurchaseInvoiceHeader::whereBetween('date', array($start, $end));

        $filterSite = 'Semua';
        if($request->input('site') !== '-1'){
            $site = Site::find($request->input('site'));
            $filterSite = $site->name;
            $piHeaders = $piHeaders->where('site_id', $site->id);

            $sites = Site::where('id', $site->id)->get();
        }
        else{
            $sites = Site::all();
        }

        $filterMrType = 'Semua';
        if($request->input('mr_type') !== '-1'){
            $mrType = $request->input('mr_type');
            if($mrType === '1'){
                $filterMrType = 'PART/NON-PART';
            }
            elseif($mrType === '2'){
                $filterMrType = 'BBM';
            }
            elseif($mrType === '3'){
                $filterMrType = 'OLI';
            }
            else{
                $filterMrType = 'SERVIS';
            }

            $piHeaders = $piHeaders->where('mr_type', $mrType);
        }

        $piHeaders = $piHeaders->orderBy('site_id')
            ->orderByDesc('date')
            ->get();

        // Check Data
        if($piHeaders->count() == 0){
            return redirect()->back()->withErrors('Data Tidak Ditemukan!', 'default')->withInput($request->all());
        }

        if($request->input('is_preview') === 'false'){
            $data =[
                'filterSite'    => $filterSite,
                'filterMrType'  => $filterMrType,
                'sites'         => $sites,
                'piHeaders'     => $piHeaders,
                'start_date'    => $request->input('start_date'),
                'end_date'      => $request->input('end_date')
            ];

            //return view('documents.material_requests.material_requests_pdf')->with($data);

            $now = Carbon::now('Asia/Jakarta');
            $filename = 'PURCHASE_INVOICE_REPORT_' . $now->toDateTimeString();

            $pdf = PDF3::loadView('documents.purchase_invoices.purchase_invoices_site_pdf', $data)
                ->setOption('footer-right', '[page] of [toPage]');

            return $pdf->download($filename.'.pdf');
        }
        else{
            $data =[
                'filterSite'    => $filterSite,
                'filterMrType'  => $filterMrType,
                'sites'         => $sites,
                'piHeaders'     => $piHeaders,
                'site'          => $request->input('site'),
                'mrType'       => $request->input('mr_type'),
                'start_date'    => $request->input('start_date'),
                'end_date'      => $request->input('end_date')
            ];

            return view('documents.purchase_invoices.purchase_invoices_site_pdf_preview')->with($data);
        }
    }

    /**
     * Add Repayment into Purchase Invoice.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function repayment(Request $request)
    {
        try{
            $purchaseInvoice = PurchaseInvoiceHeader::find($request->get('id'));

            //Check Repayment Value
            $repaymentTemp = $purchaseInvoice->repayment_amount;
            $temp = Utilities::toFloat($request->input('payment_add'));
            $totalTemp = $repaymentTemp + $temp;

            if($totalTemp > $purchaseInvoice->total_payment){
                Session::flash('message', 'Angka pelunasan tidak boleh melebihi total pelunasan!');
                return Response::json(array('success' => 'INVALID'));
            }

            $repaymentDouble = (double) $temp;
            $purchaseInvoice->repayment_amount += (double) $repaymentDouble;
            $purchaseInvoice->save();

            //Create History
            $user = \Auth::user();
            $date = $request->get('date');
            $tempDate = strtotime($date);
            $now = date('Y-m-d', $tempDate);
            PurchaseInvoiceRepayment::create([
                'purchase_invoice_header_id'    => $purchaseInvoice->id,
                'repayment_amount'              => $temp,
                'date'                          => $now,
                'created_by'                    => $user->id
            ]);

            Session::flash('message', 'Berhasil menambahkan pelunasan sebesar '. $request->get('repayment_amount') . ' pada purchase invoice '. $purchaseInvoice->code);
            return Response::json(array('success' => 'VALID'));
        }
        catch(\Exception $ex){
            return Response::json(array('errors' => 'INVALID '.$ex));
        }
    }

    /**
     * Edit Repayment into Purchase Invoice.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function repaymentUpdate(Request $request)
    {
        try{
            $repaymentInvoice = PurchaseInvoiceRepayment::find($request->get('id'));
            $purchaseInvoice = PurchaseInvoiceHeader::find($repaymentInvoice->purchase_invoice_header_id);

            // Validate payment value
            $oldRepayment = $repaymentInvoice->repayment_amount;
            $temp = Utilities::toFloat($request->input('payment_edit'));
            $leftPayment = $purchaseInvoice->total_payment - $oldRepayment;

            if(($temp + $leftPayment) > $purchaseInvoice->total_payment){
                Session::flash('message', 'Pelunasan melebihi total Invoice!');
                return Response::json(array('success' => 'INVALID'));
            }

            $user = \Auth::user();
            $date = $request->get('date');
            $tempDate = strtotime($date);
            $now = date('Y-m-d', $tempDate);
            $repaymentInvoice->repayment_amount = $temp;
            $repaymentInvoice->date = $now;
            $repaymentInvoice->updated_by = $user->id;
            $repaymentInvoice->save();

            //Get all Repayment Amount
            $tempTotal = 0;
            $allRepayment = PurchaseInvoiceRepayment::where('purchase_invoice_header_id', $purchaseInvoice->id)->get();
            foreach ($allRepayment as $data){
                $tempTotal += $data->repayment_amount;
            }
            $purchaseInvoice->repayment_amount = $tempTotal;
            $purchaseInvoice->save();

            Session::flash('message', 'Berhasil mengubah pelunasan');
            return Response::json(array('success' => 'VALID'));
        }
        catch(\Exception $ex){
            return Response::json(array('errors' => 'INVALID'));
        }

    }
}