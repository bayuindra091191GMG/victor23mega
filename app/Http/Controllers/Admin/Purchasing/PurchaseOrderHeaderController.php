<?php
/**
 * Created by PhpStorm.
 * User: hellb
 * Date: 2/18/2018
 * Time: 4:45 PM
 */

namespace App\Http\Controllers\Admin\Purchasing;


use App\Exports\PurchaseOrderExport;
use App\Http\Controllers\Controller;
use App\Libs\Utilities;
use App\Mail\ApprovalPurchaseOrderCreated;
use App\Mail\PurchaseOrderMailToCreator;
use App\Mail\PurchaseOrderApprovedMailNotification;
use App\Models\ApprovalPurchaseOrder;
use App\Models\ApprovalRule;
use App\Models\AssignmentPurchaseRequest;
use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use App\Models\Department;
use App\Models\Document;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\NumberingSystem;
use App\Models\PaymentRequestsPiDetail;
use App\Models\PaymentRequestsPoDetail;
use App\Models\PermissionMenu;
use App\Models\PreferenceCompany;
use App\Models\PurchaseInvoiceHeader;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseOrderHeader;
use App\Models\PurchaseRequestHeader;
use App\Models\QuotationHeader;
use App\Models\Status;
use App\Models\Supplier;
use App\Notifications\PurchaseOrderCreated;
use App\Transformer\Purchasing\PurchaseOrderHeaderTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use PDF3;

class PurchaseOrderHeaderController extends Controller
{
    public function index(Request $request){

        $filterStatus = '3';
        if($request->status != null){
            $filterStatus = $request->status;
        }

        $filterApproved = '-1';
        if($request->approved != null){
            $filterApproved = $request->approved;
        }

        $user = \Auth::user();

        // Check menu permission
        $roleId = $user->roles->pluck('id')[0];
        if(!PermissionMenu::where('role_id', $roleId)->where('menu_id', 25)->first()){
            Session::flash('error', 'Level Akses anda tidak mencukupi!');
            return redirect()->back();
        }

        // Custom view permission for Mr Chris
        if($user->id === 39){
            $isPriceViewPermission = false;
        }
        else{
            $isPriceViewPermission = true;
        }

        $data = [
            'filterStatus'          => $filterStatus,
            'filterApproved'        => $filterApproved,
            'isPriceViewPermission' => $isPriceViewPermission
        ];

        return View('admin.purchasing.purchase_orders.index')->with($data);
    }

    public function beforeCreate(){

        return View('admin.purchasing.purchase_orders.before_create');
    }

    public function show(PurchaseOrderHeader $purchase_order){
        $date = Carbon::parse($purchase_order->date)->format('d M Y');

        // Check Approval
        $user = \Auth::user();

        // Check menu permission
        $roleId = $user->roles->pluck('id')[0];
        if(!PermissionMenu::where('role_id', $roleId)->where('menu_id', 25)->first()){
            Session::flash('error', 'Level Akses anda tidak mencukupi!');
            return redirect()->back();
        }

        // Custom view permission for Mr Chris
        if($user->id === 39){
            $isPriceViewPermission = false;
        }
        else{
            $isPriceViewPermission = true;
        }

        $permission = true;
        $isApproving = false;
        $status = 0;

        // Check approval setting
        $setting = PreferenceCompany::find(1);
        $approvals = null;
        $arrData = array();
        $isApproved = $purchase_order->is_approved === 1 ? true : false;
        $isAtLeastOneApproved = false;

        $hardCodedApprovalSetting = 0;

        if($setting->approval_setting == 1) {
            $approvalPos = ApprovalPurchaseOrder::where('purchase_order_id', $purchase_order->id)->get();
            if(!$isApproved){
                $tempApprove = ApprovalRule::where('document_id', 4)->where('user_id', $user->id)->get();
                $approvalRules = ApprovalRule::where('document_id', 4)->get();

                if ($tempApprove->count() != 0) {
                    $isApproving = true;
                }

                $isUserAproved = ApprovalPurchaseOrder::where('purchase_order_id', $purchase_order->id)->where('user_id', $user->id)->exists();
                if($isUserAproved){
                    $isApproving = false;
                }

                // Kondisi Approve Sebagian
                $approvalPoData = ApprovalPurchaseOrder::where('purchase_order_id', $purchase_order->id)->get();
                if(!empty($approvalData) || $approvalPoData->count() > 0){
                    $status = $approvalPoData->count();

                    // Kondisi Semua sudah Approve
                    if($approvalPoData->count() == $approvalRules->count()){
                        $status = 99;
                    }
                }

                if ($approvalRules->count() != $approvalPos->count()) {
                    $permission = false;
                }

                foreach($approvalRules as $approval)
                {
                    $flag = 0;
                    foreach($approvalPos as $approvalPo)
                    {
                        if($approvalPo->user_id == $approval->user_id)
                        {
                            if($approvalPo->status_id == 12){
                                $flag = 1;
                                $isAtLeastOneApproved = true;
                            }
                            else{
                                $flag = 2;
                                // Kondisi ada yang reject
                                $status = 101;
                            }
                        }
                    }

                    if($flag == 1){
                        $arrData[] = "<span style='color: green;'>". $approval->user->name . " - Approved</span>";
                    }
                    elseif($flag == 2){
                        $arrData[] = "<span style='color: red;'>". $approval->user->name . " - Rejected</span>";
                    }
                    else{
                        $arrData[] = "<span style='color: #f4bf42;'>". $approval->user->name . " - Belum Approve</span>";
                    }
                }
            }
            else{
                // Get user approval list
                foreach($approvalPos as $approvalPo)
                {
                    if($approvalPo->status_id == 12){
                        $flag = 1;
                        $isAtLeastOneApproved = true;
                    }
                    else{
                        $flag = 2;
                        // Kondisi ada yang reject
                        $status = 101;
                    }

                    if($flag == 1){
                        $arrData[] = "<span style='color: green;'>". $approvalPo->user->name . " - Approved</span>";
                    }
                    elseif($flag == 2){
                        $arrData[] = "<span style='color: red;'>". $approvalPo->user->name . " - Rejected</span>";
                    }
                    else{
                        $arrData[] = "<span style='color: #f4bf42;'>". $approvalPo->user->name . " - Belum Approve</span>";
                    }
                }
            }
        }

        // Get total discount;
        $individualDiscount = $purchase_order->total_discount ?? 0;
        $extraDiscount = $purchase_order->extra_discount ?? 0;
        $totalDiscount = $individualDiscount + $extraDiscount;

        $totalDiscountStr = number_format($totalDiscount, 2, ",", ".");

        // Get MR URL
        $mrHeader = $purchase_order->purchase_request_header->material_request_header;
        if($mrHeader->type === 1){
            $mrUrl = route('admin.material_requests.other.show', ['material_request' => $mrHeader->id]);
        }
        elseif($mrHeader->type === 2){
            $mrUrl = route('admin.material_requests.fuel.show', ['material_request' => $mrHeader->id]);
        }
        elseif($mrHeader->type === 3){
            $mrUrl = route('admin.material_requests.oil.show', ['material_request' => $mrHeader->id]);
        }
        else{
            $mrUrl = route('admin.material_requests.service.show', ['material_request' => $mrHeader->id]);
        }

        // Get MR Type
        $mrType = $purchase_order->purchase_request_header->material_request_header->type;

        // Get GR & PI Tracking
        $isTrackingAvailable = true;
        $trackedSjHeaders = new Collection();
        if($purchase_order->item_receipt_headers->count() > 0){
            foreach ($purchase_order->item_receipt_headers as $grHeader){
                foreach ($grHeader->delivery_order_headers as $doHeader){
                    $trackedSjHeaders->add($doHeader);
                }
            }
        }

        // Get RFP tracking
        $trackedRFPHeaders = new Collection();
        $paymentReqPoDetails = PaymentRequestsPoDetail::where('purchase_order_id', $purchase_order->id)->get();
        if($paymentReqPoDetails->count() > 0){
            foreach ($paymentReqPoDetails as $paymentReqPoDetail){
                $trackedRFPHeaders->add($paymentReqPoDetail->payment_request);
            }
        }

        $purchaseInvoices = PurchaseInvoiceHeader::where('purchase_order_id', $purchase_order->id)->get();
        if($purchaseInvoices->count() > 0){
            foreach ($purchaseInvoices as $piHeader){
                $paymentReqPiDetails = PaymentRequestsPiDetail::where('purchase_invoice_header_id', $piHeader->id)->get();
                if($paymentReqPiDetails->count() > 0){
                    foreach ($paymentReqPiDetails as $paymentReqPiDetail){
                        $trackedRFPHeaders->add($paymentReqPiDetail->payment_request);
                    }
                }
            }
        }

//        dd($isAtLeastOneApproved);

        $data = [
            'header'            => $purchase_order,
            'date'              => $date,
            'totalDiscountStr'  => $totalDiscountStr,
            'permission'        => $permission,
            'isApproving'       => $isApproving,
            'status'            => $status,
            'approvalData'      => $arrData,
            'setting'           => $setting->approval_setting,
            'mrUrl'             => $mrUrl,
            'mrType'            => $mrType,
            'isTrackingAvailable'       => $isTrackingAvailable,
            'trackedGrHeaders'          => $purchase_order->item_receipt_headers,
            'trackedPiHeaders'          => $purchase_order->purchase_invoice_headers,
            'trackedSjHeaders'          => $trackedSjHeaders,
            'trackedRFPHeaders'         => $trackedRFPHeaders,
            'isApproved'                => $isApproved,
            'isAtLeastOneApproved'      => $isAtLeastOneApproved,
            'isPriceViewPermission'     => $isPriceViewPermission
        ];

        return View('admin.purchasing.purchase_orders.show')->with($data);
    }

    public function create(){
        $purchaseRequest = null;
        $quotation = null;
        if(!empty(request()->rfq)){
            $quotation = QuotationHeader::find(request()->rfq);
            if(empty($quotation)) return redirect()->route('admin.purchase_orders');
        }
        else{
            if(empty(request()->pr)){
                return redirect()->route('admin.purchase_orders.before_create');
            }
            $purchaseRequest = PurchaseRequestHeader::find(request()->pr);
            if(empty($purchaseRequest)) return redirect()->route('admin.purchase_orders.before_create');
        }

        // Validate PR already PO-ed or not
        if($purchaseRequest->is_all_poed === 1){
            return redirect()->route('admin.purchase_orders.before_create');
        }

        // Numbering System
        $user = Auth::user();
        $sysNo = NumberingSystem::where('doc_id', '4')->first();
        $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumberPurchaseOrder($docCode, $sysNo->next_no);

        $data = [
            'purchaseRequest'   => $purchaseRequest,
            'quotation'         => $quotation,
            'autoNumber'        => $autoNumber
        ];

        if($purchaseRequest->material_request_header->type === 4){
            return View('admin.purchasing.purchase_orders.create_service')->with($data);
        }

        return View('admin.purchasing.purchase_orders.create')->with($data);
    }

    public function store(Request $request){
//        dd($request);
        $validator = Validator::make($request->all(),[
            'po_code'       => 'required|max:45|regex:/^\S*$/u',
            'date'          => 'required'
        ],[
            'po_code.regex'     => 'Nomor PO harus tanpa spasi!'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate PO number
        if(!$request->filled('auto_number') && (!$request->filled('po_code') || $request->input('po_code') == "")){
            return redirect()->back()->withErrors('Nomor PO wajib diisi!', 'default')->withInput($request->all());
        }

        // Validate Vendor
        if(!$request->filled('supplier') || $request->input('supplier') === '-1'){
            return redirect()->back()->withErrors('Pilih vendor!', 'default')->withInput($request->all());
        }

        // Get vendor type
        $supplier = Supplier::find($request->input('supplier'));
        if($supplier->type === 'REGULAR'){
            // Validate Quotation for Regular Vendor
            if($request->file('quotation1') == null || $request->file('quotation2') == null || $request->file('quotation3') == null ){
                return redirect()->back()->withErrors('Mohon unggah 3 lampiran PDF penawaran untuk Vendor Tidak Tetap!', 'default')->withInput($request->all());
            }
        }
        else{
            // Validate Quotation for Fixed Vendor
            if($request->file('quotation1') == null){
                return redirect()->back()->withErrors('Mohon unggah lampiran PDF penawaran utama untuk Vendor Tetap!', 'default')->withInput($request->all());
            }
        }

        // Validate details
        $includes = $request->input('include');
        $items = $request->input('item_value');
        $qtys = $request->input('qty');
        $prices = $request->input('price');
        $discounts = $request->input('discount');
        $valid = true;
        $i = 0;
        foreach($includes as $include){
            if($include === 'true'){
                if(empty($items[$i])) $valid = false;
                if(empty($qtys[$i]) || $qtys[$i] == '0') $valid = false;
                if(empty($prices[$i]) || $prices[$i] == '0') $valid = false;

                // Validate discount
                $priceVad = Utilities::toFloat($prices[$i]);
                $discountVad = Utilities::toFloat($discounts[$i]);
                if( $discountVad > ( $priceVad * (double) $qtys[$i])) return redirect()->back()->withErrors('Diskon tidak boleh melebihi harga!', 'default')->withInput($request->all());
            }

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

        // Get PR id
        $prId = $request->input('pr_id');

        // Validate PR relationship
        $validItem = true;
        $validQty = true;
        $i = 0;
        $purchaseRequest = PurchaseRequestHeader::find($prId);
        foreach($includes as $include){
            if($include === 'true'){
                $prDetail = $purchaseRequest->purchase_request_details->where('item_id', $items[$i])->first();
                if(empty($prDetail)){
                    $validItem = false;
                    break;
                }
                else{
                    $qtyResult = $prDetail->quantity - $prDetail->quantity_poed;
                    if($qtys[$i] > $qtyResult){
                        $validQty = false;
                        break;
                    }
                }
            }
            $i++;
        }

        if(!$validItem){
            return redirect()->back()->withErrors('Inventory tidak ada dalam PR!', 'default')->withInput($request->all());
        }
        if(!$validQty){
            return redirect()->back()->withErrors('Kuantitas inventory melebihi kuantitas inventory pada PR!', 'default')->withInput($request->all());
        }

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $doc = Document::find(4);

        // Generate auto number
        if(Input::get('auto_number')){
//            $sysNo = NumberingSystem::where('doc_id', '4')->first();
//            $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
//            $poCode = Utilities::GenerateNumberPurchaseOrder($docCode, $sysNo->next_no);

            $poPrepend = $doc->code. '/'. $now->year;
            $sysNo = Utilities::GetNextAutoNumber($poPrepend);

            $docCode = $doc->code. '-'. $user->employee->site->code;
            $poCode = Utilities::GenerateNumber($docCode, $sysNo);

            // Check existing number
            $temp = PurchaseOrderHeader::where('code', $poCode)->first();
            if(!empty($temp)){
                return redirect()->back()->withErrors('Nomor PO sudah terdaftar!', 'default')->withInput($request->all());
            }

//            $sysNo->next_no++;
//            $sysNo->save();
        }
        else{
            $poCode = $request->input('po_code');

            // Check existing number
            $temp = PurchaseOrderHeader::where('code', $poCode)->first();
            if(!empty($temp)){
                return redirect()->back()->withErrors('Nomor PO sudah terdaftar!', 'default')->withInput($request->all());
            }
        }

        $now = Carbon::now('Asia/Jakarta');

        $poHeader = PurchaseOrderHeader::create([
            'code'                  => $poCode,
            'site_id'               => $user->employee->site_id,
            'purchase_request_id'   => $prId,
            'supplier_id'           => $request->input('supplier'),
            'is_all_received'       => 0,
            'is_all_invoiced'       => 0,
            'is_retur'              => 0,
            'is_approved'           => 0,
            'status_id'             => 3,
            'created_by'            => $user->id,
            'created_at'            => $now->toDateTimeString(),
            'warehouse_id'          => $purchaseRequest->warehouse_id,
            'is_reorder'            => $purchaseRequest->is_reorder
        ]);

        $extraDiscount = 0;
        if($request->filled('extra_discount')){
            $extraDiscount = Utilities::toFloat($request->input('extra_discount'));
            $poHeader->extra_discount = $extraDiscount;
        }

        $delivery = 0;
        if($request->filled('delivery_fee') && $request->input('delivery_fee') != '0'){
            $delivery = Utilities::toFloat($request->input('delivery_fee'));
            $poHeader->delivery_fee = $delivery;
        }

        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');
        $poHeader->date = $date->toDateTimeString();

        if($request->filled('payment_term')){
            $poHeader->payment_term = $request->input('payment_term');
        }

        if($request->filled('special_note')){
            $poHeader->special_note = $request->input('special_note');
        }

        // Check uploaded quotation pdf
        $folderPath = 'assets/documents/po';
        $poCodeFiltered = str_replace('/', '_', $poHeader->code);
        if($request->file('quotation1') != null){
            $name = 'QUOTATION_VENDOR_1_'. $poCodeFiltered. '_'. $now->format('Ymdhms'). '.pdf';

            $path = $request->file('quotation1')->storeAs(
                $folderPath, $name, 'public_uploads'
            );

            $poHeader->quotation_pdf_path_1 = $folderPath. '/'. $name;
        }

        if($request->file('quotation2') != null){
            $name = 'QUOTATION_VENDOR_2_'. $poCodeFiltered. '_'. $now->format('Ymdhms'). '.pdf';

            $path = $request->file('quotation2')->storeAs(
                $folderPath, $name, 'public_uploads'
            );

            $poHeader->quotation_pdf_path_2 = $folderPath. '/'. $name;
        }

        if($request->file('quotation3') != null){
            $name = 'QUOTATION_VENDOR_3_'. $poCodeFiltered. '_'. $now->format('Ymdhms'). '.pdf';

            $path = $request->file('quotation3')->storeAs(
                $folderPath, $name, 'public_uploads'
            );

            $poHeader->quotation_pdf_path_3 = $folderPath. '/'. $name;
        }

        $poHeader->save();

        // Create po detail
        $totalPrice = 0;
        $totalDiscount = 0;
        $totalPayment = 0;
        $remarks = $request->input('remark');
        $idx = 0;

        foreach($includes as $include){
            if($include === 'true'){
                $price = Utilities::toFloat($prices[$idx]);
                $qty = (double) $qtys[$idx];
                $poDetail = PurchaseOrderDetail::create([
                    'header_id'         => $poHeader->id,
                    'item_id'           => $items[$idx],
                    'quantity'          => $qty,
                    'received_quantity' => 0,
                    'quantity_invoiced' => 0,
                    'quantity_retur'    => 0,
                    'price'             => $price
                ]);

                // Check discount
                if(!empty($discounts[$idx]) && $discounts[$idx] !== '0'){
                    $discount = Utilities::toFloat($discounts[$idx]);

                    $poDetail->discount = $discount;
                    $poDetail->subtotal = ($qty * $price) - $discount;

                    // Accumulate total price
                    $totalPrice += $qty * $price;

                    // Accumulate total discount
                    $totalDiscount += $discount;
                }
                else{
                    $poDetail->subtotal = $qty * $price;
                    $totalPrice += $qty * $price;
                }

                if(!empty($remarks[$idx])) $poDetail->remark = $remarks[$idx];
                $poDetail->save();

                // Flagging quantity poed on PR details
                $prDetail = $purchaseRequest->purchase_request_details->where('item_id', $items[$idx])->first();
                if($prDetail->quantity_poed < $prDetail->quantity){
                    $prDetail->quantity_poed += $qty;
                    $prDetail->save();
                }

                // Add to stock on order
//                $itemDb = Item::find($items[$idx]);
//                $itemDb->stock_on_order += $qty;
//                $itemDb->save();

                // Accumulate subtotal
                $totalPayment += $poDetail->subtotal;
            }
            $idx++;
        }

        if($totalDiscount > 0) $poHeader->total_discount = $totalDiscount;
        $poHeader->total_price = $totalPrice;

        // Save total payment without tax
        $totalPayment -= $extraDiscount;
        $poHeader->total_payment_before_tax = $totalPayment;

        // Get PPN & PPh
        $ppnAmount = 0;
        if($request->filled('ppn') && $request->input('ppn') != '0'){
            $ppnAmount = $totalPayment * (10 / 100);
            $poHeader->ppn_percent = 10;
            $poHeader->ppn_amount = $ppnAmount;
        }

        $pphAmount = 0;
        if($request->filled('pph') && $request->input('pph') != '0'){
            $pphAmount = Utilities::toFloat($request->input('pph'));
            $poHeader->pph_amount = $pphAmount;
        }

        $poHeader->total_payment = $totalPayment + $delivery + $ppnAmount - $pphAmount;
        $poHeader->save();

        // Check if all po-ed or not on related PR
        $allPoed = true;
        foreach($purchaseRequest->purchase_request_details as $prDetail2){
            if($prDetail2->quantity_poed < $prDetail2->quantity){
                $allPoed = false;
            }
        }

        if($allPoed){
            $purchaseRequest->is_all_poed = 1;
            $purchaseRequest->save();
        }
        else{
            $purchaseRequest->is_all_poed = 2;
            $purchaseRequest->save();
        }

        // Increase autonumber
        if($request->filled('auto_number')){
            $poPrepend = $doc->code. '/'. $now->year;
            Utilities::UpdateAutoNumber($poPrepend);
        }

        // Check Approval Feature
        $environment = env('APP_ENV','local');
        $preference = PreferenceCompany::find(1);
        $isApproval = true;
        $approvals = ApprovalRule::where('document_id', 4)->get();

        $hardCodedApprovalSetting = 0;

        try{
            if($preference->approval_setting == 1) {
                if($approvals->count() > 0){
                    foreach($approvals as $approval){
                        if(!empty($approval->user->email_address)){
                            if($environment === 'prod'){
                                for ($try = 0; $try < 3; $try++){
                                    try{
                                        Mail::to($approval->user->email_address)->send(new ApprovalPurchaseOrderCreated($poHeader, $approval->user));
                                        Log::info($approval->user->email_address. ' Send Approval Purchase Order '. $poHeader->code);
                                        break 1;
                                    }
                                    catch (\Exception $ex){
                                        Log::error('PurchaseOrderHeaderController - store : '. $ex);
                                    }
                                }
                            }
                            else{
                                Mail::to('hellbardx2@gmail.com')->send(new ApprovalPurchaseOrderCreated($poHeader, $approval->user));
                            }
                        }
                    }
                }
                else{
                    $isApproval = false;
                }
            }
            else{
                $isApproval = false;
            }
        }
        catch (\Exception $ex){
            error_log($ex);
        }

        if(!$isApproval){
            try{
                // Auto approved PO
                if($approvals->count() > 0){
                    foreach($approvals as $approval){
                        ApprovalPurchaseOrder::create([
                            'purchase_order_id'   => $poHeader->id,
                            'user_id'             => $approval->user_id,
                            'status_id'           => 12,
                            'created_at'          => $now->toDateTimeString(),
                            'updated_at'          => $now->toDateTimeString(),
                            'created_by'          => $user->id,
                            'updated_by'          => $user->id
                        ]);
                    }
                }

                $poHeader->is_approved = 1;
                $poHeader->approved_date = $now->toDateTimeString();
                $poHeader->save();

                // Mail to PO creator
                Mail::to($poHeader->createdBy->email_address)->send(new PurchaseOrderMailToCreator($poHeader, $poHeader->createdBy));

                if($environment === 'prod'){
                    $logisticUser1 = User::find(26);
                    $logisticUser2 = User::find(28);
                    Mail::to($logisticUser1->email_address)->send(new PurchaseOrderApprovedMailNotification($poHeader, $poHeader->createdBy));
                    Mail::to($logisticUser2->email_address)->send(new PurchaseOrderApprovedMailNotification($poHeader, $poHeader->createdBy));

                    // Ginanjar
                    $purchasingUser4 = User::find(16);
                    Mail::to($purchasingUser4->email_address)->send(new PurchaseOrderApprovedMailNotification($poHeader, $purchasingUser4));

                    // Petrus
                    $purchasingUser2 = User::find(25);
                    Mail::to($purchasingUser2->email_address)->send(new PurchaseOrderApprovedMailNotification($poHeader, $purchasingUser2));

                    // Lena
                    $purchasingUser1 = User::find(27);
                    Mail::to($purchasingUser1->email_address)->send(new PurchaseOrderApprovedMailNotification($poHeader, $purchasingUser1));

                    // Irene
                    $purchasingUser5 = User::find(40);
                    Mail::to($purchasingUser5->email_address)->send(new PurchaseOrderApprovedMailNotification($poHeader, $purchasingUser5));

                    // Karina
                    $purchasingUser3 = User::find(47);
                    Mail::to($purchasingUser3->email_address)->send(new PurchaseOrderApprovedMailNotification($poHeader, $purchasingUser3));
                }

                // Send notification
                $poCreator = $poHeader->createdBy;
                $poCreator->notify(new PurchaseOrderCreated($poHeader, 'false', 'true'));

                $mrCreator = $poHeader->purchase_request_header->material_request_header->createdBy;
                $mrCreator->notify(new PurchaseOrderCreated($poHeader, 'true', 'false'));

                $roles = Role::where('id', 13)->get();
                foreach($roles as $role){
                    $users =  $role->users()->get();
                    if($users->count() > 0){
                        foreach ($users as $notifiedUser){
                            if($notifiedUser->id !== $mrCreator->id){
                                $notifiedUser->notify(new PurchaseOrderCreated($poHeader, 'false', 'false'));
                            }
                        }
                    }
                }
            }
            catch(\Exception $ex){
                error_log($ex);
            }
        }

        // Check assignment
        $assignmentPr = AssignmentPurchaseRequest::where('purchase_request_id', $purchaseRequest->id)
            ->where('status_id', 17)
            ->first();

        if(!empty($assignmentPr) && $allPoed){
            $assignmentPr->status_id = 18;
            $assignmentPr->processed_by = $user->id;
            $assignmentPr->processed_date = $now->toDateTimeString();

            if($user->id != $assignmentPr->assigned_user_id){
                $assignmentPr->is_different_processor = 1;
            }

            $assignmentPr->save();
        }

        // Update processed by for assignment
        if($allPoed){
            $purchaseRequest->processed_by = $user->id;
            $purchaseRequest->all_poed_processed_date = $now->toDateTimeString();
            $purchaseRequest->save();
        }

        Session::flash('message', 'Berhasil membuat Purchase Order!');

        return redirect()->route('admin.purchase_orders.show', ['purchase_order' => $poHeader]);
    }

    public function storeService(Request $request){
//        dd($request);
        $validator = Validator::make($request->all(),[
            'po_code'       => 'required|max:45|regex:/^\S*$/u',
            'date'          => 'required'
        ],[
            'po_code.regex'     => 'Nomor PO harus tanpa spasi!'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate PO number
        if(!$request->filled('auto_number') && (!$request->filled('po_code') || $request->input('po_code') == "")){
            return redirect()->back()->withErrors('Nomor PO wajib diisi!', 'default')->withInput($request->all());
        }

        // Validate Vendor
        if(!$request->filled('supplier') || $request->input('supplier') === '-1'){
            return redirect()->back()->withErrors('Pilih vendor!', 'default')->withInput($request->all());
        }

        // Validate Quotation Vendor
        if($request->file('quotation1') == null){
            return redirect()->back()->withErrors('Mohon unggah lampiran utama pdf quotation vendor!', 'default')->withInput($request->all());
        }

        // Validate Service Price
        if(!$request->filled('service_price') || $request->input('service_price') === "0"){
            return redirect()->back()->withErrors('Harga servis wajib diisi!', 'default')->withInput($request->all());
        }

        // Get PR id
        $prId = $request->input('pr_id');

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $doc = Document::find(4);

        // Generate auto number
        if(Input::get('auto_number')){
//            $sysNo = NumberingSystem::where('doc_id', '4')->first();
//            $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
//            $poCode = Utilities::GenerateNumberPurchaseOrder($docCode, $sysNo->next_no);

            $poPrepend = $doc->code. '/'. $now->year;
            $sysNo = Utilities::GetNextAutoNumber($poPrepend);

            $docCode = $doc->code. '-'. $user->employee->site->code;
            $poCode = Utilities::GenerateNumber($docCode, $sysNo);

            // Check existing number
            $temp = PurchaseOrderHeader::where('code', $poCode)->first();
            if(!empty($temp)){
                return redirect()->back()->withErrors('Nomor PO sudah terdaftar!', 'default')->withInput($request->all());
            }

//            $sysNo->next_no++;
//            $sysNo->save();
        }
        else{
            $poCode = $request->input('po_code');

            // Check existing number
            $temp = PurchaseOrderHeader::where('code', $poCode)->first();
            if(!empty($temp)){
                return redirect()->back()->withErrors('Nomor PO sudah terdaftar!', 'default')->withInput($request->all());
            }
        }

        $now = Carbon::now('Asia/Jakarta');

        $poHeader = PurchaseOrderHeader::create([
            'code'                  => $poCode,
            'site_id'               => $user->employee->site_id,
            'purchase_request_id'   => $prId,
            'supplier_id'           => $request->input('supplier'),
            'is_all_received'       => 1,
            'is_all_invoiced'       => 0,
            'is_retur'              => 0,
            'is_approved'           => 0,
            'status_id'             => 3,
            'created_by'            => $user->id,
            'created_at'            => $now->toDateTimeString()
        ]);

        $extraDiscount = 0;
        if($request->filled('extra_discount')){
            $extraDiscount = Utilities::toFloat($request->input('extra_discount'));
            $poHeader->extra_discount = $extraDiscount;
        }

        $delivery = 0;
        if($request->filled('delivery_fee') && $request->input('delivery_fee') != '0'){
            $delivery = Utilities::toFloat($request->input('delivery_fee'));
            $poHeader->delivery_fee = $delivery;
        }

        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');
        $poHeader->date = $date->toDateTimeString();

        if($request->filled('payment_term')){
            $poHeader->payment_term = $request->input('payment_term');
        }

        if($request->filled('special_note')){
            $poHeader->special_note = $request->input('special_note');
        }

        // Check uploaded quotation pdf
        $folderPath = 'assets/documents/po';
        $poCodeFiltered = str_replace('/', '_', $poHeader->code);
        if($request->file('quotation1') != null){
            $name = 'QUOTATION_VENDOR_1_'. $poCodeFiltered. '_'. $now->format('Ymdhms'). '.pdf';

            $path = $request->file('quotation1')->storeAs(
                $folderPath, $name, 'public_uploads'
            );

            $poHeader->quotation_pdf_path_1 = $folderPath. '/'. $name;
        }

        if($request->file('quotation2') != null){
            $name = 'QUOTATION_VENDOR_2_'. $poCodeFiltered. '_'. $now->format('Ymdhms'). '.pdf';

            $path = $request->file('quotation2')->storeAs(
                $folderPath, $name, 'public_uploads'
            );

            $poHeader->quotation_pdf_path_2 = $folderPath. '/'. $name;
        }

        if($request->file('quotation3') != null){
            $name = 'QUOTATION_VENDOR_3_'. $poCodeFiltered. '_'. $now->format('Ymdhms'). '.pdf';

            $path = $request->file('quotation3')->storeAs(
                $folderPath, $name, 'public_uploads'
            );

            $poHeader->quotation_pdf_path_3 = $folderPath. '/'. $name;
        }

        $poHeader->save();

        // Create po detail
        $prHeader = PurchaseRequestHeader::find($prId);
        $totalPrice = 0;
        $totalDiscount = 0;
        $totalPayment = 0;
        $idx = 0;

        $price = Utilities::toFloat($request->input('service_price'));
        $totalPayment = $price;
        $totalPrice = $price;
        $poDetail = PurchaseOrderDetail::create([
            'header_id'         => $poHeader->id,
            'item_id'           => 0,
            'quantity'          => 1,
            'received_quantity' => 1,
            'quantity_invoiced' => 0,
            'quantity_retur'    => 0,
            'price'             => $price,
            'discount'          => 0,
            'subtotal'          => $price,
            'remark'            => $prHeader->purchase_request_details->first()->remark
        ]);

        // Flagging quantity poed on PR details
        $prDetail = $prHeader->purchase_request_details->first();
        if($prDetail->quantity_poed === 0){
            $prDetail->quantity_poed = 1;
            $prDetail->save();

            $prHeader->is_all_poed = 1;
            $prHeader->save();
        }

        if($totalDiscount > 0) $poHeader->total_discount = $totalDiscount;
        $poHeader->total_price = $totalPrice;

        // Save total payment without tax
        $totalPayment -= $extraDiscount;
        $poHeader->total_payment_before_tax = $totalPayment;

        // Get PPN & PPh
        $ppnAmount = 0;
        if($request->filled('ppn') && $request->input('ppn') != '0'){
            $ppnAmount = $totalPayment * (10 / 100);
            $poHeader->ppn_percent = 10;
            $poHeader->ppn_amount = $ppnAmount;
        }

        $pphAmount = 0;
        if($request->filled('pph') && $request->input('pph') != '0'){
            $pphAmount = Utilities::toFloat($request->input('pph'));
            $poHeader->pph_amount = $pphAmount;
        }

        $poHeader->total_payment = $totalPayment + $delivery + $ppnAmount - $pphAmount;
        $poHeader->save();

        // Increase autonumber
        if($request->filled('auto_number')){
            $poPrepend = $doc->code. '/'. $now->year;
            Utilities::UpdateAutoNumber($poPrepend);
        }

        // Check Approval Feature
        $environment = env('APP_ENV','local');
        $preference = PreferenceCompany::find(1);
        $isApproval = true;
        $approvals = ApprovalRule::where('document_id', 4)->get();

        $hardCodedApprovalSetting = 0;

        try{
            if($preference->approval_setting == 1) {
                if($approvals->count() > 0){
                    foreach($approvals as $approval){
                        if(!empty($approval->user->email_address)){
                            if($environment === 'prod'){
                                for ($try = 0; $try < 3; $try++){
                                    try{
                                        Mail::to($approval->user->email_address)->send(new ApprovalPurchaseOrderCreated($poHeader, $approval->user));
                                        break 1;
                                    }
                                    catch (\Exception $ex){
                                        Log::error('PurchaseOrderHeaderController - store : '. $ex);
                                    }
                                }
                            }
                            else{
                                Mail::to('hellbardx2@gmail.com')->send(new ApprovalPurchaseOrderCreated($poHeader, $approval->user));
                            }
                        }
                    }
                }
                else{
                    $isApproval = false;
                }
            }
            else{
                $isApproval = false;
            }
        }
        catch (\Exception $ex){
            error_log($ex);
        }

        if(!$isApproval){
            try{
                // Auto approved PO
                if($approvals->count() > 0){
                    foreach($approvals as $approval){
                        ApprovalPurchaseOrder::create([
                            'purchase_order_id'   => $poHeader->id,
                            'user_id'             => $approval->user_id,
                            'status_id'           => 12,
                            'created_at'          => $now->toDateTimeString(),
                            'updated_at'          => $now->toDateTimeString(),
                            'created_by'          => $user->id,
                            'updated_by'          => $user->id
                        ]);
                    }
                }

                $poHeader->is_approved = 1;
                $poHeader->approved_date = $now->toDateTimeString();
                $poHeader->save();

                // Mail to PO creator
                Mail::to($poHeader->createdBy->email_address)->send(new PurchaseOrderMailToCreator($poHeader, $poHeader->createdBy));

                if($environment === 'prod'){
                    $logisticUser1 = User::find(26);
                    $logisticUser2 = User::find(28);
                    Mail::to($logisticUser1->email_address)->send(new PurchaseOrderApprovedMailNotification($poHeader, $poHeader->createdBy));
                    Mail::to($logisticUser2->email_address)->send(new PurchaseOrderApprovedMailNotification($poHeader, $poHeader->createdBy));

                    $purchasingUser1 = User::find(27);
                    Mail::to($purchasingUser1->email_address)->send(new PurchaseOrderApprovedMailNotification($poHeader, $poHeader->createdBy));

                    $purchasingUser2 = User::find(25);
                    Mail::to($purchasingUser2->email_address)->send(new PurchaseOrderApprovedMailNotification($poHeader, $poHeader->createdBy));

                    $purchasingUser3 = User::find(47);
                    Mail::to($purchasingUser3->email_address)->send(new PurchaseOrderApprovedMailNotification($poHeader, $poHeader->createdBy));

                    $purchasingUser4 = User::find(16);
                    Mail::to($purchasingUser4->email_address)->send(new PurchaseOrderApprovedMailNotification($poHeader, $poHeader->createdBy));

                    $purchasingUser5 = User::find(40);
                    Mail::to($purchasingUser5->email_address)->send(new PurchaseOrderApprovedMailNotification($poHeader, $poHeader->createdBy));
                }

                // Send notification
                $poCreator = $poHeader->createdBy;
                $poCreator->notify(new PurchaseOrderCreated($poHeader, 'false', 'true'));

                $mrCreator = $poHeader->purchase_request_header->material_request_header->createdBy;
                $mrCreator->notify(new PurchaseOrderCreated($poHeader, 'true', 'false'));

                $roles = Role::where('id', 13)->get();
                foreach($roles as $role){
                    $users =  $role->users()->get();
                    if($users->count() > 0){
                        foreach ($users as $notifiedUser){
                            if($notifiedUser->id !== $mrCreator->id){
                                $notifiedUser->notify(new PurchaseOrderCreated($poHeader, 'false', 'false'));
                            }
                        }
                    }
                }
            }
            catch(\Exception $ex){
                error_log($ex);
            }
        }

        Session::flash('message', 'Berhasil membuat Purchase Order Service!');

        return redirect()->route('admin.purchase_orders.show', ['purchase_order' => $poHeader]);
    }

    public function edit(PurchaseOrderHeader $purchase_order){
        $date = Carbon::parse($purchase_order->date)->format('d M Y');

        $pdfUrl1 = null;
        if(!empty($purchase_order->quotation_pdf_path_1)){
            $pdfUrl1 = public_path(). '/'. $purchase_order->quotation_pdf_path_1;
        }

        $pdfUrl2 = null;
        if(!empty($purchase_order->quotation_pdf_path_2)){
            $pdfUrl2 = public_path(). '/'. $purchase_order->quotation_pdf_path_2;
        }

        $pdfUrl3 = null;
        if(!empty($purchase_order->quotation_pdf_path_3)){
            $pdfUrl3 = public_path(). '/'. $purchase_order->quotation_pdf_path_3;
        }

        $data = [
            'header'    => $purchase_order,
            'date'      => $date,
            'pdfUrl1'   => $pdfUrl1,
            'pdfUrl2'   => $pdfUrl2,
            'pdfUrl3'   => $pdfUrl3
        ];

        // Check if MR type is Service
        if($purchase_order->purchase_request_header->material_request_header->type === 4){
            return View('admin.purchasing.purchase_orders.edit_service')->with($data);
        }

        return View('admin.purchasing.purchase_orders.edit')->with($data);
    }

    public function update(Request $request, PurchaseOrderHeader $purchase_order){
        $validator = Validator::make($request->all(),[
            'date'          => 'required',
            'payment_term'  => 'max:50',
            'special_note'  => 'max:200'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check MR type
        $mrHeader = $purchase_order->purchase_request_header->material_request_header;
        if($mrHeader->type === 4){
            // Validate Service Price
            if(!$request->filled('service_price') || $request->input('service_price') === "0"){
                return redirect()->back()->withErrors('Harga servis wajib diisi!', 'default')->withInput($request->all());
            }
        }

        if($request->filled('supplier')) $purchase_order->supplier_id = Input::get('supplier');

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');

        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');
        $purchase_order->date = $date->toDateTimeString();

        $purchase_order->payment_term = $request->filled('payment_term') ? $request->input('payment_term') : null;
        $purchase_order->special_note = $request->filled('special_note') ? $request->input('special_note') : null;
        $purchase_order->updated_by = $user->id;
        $purchase_order->updated_at = $now->toDateTimeString();

        $totalPaymentWithoutTax = $purchase_order->total_payment_before_tax;

        // Check if MR type is Service
        if($mrHeader->type === 4){
            $servicePrice = Utilities::toFloat($request->input('service_price'));
            $poDetail = $purchase_order->purchase_order_details->first();

            if($servicePrice != $poDetail->price){
                $oldServicePrice = $poDetail->price;

                $poDetail->price = $servicePrice;
                $poDetail->subtotal = $servicePrice;
                $poDetail->save();

                $purchase_order->total_price = $servicePrice;

                $totalPaymentWithoutTax = $totalPaymentWithoutTax - $oldServicePrice + $servicePrice;
            }
        }

        $newDelivery = 0;
        if($request->filled('delivery_fee') && $request->input('delivery_fee') != '0'){
            $newDelivery = Utilities::toFloat($request->input('delivery_fee'));
            $purchase_order->delivery_fee = $newDelivery;
        }
        else{
            $purchase_order->delivery_fee = null;
        }

        $oldExtraDiscount = $purchase_order->extra_discount ?? 0;
        $newExtraDiscount = 0;
        if($request->filled('extra_discount') && $request->input('extra_discount') != '0'){
            $newExtraDiscount = Utilities::toFloat($request->input('extra_discount'));
            $purchase_order->extra_discount = $newExtraDiscount;
        }
        else{
            $purchase_order->extra_discount = null;
        }

        $totalPayment = $totalPaymentWithoutTax + $oldExtraDiscount - $newExtraDiscount;
        $purchase_order->total_payment_before_tax = $totalPayment;

        // Get PPN & PPh
        $ppnAmount = 0;
        if($request->filled('ppn')){
            $ppnAmount = $totalPayment * (10 / 100);
            $purchase_order->ppn_percent = 10;
            $purchase_order->ppn_amount = $ppnAmount;
        }
        else{
            $purchase_order->ppn_percent = null;
            $purchase_order->ppn_amount = null;
        }

        $pphAmount = 0;
        if($request->filled('pph')){
            $pphAmount = Utilities::toFloat($request->input('pph'));
            $purchase_order->pph_amount = $pphAmount;
        }
        else{
            $purchase_order->pph_percent = null;
            $purchase_order->pph_amount = null;
        }

        $purchase_order->total_payment = $totalPayment + $newDelivery + $ppnAmount - $pphAmount;

        // Check uploaded quotation pdf
        $folderPath = 'assets/documents/po';
        $poCodeFiltered = str_replace('/', '_', $purchase_order->code);
        if($request->file('quotation1') != null){
            if(!empty($purchase_order->quotation_pdf_path_1)){

                // Delete old pdf
                $deletedPath1 = public_path($purchase_order->quotation_pdf_path_1);
                if(file_exists($deletedPath1)) unlink($deletedPath1);
            }

            $name = 'QUOTATION_VENDOR_1_'. $poCodeFiltered. '_'. $now->format('Ymdhms'). '.pdf';

            $path = $request->file('quotation1')->storeAs(
                $folderPath, $name, 'public_uploads'
            );

            $purchase_order->quotation_pdf_path_1 = $folderPath. '/'. $name;
        }

        if($request->file('quotation2') != null){
            if(!empty($purchase_order->quotation_pdf_path_2)){

                // Delete old pdf
                $deletedPath2 = public_path($purchase_order->quotation_pdf_path_2);
                if(file_exists($deletedPath2)) unlink($deletedPath2);
            }

            $name = 'QUOTATION_VENDOR_2_'. $poCodeFiltered. '_'. $now->format('Ymdhms'). '.pdf';

            $path = $request->file('quotation2')->storeAs(
                $folderPath, $name, 'public_uploads'
            );

            $purchase_order->quotation_pdf_path_2 = $folderPath. '/'. $name;
        }

        if($request->file('quotation3') != null){
            if(!empty($purchase_order->quotation_pdf_path_3)){

                // Delete old pdf
                $deletedPath3 = public_path($purchase_order->quotation_pdf_path_3);
                if(file_exists($deletedPath3)) unlink($deletedPath3);
            }

            $name = 'QUOTATION_VENDOR_3_'. $poCodeFiltered. '_'. $now->format('Ymdhms'). '.pdf';

            $path = $request->file('quotation3')->storeAs(
                $folderPath, $name, 'public_uploads'
            );

            $purchase_order->quotation_pdf_path_3 = $folderPath. '/'. $name;
        }


        $purchase_order->save();

        Session::flash('message', 'Berhasil ubah purchase order!');

        return redirect()->route('admin.purchase_orders.show', ['purchase_order' => $purchase_order]);
    }

    public function close(Request $request){
        try{
            $user = Auth::user();
            $now = Carbon::now('Asia/Jakarta');

            $purchaseOrder = PurchaseOrderHeader::find($request->input('id'));
            $purchaseOrder->closed_by = $user->id;
            $purchaseOrder->closing_date = $now->toDateTimeString();
            $purchaseOrder->close_reason = $request->input('reason');
            $purchaseOrder->status_id = 11;
            $purchaseOrder->save();

            $prHeader = $purchaseOrder->purchase_request_header;

            $isPoCancel = false;
            if($purchaseOrder->is_all_received !== 1 && $purchaseOrder->is_approved === 1){
                foreach ($purchaseOrder->purchase_order_details as $poDetail){
                    if($poDetail->received_quantity < $poDetail->quantity){
                        $qtyClosed = $poDetail->quantity - $poDetail->received_quantity;

                        $item = Item::find($poDetail->item_id);
                        // Recount stock on order
                        if($item->stock_on_order >= $qtyClosed){
                            $item->stock_on_order -= $qtyClosed;
                            $item->save();

                            if(!empty($purchaseOrder->warehouse_id)){
                                $itemStock = ItemStock::where('warehouse_id', $purchaseOrder->warehouse_id)
                                    ->where('item_id', $poDetail->item_id)
                                    ->first();

                                if(!empty($itemStock)){
                                    $itemStock->stock_on_order -= $qtyClosed;
                                    $itemStock->save();
                                }
                            }

                        }

                        // Recount PR quantity poed
                        $prDetail = $prHeader->purchase_request_details->where('item_id', $item->id)->first();
                        $prDetail->quantity_poed -= $qtyClosed;
                        $prDetail->save();

                        $isPoCancel = true;
                    }
                }

                // Undo reorder process
                if($purchaseOrder->is_reorder === 1){
                    foreach ($purchaseOrder->purchase_order_details as $detail){
                        $itemStock = ItemStock::where('warehouse_id', $purchaseOrder->warehouse_id)
                            ->where('item_id', $detail->item_id)
                            ->first();
                        $itemStock->stock_on_reorder -= $detail->quantity;
                        $itemStock->save();
                    }
                }
            }

            if($purchaseOrder->is_all_received !== 1 && $purchaseOrder->is_approved === 0){
                foreach ($purchaseOrder->purchase_order_details as $poDetail){
                    if($poDetail->received_quantity < $poDetail->quantity){
                        $qtyClosed = $poDetail->quantity - $poDetail->received_quantity;
                        $item = Item::find($poDetail->item_id);

                        // Recount PR quantity poed
                        $prDetail = $prHeader->purchase_request_details->where('item_id', $item->id)->first();
                        $prDetail->quantity_poed -= $qtyClosed;
                        $prDetail->save();

                        $isPoCancel = true;
                    }
                }
            }

            if($prHeader->is_all_poed === 1 && $isPoCancel){
                $prHeader->is_all_poed = 0;
                $prHeader->save();
            }

            Session::flash('message', 'Berhasil tutup PO!');

            return Response::json(array('success' => 'VALID'));
        }
        catch(\Exception $ex){
            return Response::json(array('errors' => 'INVALID'));
        }
    }

    public function getIndex(Request $request){
        try{
            $purchaseOrders = null;

            $status = '0';
            if($request->filled('status')){
                $status = $request->input('status');
                if($status != '0'){
                    $purchaseOrders = PurchaseOrderHeader::where('status_id', $status);
                }
                else{
                    $purchaseOrders = PurchaseOrderHeader::whereIn('status_id', [3,4,11,13]);
                }
            }
            else{
                $purchaseOrders = PurchaseOrderHeader::where('status_id', 3);
            }

            $mode = 'default';
            if($request->filled('mode')){
                $mode = $request->input('mode');

                if($mode == 'before_create_rfp'){
                    $purchaseOrders = PurchaseOrderHeader::where('is_approved', 1);
                }
                elseif($mode == 'before_create_gr'){
                    $purchaseOrders = $purchaseOrders->where('status_id', 3)
                        ->where('is_approved', 1)
                        ->whereIn('is_all_received', [0,2]);
                }
                elseif($mode == 'before_create_pi'){
                    $purchaseOrders = $purchaseOrders->where('status_id', 3)
                        ->where('is_approved', 1)
                        ->whereIn('is_all_received', [1,2]);
                }
            }

            if($mode === 'default'){
                // Filter approval status
                $approved = $request->approved;
                if($approved !== '-1'){
                    $purchaseOrders = $purchaseOrders->where('is_approved', $approved);
                }
            }

            return DataTables::of($purchaseOrders)
                ->setTransformer(new PurchaseOrderHeaderTransformer($mode))
                ->make(true);
        }
        catch(\Exception $ex){
            error_log($ex);
        }
    }

    public function getPurchaseOrders(Request $request){
        $term = trim($request->q);

        if(!empty($request->supplier)){
            $supplierId = $request->supplier;
            $purchaseOrders = PurchaseOrderHeader::where('supplier_id', $supplierId)
                ->where('code', 'LIKE', '%'. $term. '%')
                ->get();
        }
        else{
            $purchaseOrders = PurchaseOrderHeader::where('status_id', 3)
                ->where('code', 'LIKE', '%'. $term. '%')
                ->get();
        }

        $formatted_tags = [];

        foreach ($purchaseOrders as $purchaseOrder) {
            $formatted_tags[] = ['id' => $purchaseOrder->id, 'text' => $purchaseOrder->code];
        }

        return \Response::json($formatted_tags);
    }

    public function report(){
        $departments = Department::all();

        return View('admin.purchasing.purchase_orders.report', compact('departments'));
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

        if($request->input('is_excel') === 'true'){
            $nowExcel = Carbon::now('Asia/Jakarta');
            $filenameExcel = 'PURCHASE_ORDER_REPORT_' . $nowExcel->toDateTimeString(). '.xlsx';

            return (new PurchaseOrderExport(
                $start->toDateTimeString(),
                $end->toDateTimeString(),
                (int) $request->input('department'),
                (int) $request->input('status'),
                $request->filled('user') ? (int) $request->input('user') : -1
            ))->download($filenameExcel);
        }

        $start = $start->addDays(-1);
        $end = $end->addDays(1);

        $poHeaders = PurchaseOrderHeader::whereBetween('date', array($start->toDateTimeString(), $end->toDateTimeString()));

        // Filter departemen
        $filterDepartment = 'Semua';
        $department = $request->input('department');
        if($department != '0'){
//            $poHeaders = $poHeaders->where('department_id', $department);
            $poHeaders = $poHeaders->whereHas('purchase_request_header', function($query) use($department) {
                $query->where('department_id', $department);
                }
            );
            $filterDepartment = Department::find($department)->name;
        }

        // Filter status
        $filterStatus = 'Semua';
        $status = $request->input('status');
        if($status != '0'){
            $poHeaders = $poHeaders->where('status_id', $status);
            $filterStatus = Status::find($status)->description;
        }

        $poHeaders = $poHeaders->orderByDesc('date')
            ->get();

        // Filter created by
        $filterUser = 'Semua';
        if(!empty($request->input('user'))){
            $user = User::find($request->input('user'));
            $poHeaders = $poHeaders->where('created_by', $user->id);
            $filterUser = $user->email;
        }

        // Validate Data
        if($poHeaders->count() == 0){
            return redirect()->back()->withErrors('Data tidak ditemukan!', 'default')->withInput($request->all());
        }

        $total = $poHeaders->sum('total_payment');
        $totalStr = number_format($total, 2, ",", ".");

        if($request->input('is_preview') === 'false'){
            $data =[
                'poHeaders'         => $poHeaders,
                'start_date'        => $request->input('start_date'),
                'end_date'          => $request->input('end_date'),
                'filterDepartment'  => $filterDepartment,
                'filterStatus'      => $filterStatus,
                'filterUser'        => $filterUser,
                'total'             => $totalStr
            ];

            $now = Carbon::now('Asia/Jakarta');
            $filename = 'PURCHASE_ORDER_REPORT_' . $now->toDateTimeString();

            $pdf = PDF3::loadView('documents.purchase_orders.purchase_orders_pdf', $data)
                ->setOption('footer-right', '[page] of [toPage]');

            return $pdf->download($filename.'.pdf');
        }
        else{
            $data =[
                'poHeaders'         => $poHeaders,
                'start_date'        => $request->input('start_date'),
                'end_date'          => $request->input('end_date'),
                'filterDepartment'  => $filterDepartment,
                'filterStatus'      => $filterStatus,
                'filterUser'        => $filterUser,
                'total'             => $totalStr,
                'department'        => $request->input('department'),
                'status'            => $request->input('status'),
                'user'              => $request->input('status')
            ];

            return view('documents.purchase_orders.purchase_orders_pdf_preview')->with($data);
        }
    }

    public function printDocument($id){
        $purchaseOrder = PurchaseOrderHeader::find($id);
        $dateNow = Carbon::now('Asia/Jakarta');
        $now = $dateNow->format('d-M-Y');

        // Get indexed approval users
        $approvals = new Collection();
        $approvalRules = ApprovalRule::where('document_id', 4)->orderBy('index')->get();
        $approvalPos = ApprovalPurchaseOrder::where('purchase_order_id', $id)->get();

        foreach ($approvalRules as $rule){
            if($approvalPos->where('user_id', $rule->user_id)->first()){
                $approvals->add($rule);
            }
        }

        // Get MR Type
        $mrType = $purchaseOrder->purchase_request_header->material_request_header->type;

        $data = [
            'purchaseOrder'         => $purchaseOrder,
            'now'                   => $now,
            'approvals'             => $approvals,
            'mrType'                => $mrType
        ];

        return view('documents.purchase_orders.purchase_orders_doc')->with($data);
    }

    public function download($id){
        $purchaseOrder = PurchaseOrderHeader::find($id);
        $purchaseOrderDetails = PurchaseOrderDetail::where('header_id', $purchaseOrder->id)->get();

        $data = [
            'purchaseOrder'         => $purchaseOrder,
            'purchaseOrderDetails'  => $purchaseOrderDetails
        ];

        $pdf = PDF::loadView('documents.purchase_orders.purchase_orders_doc', $data)->setPaper('A4');
        $now = Carbon::now('Asia/Jakarta');
        $filename = $purchaseOrder->code. '_' . $now->toDateTimeString();



        return $pdf->download($filename.'.pdf');
    }

    public function downloadPdf1(PurchaseOrderHeader $purchase_order){
        $downloadPath = public_path(). '/'. $purchase_order->quotation_pdf_path_1;

        if(file_exists($downloadPath)){
            return response()->download($downloadPath);
        }
        else{
            Session::flash('error', 'File PDF tidak ditemukan!');
            return redirect()->route('admin.purchase_orders.show', ['purchase_order' => $purchase_order->id]);
        }
    }

    public function downloadPdf2(PurchaseOrderHeader $purchase_order){
        $downloadPath = public_path(). '/'. $purchase_order->quotation_pdf_path_2;

        if(file_exists($downloadPath)){
            return response()->download($downloadPath);
        }
        else{
            Session::flash('error', 'File PDF tidak ditemukan!');
            return redirect()->route('admin.purchase_orders.show', ['purchase_order' => $purchase_order->id]);
        }
    }

    public function downloadPdf3(PurchaseOrderHeader $purchase_order){
        $downloadPath = public_path(). '/'. $purchase_order->quotation_pdf_path_3;

        if(file_exists($downloadPath)){
            return response()->download($downloadPath);
        }
        else{
            Session::flash('error', 'File PDF tidak ditemukan!');
            return redirect()->route('admin.purchase_orders.show', ['purchase_order' => $purchase_order->id]);
        }
    }
}