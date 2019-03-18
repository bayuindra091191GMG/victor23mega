<?php
/**
 * Created by PhpStorm.
 * User: hellb
 * Date: 2/18/2018
 * Time: 4:45 PM
 */

namespace App\Http\Controllers\Admin\Purchasing;


use App\Http\Controllers\Controller;
use App\Libs\Utilities;
use App\Models\NumberingSystem;
use App\Models\PaymentRequest;
use App\Models\PaymentRequestsPiDetail;
use App\Models\PaymentRequestsPoDetail;
use App\Models\PurchaseInvoiceHeader;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseOrderHeader;
use App\Models\Supplier;
use App\Transformer\Purchasing\PaymentRequestTransformer;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use PDF3;

class PaymentRequestController extends Controller
{
    public function index(){

        return View('admin.purchasing.payment_requests.index');
    }

    public function show(PaymentRequest $paymentRequest){
        $date = Carbon::parse($paymentRequest->date)->format('d M Y');

        $purchaseInvoices = PaymentRequestsPiDetail::where('payment_requests_id', $paymentRequest->id)->get();
        $purchaseOrders = PaymentRequestsPoDetail::where('payment_requests_id', $paymentRequest->id)->get();

        $flag = "po";
        if($purchaseInvoices->count() > 0){
            $flag = "pi";
        }

        // Get RFP type
        if($paymentRequest->type === 'cbd'){
            $type = "CASH BEFORE DELIVERY";
        }
        elseif($paymentRequest->type === "dp"){
            $type = "DOWN PAYMENT";
        }
        else{
            $type = "NORMAL";
        }

        $data = [
            'header'            => $paymentRequest,
            'purchaseInvoices'  => $purchaseInvoices,
            'purchaseOrders'    => $purchaseOrders,
            'flag'              => $flag,
            'date'              => $date,
            'type'              => $type
        ];

        return View('admin.purchasing.payment_requests.show')->with($data);
    }

    public function chooseVendor(){
        return View('admin.purchasing.payment_requests.choose_vendor');
    }

    public function chooseVendorPo(){
        return View('admin.purchasing.payment_requests.choose_vendor_po');
    }

    public function beforeCreateFromPi(){
        $supplier = null;
        if(!empty(request()->supplier)){
            $supplier = Supplier::find(request()->supplier);
        }

        return View('admin.purchasing.payment_requests.before_create_from_pi', compact('supplier'));
    }

    public function beforeCreateFromPo(){
        $supplier = null;
        if(!empty(request()->supplier)){
            $supplier = Supplier::find(request()->supplier);
        }

        return View('admin.purchasing.payment_requests.before_create_from_po', compact('supplier'));
    }

    public function createFromPi(Request $request){

        if(!$request->filled('ids')){
            Session::flash('error', 'Mohon pilih Purchase Invoice!');

            return redirect()->back();
        }

        $ids = $request->input('ids');
        $purchaseInvoices = PurchaseInvoiceHeader::whereIn('id', $ids)->get();

        // Get total Invoice
        $totalPayment = 0;
        foreach ($purchaseInvoices as $invoice){
            $totalPayment += $invoice->total_payment;
        }

        // Get supplier
        $vendor = Supplier::find($request->input('supplier'));

        // Numbering System
        $user = Auth::user();
        $sysNo = NumberingSystem::where('doc_id', '7')->first();
        $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumber($docCode, $sysNo->next_no);

        $data = [
            'purchaseInvoices'  => $purchaseInvoices,
            'autoNumber'        => $autoNumber,
            'vendor'            => $vendor,
            'totalPayment'      => $totalPayment
        ];

        return View('admin.purchasing.payment_requests.create')->with($data);
    }

    public function createFromPo(Request $request){
        if(!$request->filled('ids')){
            Session::flash('error', 'Mohon pilih Purchase Order!');

            return redirect()->back();
        }

        $ids = $request->input('ids');
        $purchaseOrders = PurchaseOrderHeader::whereIn('id', $ids)->get();

        // Get total PO
        $totalPayment = 0;
        foreach ($purchaseOrders as $poHeader){
            $totalPayment += $poHeader->total_payment;
        }

        // Get supplier
        $vendor = Supplier::find($request->input('supplier'));

        // Numbering System
        $user = Auth::user();
        $sysNo = NumberingSystem::where('doc_id', '7')->first();
        $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumber($docCode, $sysNo->next_no);

        $data = [
            'purchaseOrders'    => $purchaseOrders,
            'autoNumber'        => $autoNumber,
            'vendor'            => $vendor,
            'totalPayment'      => $totalPayment
        ];

        return View('admin.purchasing.payment_requests.create')->with($data);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'code'          => 'max:45|regex:/^\S*$/u',
            'date'          => 'required',
        ],[
            'code.regex'     => 'Nomor PO harus tanpa spasi!'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate Payment Request number
        if(empty(Input::get('auto_number')) && (empty(Input::get('code')) || Input::get('code') == "")){
            dd("Nomor RFP wajib diisi!");
            return redirect()->back()->withErrors('Nomor RFP wajib diisi!', 'default')->withInput($request->all());
//            Session::flash('error', 'Nomor RFP wajib diisi');
//            return redirect()->route('')
        }

        // Validate requested amount
        if(!$request->filled('amount') || $request->input('amount') === '0,00'){
            dd("Jumlah Permintaan wajib diisi!");
//            return redirect()->back()->withErrors('Jumlah Permintaan wajib diisi!', 'default')->withInput($request->all());
        }

        $flag = $request->input('flag');
        $inputAmount = Utilities::toFloat($request->input('amount'));
        $totalPayment = (double) $request->input('total_payment');

        if($inputAmount > ($totalPayment + 1)){
            dd("Jumlah Permintaan tidak boleh melebihi total pembayaran!");
//            return redirect()->back()->withErrors('Jumlah Permintaan tidak boleh melebihi total pembayaran!', 'default')->withInput($request->all());
        }

        $user = Auth::user();

        // Generate auto number
        if(Input::get('auto_number')){
            $sysNo = NumberingSystem::where('doc_id', '7')->first();
            $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
            $code = Utilities::GenerateNumber($docCode, $sysNo->next_no);
            $sysNo->next_no++;
            $sysNo->save();
        }
        else{
            $code = Input::get('code');
        }

        // Check existing number
        $temp = PaymentRequest::where('code', $code)->first();
        if(!empty($temp)){
            return redirect()->back()->withErrors('Nomor Payment Request sudah terdaftar!', 'default')->withInput($request->all());
        }

        $ids = $request->input('item');
        $ppn = 0;
        $pph_23 = 0;
        $total_amount = 0;
        $amount = 0;

        if($flag === "pi"){
            foreach($ids as $id){
                $temp = PurchaseInvoiceHeader::find($id);
                $ppn += $temp->ppn_amount ?? 0;
                $pph_23 += $temp->pph_amount ?? 0;
                $amount += $temp->total_price;
                $total_amount += $temp->total_payment;
            }
        }
        else{
            foreach($ids as $id){
                $temp = PurchaseOrderHeader::find($id);
                $ppn += $temp->ppn_amount ?? 0;
                $pph_23 += $temp->pph_amount ?? 0;
                $amount += $temp->total_price;
                $total_amount += $temp->total_payment;
            }
        }

        $now = Carbon::now('Asia/Jakarta');
        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');
        $vendor = Supplier::find($request->input('supplier'));

        $paymentRequest = PaymentRequest::create([
            'code'                      => $code,
            'date'                      => $date,
            'supplier_id'               => $vendor->id,
            'amount'                    => $inputAmount,
            'dp_amount'                 => 0,
            'requester_bank_name'       => $vendor->bank_name,
            'requester_bank_account'    => $vendor->bank_account_number,
            'requester_account_name'    => $vendor->bank_account_name,
            'note'                      => $request->input('note'),
            'type'                      => $request->input('type') === 'NORMAL' ? 'default' : $request->input('type'),
            'status_id'                 => 3,
            'created_by'                => $user->id,
            'created_at'                => $now->toDateTimeString()
        ]);

        // Check if DP or CBD
        $type = $request->input('type');
        if($type === "db" || $type === "cbd"){
            $paymentRequest->ppn = 0;
            $paymentRequest->pph_23 = 0;
        }
        else{
            $paymentRequest->ppn = $ppn;
            $paymentRequest->pph_23 = $pph_23;
        }

        // Save DP amount
        if($request->input('type') === 'dp'){
            $paymentRequest->dp_amount = $inputAmount;
            $paymentRequest->total_amount = $totalPayment;
        }
        else{
            $paymentRequest->total_amount = $inputAmount;
        }

        $paymentRequest->save();

        // Create Payment Request detail
        if($flag == "pi"){
            $purchaseInvoices = PurchaseInvoiceHeader::whereIn('id', $ids)->get();
            foreach($purchaseInvoices as $detail){
                //create detail
                $prDetail = PaymentRequestsPiDetail::create([
                    'payment_requests_id'           => $paymentRequest->id,
                    'purchase_invoice_header_id'    => $detail->id
                ]);

                $prDetail->save();
            }
        }
        else{
            $purchaseOrders = PurchaseOrderHeader::whereIn('id', $ids)->get();
            foreach($purchaseOrders as $detail){
                //create detail
                $prDetail = PaymentRequestsPoDetail::create([
                    'payment_requests_id'  => $paymentRequest->id,
                    'purchase_order_id'    => $detail->id
                ]);

                $prDetail->save();
            }
        }

        Session::flash('message', 'Berhasil membuat Payment Request!');

        return redirect()->route('admin.payment_requests.show', ['payment_request' => $paymentRequest]);
    }

    public function edit(PaymentRequest $payment_request){
        $header = $payment_request;

        $date = Carbon::parse($header->date)->format('d M Y');
        $purchaseInvoices = PaymentRequestsPiDetail::where('payment_requests_id', $header->id)->get();
        $purchaseOrders = PaymentRequestsPoDetail::where('payment_requests_id', $header->id)->get();

        if($header->type === 'dp'){
            $amount = !empty($header->dp_amount ) && $header->dp_amount > 0 ? $header->dp_amount : $header->total_amount;
        }
        else{
            $amount = $header->total_amount;
        }

        if($header->type === 'default'){
            $totalPayment = 0;
            foreach ($purchaseInvoices as $piHeader){
                $totalPayment += $piHeader->purchase_invoice_header->total_payment;
            }
        }
        else{
            $totalPayment = 0;
            foreach ($purchaseOrders as $poHeader){
                $totalPayment += $poHeader->purchase_order_header->total_payment;
            }
        }

        $data = [
            'header'            => $header,
            'purchaseInvoices'  => $purchaseInvoices,
            'purchaseOrders'    => $purchaseOrders,
            'date'              => $date,
            'amount'            => $amount,
            'totalPayment'      => $totalPayment
        ];

        return View('admin.purchasing.payment_requests.edit')->with($data);
    }

    public function update(Request $request, PaymentRequest $payment_request){
        $validator = Validator::make($request->all(),[
            'bank_name'     => 'required',
            'account_no'    => 'required',
            'account_name'  => 'required',
            'date'          => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate requested amount
        if(!$request->filled('amount') || $request->input('amount') === '0,00'){
            return redirect()->back()->withErrors('Jumlah Permintaan wajib diisi!', 'default')->withInput($request->all());
        }

        $inputAmount = Utilities::toFloat($request->input('amount'));
        $totalPayment = (double) $request->input('total_payment');

        if($inputAmount > $totalPayment){
            return redirect()->back()->withErrors('Jumlah Permintaan tidak boleh melebihi total pembayaran!', 'default')->withInput($request->all());
        }

        $ids = $request->input('item');
        $flag = $request->input('flag');
        $ppn = 0;
        $pph_23 = 0;
        $totalAmount = 0;
        $amount = 0;

        if($flag == "pi"){
            foreach($ids as $id){
                $temp = PurchaseInvoiceHeader::find($id);

                $ppn += $temp->ppn_amount ?? 0;
                $pph_23 += $temp->pph_amount ?? 0;
                $amount += $temp->total_price;
                $totalAmount += $temp->total_payment;
            }
        }
        else{
            foreach($ids as $id){
                $temp = PurchaseOrderHeader::find($id);
                $ppn += $temp->ppn_amount ?? 0;
                $pph_23 += $temp->pph_amount ?? 0;
                $amount += $temp->total_price;
                $totalAmount += $temp->total_payment;
            }
        }

        $user = \Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');

        $payment_request->date = $date;
        $payment_request->type = $request->input('type') === 'NORMAL' ? 'default' : $request->input('type');
        $payment_request->amount = $inputAmount;
        $payment_request->note = $request->input('note');
        $payment_request->updated_at = $now->toDateTimeString();
        $payment_request->updated_by = $user->id;

        // Check if DP or CBD
        $type = $request->input('type');
        if($type === "db" || $type === "cbd"){
            $payment_request->ppn = 0;
            $payment_request->pph_23 = 0;
        }
        else{
            $payment_request->ppn = $ppn;
            $payment_request->pph_23 = $pph_23;
        }

        // Save DP amount
        if($request->input('type') === 'dp'){
            $payment_request->dp_amount = $inputAmount;
            $payment_request->total_amount = $totalPayment;
        }
        else{
            $payment_request->total_amount = $inputAmount;
        }

        // Check vendor
        $payment_request->requester_bank_name = $payment_request->supplier->bank_name;
        $payment_request->requester_account_name = $payment_request->supplier->bank_account_name;
        $payment_request->requester_bank_account = $payment_request->supplier->bank_account_number;

        $payment_request->save();

        Session::flash('message', 'Berhasil mengubah Payment Request!');

        return redirect()->route('admin.payment_requests.edit', ['payment_request' => $payment_request->id]);
    }

    public function destroy(Request $request){
        $paymentRequest = PaymentRequest::find($request->input('deleted_id'));
        $tmpCode = $paymentRequest->code;

        if($paymentRequest->payment_requests_pi_details()->count() > 0){
            foreach ($paymentRequest->payment_requests_pi_details as $piDetail){
                $piDetail->delete();
            }
        }

        if($paymentRequest->payment_requests_po_details()->count() > 0){
            foreach ($paymentRequest->payment_requests_po_details as $poDetail){
                $poDetail->delete();
            }
        }

        $paymentRequest->delete();

        Session::flash('message', 'Berhasil menghapus Payment Request '. $tmpCode);

        return redirect()->route('admin.payment_requests');
    }

    public function report(){
        return View('admin.purchasing.payment_requests.report');
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

        $rfpHeaders = PaymentRequest::whereBetween('date', array($start->toDateTimeString(), $end->toDateTimeString()))
            ->orderBy('date')
            ->get();

        if($rfpHeaders->count() == 0){
            return redirect()->back()->withErrors('Data Tidak Ditemukan!', 'default')->withInput($request->all());
        }

        // Get Total Amount
        $total = 0;
        foreach ($rfpHeaders as $header){
            $total += $header->total_amount;
        }

        $totalStr = 'Rp '. number_format($total, 2, ",", ".");

        if($request->input('is_preview') === 'false'){
            $data =[
                'rfpHeaders'        => $rfpHeaders,
                'start_date'        => $request->input('start_date'),
                'finish_date'       => $request->input('end_date'),
                'totalStr'          => $totalStr
            ];

            //return view('documents.material_requests.material_requests_pdf')->with($data);

            $now = Carbon::now('Asia/Jakarta');
            $filename = 'PAYMENT_REQUEST_REPORT_' . $now->toDateTimeString();

            $pdf = PDF3::loadView('documents.payment_requests.payment_requests_pdf', $data)
                ->setOption('footer-right', '[page] of [toPage]');

            return $pdf->download($filename.'.pdf');
        }
        else{
            $data =[
                'rfpHeaders'        => $rfpHeaders,
                'start_date'        => $request->input('start_date'),
                'finish_date'       => $request->input('end_date'),
                'totalStr'          => $totalStr
            ];

            return view('documents.payment_requests.payment_requests_pdf_preview')->with($data);
        }
    }

    public function getIndex(){
        try{
            $paymentRequests = PaymentRequest::with('supplier');
            return DataTables::of($paymentRequests)
                ->setTransformer(new PaymentRequestTransformer())
                ->make(true);
        }
        catch(\Exception $ex){
            error_log($ex);
        }
    }

    // Function terbilang
    function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai <20) {
            $temp = $this->penyebut($nilai - 10). " belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebut($nilai/10)." puluh". $this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . $this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebut($nilai/100) . " ratus" . $this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . $this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebut($nilai/1000) . " ribu" . $this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebut($nilai/1000000) . " juta" . $this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->penyebut($nilai/1000000000) . " milyar" . $this->penyebut(fmod($nilai,1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $this->penyebut($nilai/1000000000000) . " trilyun" . $this->penyebut(fmod($nilai,1000000000000));
        }
        return $temp;
    }

    //Function Terbilang
    function terbilang($nilai) {
        if($nilai<0) {
            $hasil = "minus ". trim($this->penyebut($nilai));
        } else {
            $hasil = trim($this->penyebut($nilai));
        }
        return $hasil;
    }

    public function printDocument($id){
        $paymentRequest = PaymentRequest::find($id);
        $poDetails = PaymentRequestsPoDetail::where('payment_requests_id', $paymentRequest->id)->get();
        $piDetails = PaymentRequestsPiDetail::where('payment_requests_id', $paymentRequest->id)->get();

        // Get RFP type
        if($paymentRequest->type === 'cbd'){
            $type = "CASH BEFORE DELIVERY";
            $terbilang = $this->terbilang(round($paymentRequest->total_amount));
        }
        elseif($paymentRequest->type === "dp"){
            $type = "DOWN PAYMENT";
            $terbilang = $this->terbilang(round($paymentRequest->dp_amount));
        }
        else{
            $type = "NORMAL";
            $terbilang = $this->terbilang(round($paymentRequest->total_amount));
        }

        $data = [
            'paymentRequest'        => $paymentRequest,
            'poDetails'             => $poDetails,
            'piDetails'             => $piDetails,
            'terbilang'             => $terbilang,
            'type'                  => $type
        ];

        return view('documents.payment_requests.payment_requests_doc')->with($data);
    }

    public function download($id){
        $purchaseOrder = PurchaseOrderHeader::find($id);
        $purchaseOrderDetails = PurchaseOrderDetail::where('header_id', $purchaseOrder->id)->get();

        $pdf = PDF::loadView('documents.purchase_orders.purchase_orders_doc', ['purchaseOrder' => $purchaseOrder, 'purchaseOrderDetails' => $purchaseOrderDetails])->setPaper('A4');
        $now = Carbon::now('Asia/Jakarta');
        $filename = $purchaseOrder->code. '_' . $now->toDateTimeString();

        return $pdf->download($filename.'.pdf');
    }
}