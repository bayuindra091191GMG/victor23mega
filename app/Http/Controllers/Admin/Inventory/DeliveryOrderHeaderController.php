<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 02/03/2018
 * Time: 14:56
 */

namespace App\Http\Controllers\Admin\Inventory;


use App\Http\Controllers\Controller;
use App\Libs\Utilities;
use App\Models\DeliveryOrderConfirmDetail;
use App\Models\DeliveryOrderConfirmHeader;
use App\Models\DeliveryOrderDetail;
use App\Models\DeliveryOrderHeader;
use App\Models\Item;
use App\Models\ItemReceiptHeader;
use App\Models\ItemStock;
use App\Models\NumberingSystem;
use App\Models\StockCard;
use App\Models\Warehouse;
use App\Transformer\Inventory\DeliveryOrderHeaderTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade as PDF;

class DeliveryOrderHeaderController extends Controller
{
    public function index(Request $request){
        $filterStatus = '3';
        if($request->status != null){
            $filterStatus = $request->status;
        }

        return View('admin.inventory.delivery_orders.index', compact('filterStatus'));
    }

    public function create(){
        $warehouses = Warehouse::where('id', '>', 0)->get();

        // Get GR data if exist
        $itemReceipt = null;
        if(!empty(request()->gr)){
            $itemReceipt = ItemReceiptHeader::find(request()->gr);
        }

        // Numbering System
        $user = Auth::user();
        $sysNo = NumberingSystem::where('doc_id', '8')->first();
        $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumber($docCode, $sysNo->next_no);

        $data =[
            'warehouses'        => $warehouses,
            'autoNumber'        => $autoNumber,
            'itemReceipt'       => $itemReceipt
        ];

        return View('admin.inventory.delivery_orders.create')->with($data);
    }

    public function show(DeliveryOrderHeader $delivery_order){
        $header = $delivery_order;
        $date = Carbon::parse($delivery_order->date)->format('d M Y');

        // Get MR if exists
        $mrShowUrl = null;
        if(!empty($header->item_receipt_id)){
            $materialRequest = $header->item_receipt_header->purchase_order_header->purchase_request_header->material_request_header;
            if($materialRequest->type === 1){
                $mrShowUrl = route('admin.material_requests.other.show', ['material_request' => $materialRequest->id]);
            }
            else if($materialRequest->type === 2){
                $mrShowUrl = route('admin.material_requests.fuel.show', ['material_request' => $materialRequest->id]);
            }
            else{
                $mrShowUrl = route('admin.material_requests.oil.show', ['material_request' => $materialRequest->id]);
            }
        }

        // Check for partial confirmation data
        $isPartial = DB::table('delivery_order_confirm_headers')
            ->where('delivery_order_id', '=', $header->id)
            ->exists();

        $data = [
            'header'    => $header,
            'date'      => $date,
            'mrShowUrl' => $mrShowUrl,
            'isPartial' => $isPartial
        ];

        return View('admin.inventory.delivery_orders.show')->with($data);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'do_code'        => 'required|max:30|regex:/^\S*$/u',
            'remark_header'  => 'max:150',
            'date'           => 'required'
        ],[
            'code.regex'     => 'Nomor Surat Jalan harus tanpa spasi'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate DO number
        if(empty($request->input('auto_number')) && (empty($request->input('do_code')) || $request->input('do_code') == "")){
            return redirect()->back()->withErrors('Nomor Surat Jalan wajib diisi!', 'default')->withInput($request->all());
        }

        // Validate PR number
//        if(empty($request->input('pr_code')) && empty($request->input('pr_id'))){
//            return redirect()->back()->withErrors('Nomor PR wajib diisi!', 'default')->withInput($request->all());
//        }

        // Validate from & to warehouse
        if($request->input('from_warehouse') === '-1' || $request->input('to_warehouse') === '-1'){
            return redirect()->back()->withErrors('Pilih gudang keberangkatan & tujuan!', 'default')->withInput($request->all());
        }

        if($request->input('from_warehouse') === $request->input('to_warehouse')){
            return redirect()->back()->withErrors('Gudang keberangkatan & tujuan harus berbeda!', 'default')->withInput($request->all());
        }

        // Get GR id
        $grId = '0';
        if($request->filled('gr_code')){
            $grId = $request->input('gr_code');
        }
        else{
            if($request->filled('gr_id')){
                $grId = $request->input('gr_id');
            }
        }

        if($grId != '0'){
            $itemReceipt = ItemReceiptHeader::find($grId);
            if($itemReceipt->purchase_order_header->purchase_request_header->material_request_header->type == 4){
                return redirect()->back()->withErrors('MR servis tidak bisa dibuat surat jalan!', 'default')->withInput($request->all());
            }
        }

        // Validate details
        $items = $request->input('item');
        $remarks = $request->input('remark');

        if(count($items) == 0){
            return redirect()->back()->withErrors('Detail barang wajib diisi!', 'default')->withInput($request->all());
        }

        $qtys = $request->input('qty');
        $valid = true;
        $i = 0;
        foreach($items as $item){
            if(empty($item)) $valid = false;
            if(empty($qtys[$i]) || $qtys[$i] == '0') $valid = false;
            $i++;
        }

        if(!$valid){
            return redirect()->back()->withErrors('Detail barang dan jumlah wajib diisi!', 'default')->withInput($request->all());
        }

        $valid = true;
        // Validate stock
        $fromWarehouse = Warehouse::find($request->input('from_warehouse'));
        $i = 0;
        foreach($items as $item){
            if(!empty($item)){
                $valid = ItemStock::where('warehouse_id', $fromWarehouse->id)
                    ->where('item_id', $item)
                    ->where('stock', '>=', $qtys[$i])
                    ->exists();
            }
            $i++;
        }

        if(!$valid){
            return redirect()->back()->withErrors('Stok barang kosong atau tidak ada!', 'default')->withInput($request->all());
        }

        $user = Auth::user();

        // Generate auto number
        $doCode = 'default';
        if($request->input('auto_number')){
            $sysNo = NumberingSystem::where('doc_id', '8')->first();
            $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
            $doCode = Utilities::GenerateNumber($docCode, $sysNo->next_no);

            // Check existing number
            $temp = DeliveryOrderHeader::where('code', $doCode)->first();
            if(!empty($temp)){
                return redirect()->back()->withErrors('Nomor Surat Jalan sudah terpakai!', 'default')->withInput($request->all());
            }

            $sysNo->next_no++;
            $sysNo->save();
        }
        else{
            $doCode = $request->input('do_code');

            // Check existing number
            $temp = DeliveryOrderHeader::where('code', $doCode)->first();
            if(!empty($temp)){
                return redirect()->back()->withErrors('Nomor Surat Jalan sudah terpakai!', 'default')->withInput($request->all());
            }
        }

        $now = Carbon::now('Asia/Jakarta');

        $doHeader = DeliveryOrderHeader::create([
            'code'                  => $doCode,
            'from_warehouse_id'     => $request->input('from_warehouse'),
            'to_warehouse_id'       => $request->input('to_warehouse'),
            'status_id'             => 3,
            'created_by'            => $user->id,
            'created_at'            => $now->toDateTimeString(),
            'updated_by'            => $user->id
        ]);

        $doHeader->item_receipt_id = $grId !== '0' ? $grId : null;

        if($request->filled('machinery')){
            $doHeader->machinery_id = $request->input('machinery');
        }
        else{
            if($request->filled('machinery_id')){
                $doHeader->machinery_id = $request->input('machinery_id');
            }
        }

        if($request->filled('remark')){
            $doHeader->remark = $request->input('remark_header');
        }

        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');

        $doHeader->date = $date->toDateTimeString();

        $doHeader->save();

        // Create delivery order detail
        $idx = 0;
        foreach($request->input('item') as $item){
            if(!empty($item)){
                $doDetail = DeliveryOrderDetail::create([
                    'header_id'     => $doHeader->id,
                    'item_id'       => $item,
                    'quantity'      => $qtys[$idx]
                ]);

                if(!empty($remarks[$idx])) {
                    $doDetail->remark = $remarks[$idx];
                    $doDetail->save();
                }

                // Change stock
                $stock = ItemStock::where('warehouse_id', $fromWarehouse->id)
                    ->where('item_id', $item)
                    ->first();

                $qtyInt = (int) $qtys[$idx];

                $stock->stock -= $qtyInt;
                $stock->save();

                // Get warehouse stock result
                $stockResultWarehouse = $qtyInt;

                $itemData = Item::find($item);

                // Add stock card
                StockCard::create([
                    'item_id'               => $item,
                    'in_qty'                => 0,
                    'out_qty'               => $qtys[$idx],
                    'result_qty'            => $itemData->stock,
                    'result_qty_warehouse'  => $stockResultWarehouse,
                    'warehouse_id'          => $fromWarehouse->id,
                    'created_by'            => $user->id,
                    'created_at'            => $now->toDateTimeString(),
                    'updated_by'            => $user->id,
                    'updated_at'            => $now->toDateTimeString(),
                    'reference'             => 'Surat Jalan '. $doHeader->code
                ]);

                // Entry to Transport Warehouse
                $transportStock = ItemStock::where('warehouse_id', 0)
                    ->where('item_id', $item)
                    ->first();
                if(!empty($transportStock)){
                    $transportStock->stock += intval($qtys[$idx]);
                    $transportStock->updated_by = $user->id;
                    $transportStock->updated_at = $now->toDateTimeString();
                    $transportStock->save();
                }
                else{
                    $newTransportStock = ItemStock::create([
                        'item_id'           => $item,
                        'warehouse_id'      => 0,
                        'stock'             => $qtys[$idx],
                        'stock_min'         => 0,
                        'stock_max'         => 0,
                        'is_stock_warning'  => false,
                        'created_by'        => $user->id,
                        'created_at'        => $now->toDateTimeString(),
                        'updated_by'        => $user->id,
                        'updated_at'        => $now->toDateTimeString(),
                    ]);
                }
            }
            $idx++;
        }

        Session::flash('message', 'Berhasil membuat surat jalan!');

        return redirect()->route('admin.delivery_orders.show', ['delivery_order' => $doHeader]);
    }

    public function edit(DeliveryOrderHeader $delivery_order){
        $header = $delivery_order;
        $warehouses = Warehouse::where('id', '>', 0)->get();
        $date = Carbon::parse($delivery_order->date)->format('d M Y');

        $data = [
            'header'        => $header,
            'warehouses'    => $warehouses,
            'date'          => $date
        ];

        return View('admin.inventory.delivery_orders.edit')->with($data);
    }

    public function update(Request $request, DeliveryOrderHeader $delivery_order){
        $validator = Validator::make($request->all(),[
            'remark'        => 'max:150',
            'date'          => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = \Auth::user();
        $now = Carbon::now('Asia/Jakarta');

        $delivery_order->remark = $request->input('remark');
        $delivery_order->updated_by = $user->id;
        $delivery_order->updated_at = $now->toDateTimeString();

        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');
        $delivery_order->date = $date->toDateTimeString();

        $delivery_order->save();

        Session::flash('message', 'Berhasil ubah Surat Jalan!');

        return redirect()->route('admin.delivery_orders.show', ['delivery_order' => $delivery_order]);
    }

    public function confirm(Request $request){
        try{
            $user = \Auth::user();
            $now = Carbon::now('Asia/Jakarta');

            $header = DeliveryOrderHeader::find($request->input('id'));

            // Validate status
            if($header->status_id !== 3){
                return Response::json(array('errors' => 'INVALID'));
            }

            // Create confirmation data
            $newConfirm = new DeliveryOrderConfirmHeader();
            $newConfirm->delivery_order_id = $header->id;

            $documentCode = $header->code. '-CONFIRM-1';

            $newConfirm->code = $documentCode;
            $newConfirm->remark = '';
            $newConfirm->confirm_by = $user->id;
            $newConfirm->confirm_date = Carbon::now('Asia/Jakarta')->toDateTimeString();
            $newConfirm->created_by = $user->id;
            $newConfirm->updated_by = $user->id;
            $newConfirm->save();

            foreach($header->delivery_order_details as $detail){
                // Create detail confirmation data
                $newConfirmDetail = new DeliveryOrderConfirmDetail();
                $newConfirmDetail->header_id = $newConfirm->id;
                $newConfirmDetail->item_id = $detail->item_id;
                $newConfirmDetail->item_code = $detail->item->code;
                $newConfirmDetail->item_name = $detail->item->name;
                $newConfirmDetail->item_uom = $detail->item->uom;
                $newConfirmDetail->qty = $detail->quantity;
                $newConfirmDetail->created_by = $user->id;
                $newConfirmDetail->updated_by = $user->id;
                $newConfirmDetail->save();

                // Update qty confirmed
                DB::table('delivery_order_details')
                    ->where('id', '=', $detail->id)
                    ->update([
                        'quantity_confirmed' => $detail->quantity
                    ]);

                // Decrease transport warehouse stock
                $stockTransport = ItemStock::where('warehouse_id', 0)
                    ->where('item_id', $detail->item_id)
                    ->first();

                if($stockTransport->stock === 0) {
                    continue;
                }

                $stockTransport->stock -= $detail->quantity;
                $stockTransport->save();

                // Increase arrival warehouse stock
                $stockArrival = ItemStock::where('warehouse_id', $header->to_warehouse_id)
                    ->where('item_id', $detail->item_id)
                    ->first();

                // Get warehouse stock result
                $stockResultWarehouse = 0;
                if(!empty($stockArrival)){
                    $stockArrival->stock += $detail->quantity;
                    $stockArrival->updated_at = $now->toDateTimeString();
                    $stockArrival->updated_by = $user->id;
                    $stockArrival->save();

                    $stockResultWarehouse = $stockArrival->stock;
                }
                else{
                    $newStock = new ItemStock();
                    $newStock->warehouse_id = $header->to_warehouse_id;
                    $newStock->item_id = $detail->item_id;
                    $newStock->stock = $detail->quantity;
                    $newStock->stock_min = 0;
                    $newStock->stock_max = 0;
                    $newStock->is_stock_warning = false;
                    $newStock->created_by = $user->id;
                    $newStock->created_at = $now->toDateTimeString();
                    $newStock->updated_by = $user->id;
                    $newStock->updated_at = $now->toDateTimeString();
                    $newStock->save();

                    $stockResultWarehouse = $newStock->stock;
                }

                // Add stock card
                StockCard::create([
                    'item_id'               => $detail->item_id,
                    'in_qty'                => $detail->quantity,
                    'out_qty'               => 0,
                    'result_qty'            => $detail->item->stock,
                    'result_qty_warehouse'  => $stockResultWarehouse,
                    'warehouse_id'          => $header->to_warehouse_id,
                    'created_by'            => $user->id,
                    'created_at'            => $now->toDateTimeString(),
                    'updated_by'            => $user->id,
                    'updated_at'            => $now->toDateTimeString(),
                    'reference'             => 'Surat Jalan '. $header->code. ' Confirm'
                ]);

                // Update qty confirmed
                DB::table('delivery_order_details')
                    ->where('id', '=', $detail->id)
                    ->update([
                        'quantity_confirmed' => $detail->quantity
                    ]);
            }

            $header->status_id = 4;
            $header->is_partial_confirmed = false;
            $header->is_all_confirmed = true;
            $header->is_synced = false;
            $header->confirm_by = $user->id;
            $header->confirm_date = $now->toDateTimeString();
            $header->updated_by = $user->id;
            $header->updated_at = $now->toDateTimeString();

            // Get lead time
            if(!empty($header->item_receipt_id)){
                $mrHeader = $header->item_receipt_header->purchase_order_header->purchase_request_header->material_request_header;
                $mrCreatedDate = Carbon::parse($mrHeader->date);
                $header->lead_time = $mrCreatedDate->diffInDays($now);
            }

            $header->save();

            Session::flash('message', 'Berhasil konfirmasi barang datang pada Surat Jalan '. $header->code);

            return Response::json(array('success' => 'VALID'));
        }
        catch(\Exception $ex){
            error_log($ex);
            return Response::json(array('errors' => 'INVALID'));
        }
    }

    public function cancel(Request $request){
        try{
            $user = \Auth::user();
            $now = Carbon::now('Asia/Jakarta');

            $header = DeliveryOrderHeader::find($request->input('id'));

            // Validate status
            if($header->status_id != 3){
                return Response::json(array('errors' => 'INVALID'));
            }

            foreach($header->delivery_order_details as $detail){
                // Decrease transport warehouse stock
                $stockTransport = ItemStock::where('warehouse_id', 0)
                    ->where('item_id', $detail->item_id)
                    ->first();

                if($stockTransport->stock === 0) {
                    continue;
                }

                $stockTransport->stock -= $detail->quantity;
                $stockTransport->save();

                // Restore from warehouse stock
                $stockArrival = ItemStock::where('warehouse_id', $header->from_warehouse_id)
                    ->where('item_id', $detail->item_id)
                    ->first();

                // Get warehouse stock result
                $stockResultWarehouse = 0;
                if(!empty($stockArrival)){
                    $stockArrival->stock += $detail->quantity;
                    $stockArrival->updated_at = $now->toDateTimeString();
                    $stockArrival->updated_by = $user->id;
                    $stockArrival->save();

                    $stockResultWarehouse = $stockArrival->stock;
                }
                else{
                    $newStock = new ItemStock();
                    $newStock->warehouse_id = $header->from_warehouse_id;
                    $newStock->item_id = $detail->item_id;
                    $newStock->stock = $detail->quantity;
                    $newStock->stock_min = 0;
                    $newStock->stock_max = 0;
                    $newStock->is_stock_warning = false;
                    $newStock->created_by = $user->id;
                    $newStock->created_at = $now->toDateTimeString();
                    $newStock->updated_by = $user->id;
                    $newStock->updated_at = $now->toDateTimeString();
                    $newStock->save();

                    $stockResultWarehouse = $newStock->stock;
                }

                // Add stock card
                StockCard::create([
                    'item_id'               => $detail->item_id,
                    'in_qty'                => $detail->quantity,
                    'out_qty'               => 0,
                    'result_qty'            => $detail->item->stock,
                    'result_qty_warehouse'  => $stockResultWarehouse,
                    'warehouse_id'          => $header->from_warehouse_id,
                    'created_by'            => $user->id,
                    'created_at'            => $now->toDateTimeString(),
                    'updated_by'            => $user->id,
                    'updated_at'            => $now->toDateTimeString(),
                    'reference'             => 'Surat Jalan '. $header->code. ' Cancel'
                ]);
            }

            $header->status_id = 5;
            $header->cancel_by = $user->id;
            $header->cancel_date = $now->toDateTimeString();
            $header->updated_by = $user->id;
            $header->updated_at = $now->toDateTimeString();
            $header->save();

            Session::flash('message', 'Berhasil membatalkan Surat Jalan '. $header->code);

            return Response::json(array('success' => 'VALID'));
        }
        catch(\Exception $ex){
            error_log($ex);
            return Response::json(array('errors' => 'INVALID'));
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getIndex(Request $request){

        $status = '0';
        if($request->filled('status')){
            $status = $request->input('status');
            if($status != '0'){
                $deliveryOrders = DeliveryOrderHeader::where('status_id', $status)
                    ->dateDescending()
                    ->get();
            }
            else{
                $deliveryOrders = DeliveryOrderHeader::dateDescending()->get();
            }
        }
        else{
            $deliveryOrders = DeliveryOrderHeader::dateDescending()->get();
        }

        return DataTables::of($deliveryOrders)
            ->setTransformer(new DeliveryOrderHeaderTransformer)
            ->addIndexColumn()
            ->make(true);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getDeliveryOrders(Request $request){
        $term = trim($request->q);
        $deliveries = DeliveryOrderHeader::where('code', 'LIKE', '%'. $term. '%')
            ->get();

        $formatted_tags = [];

        foreach ($deliveries as $delivery) {
            $formatted_tags[] = ['id' => $delivery->id, 'text' => $delivery->code];
        }

        return Response::json($formatted_tags);
    }

    public function print($id){
        $header = DeliveryOrderHeader::find($id);

        return view('documents.delivery_orders.delivery_orders_doc', compact('header'));
    }

    public function report(){
        return View('admin.inventory.delivery_orders.report');
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

        $doHeaders = DeliveryOrderHeader::whereBetween('created_at', array($start, $end));

        // Filter status
        $status = $request->input('status');
        if($status != '0'){
            $doHeaders = $doHeaders->where('status_id', $status);
        }

        $doHeaders = $doHeaders->orderByDesc('date')
            ->get();

        // Check Data
        if($doHeaders->count() == 0){
            return redirect()->back()->withErrors('Data tidak ditemukan!', 'default')->withInput($request->all());
        }

        $data =[
            'doHeaders'         => $doHeaders,
            'start_date'        => $request->input('start_date'),
            'finish_date'       => $request->input('end_date')
        ];

        $pdf = PDF::loadView('documents.delivery_orders.delivery_orders_pdf', $data)
            ->setPaper('a4', 'landscape');
        $now = Carbon::now('Asia/Jakarta');
        $filename = 'DELIVERY_ORDER_REPORT_' . $now->toDateTimeString();
        $pdf->setOptions(["isPhpEnabled"=>true]);

        return $pdf->download($filename.'.pdf');
    }

    /**
     * Function to show DO partial confirm form
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|string
     */
    public function partialConfirmForm($id){
        try {
            $deliveryOrder = DeliveryOrderHeader::with(['delivery_order_details'])->find($id);
            if(empty($deliveryOrder)){
                return redirect()->back();
            }

            $date = Carbon::parse($deliveryOrder->date)->format('d M Y');

            // Get index of confirmation data
            $confirmCount = DB::table('delivery_order_confirm_headers')
                ->where('delivery_order_id', '=', $deliveryOrder->id)
                ->count();

            $documentCode = $deliveryOrder->code. '-CONFIRM-'. ($confirmCount + 1);

            $deliveryOrderDetail = [];
            $idx = 0;
            foreach ($deliveryOrder->delivery_order_details as $detail){
                if($detail->quantity_confirmed  < $detail->quantity){
                    $deliveryOrderDetail[] = [
                        'item_id' => $detail->item_id,
                        'code' => $detail->item->code,
                        'name' => $detail->item->name,
                        'qty_value' => $detail->quantity - $detail->quantity_confirmed,
                        'qty_input_id' => 'qty_input_'. $idx
                    ];
                }

                $idx++;
            }

            $vueData = [
                'delivery_order_array' => $deliveryOrderDetail
            ];

            $data = [
                'deliveryOrder' => $deliveryOrder,
                'documentCode' => $documentCode,
                'date' => $date,
                'vueData' => $vueData
            ];

            return view('admin.inventory.delivery_orders.partial_confirm')->with($data);
        }
        catch (\Exception $ex){
            Log::error('DeliveryOrderHeaderController - partialConfirmForm '. $ex);
            return 'Terjadi kesalah internal sistem';
        }
    }

    public function partialConfirmSubmit(Request $request, $id){
        try {
            $doId = $id;

            $deliveryOrder = DeliveryOrderHeader::with(['delivery_order_details'])->find($doId);
            if(empty($deliveryOrder)){
                return redirect()->back()->withErrors('BAD REQUEST!', 'default')->withInput($request->all());
            }

            $user = \Auth::user();

            // Validate qty of each row
            $isValid = true;
            $itemDetailArr = $request->input('item_detail');

            foreach ($itemDetailArr as $itemDetail){
                if(empty($itemDetail['qty'])){
                    return redirect()->back()->withErrors('Wajib isi QTY Penerimaan!', 'default')->withInput($request->all());
                }
                else{
                    $qty = Utilities::toInt($itemDetail['qty']);
                    if($qty === 0){
                        return redirect()->back()->withErrors('QTY Penerimaan tidak boleh nol!', 'default')->withInput($request->all());
                    }
                    else{
                        $doDetail = DB::table('delivery_order_details')
                            ->where('header_id', '=', $deliveryOrder->id)
                            ->where('item_id', '=', $itemDetail['item_id'])
                            ->first();

                        if(empty($doDetail)){
                            return redirect()->back()->withErrors('Tidak ditemukan detil barang pada Surat Jalan!', 'default')->withInput($request->all());
                        }
                        else{
                            $qtyLeft = $doDetail->quantity - $doDetail->quantity_confirmed;
                            if($qty > $qtyLeft){
                                return redirect()->back()->withErrors('QTY Penerimaan tidak boleh melebihi QTY Surat Jalan!', 'default')->withInput($request->all());
                            }
                        }
                    }
                }
            }

            $siteName = DB::table('sites')
                ->where('id', '=', $deliveryOrder->toWarehouse->site_id)
                ->value('name');
            $siteName = strtolower($siteName);

            // Store DO confirmation data
            $newConfirm = new DeliveryOrderConfirmHeader();
            $newConfirm->delivery_order_id = $deliveryOrder->id;

            // Get index of confirmation data
            $confirmCount = DB::table('delivery_order_confirm_headers')
                ->where('delivery_order_id', '=', $deliveryOrder->id)
                ->count();

            $documentCode = $deliveryOrder->code. '-CONFIRM-'. ($confirmCount + 1);

            $newConfirm->code = $documentCode;
            $newConfirm->remark = $request->input('remark');
            $newConfirm->confirm_by = $user->id;
            $newConfirm->confirm_date = Carbon::now('Asia/Jakarta')->toDateTimeString();
            $newConfirm->created_by = $user->id;
            $newConfirm->updated_by = $user->id;
            $newConfirm->is_synced = false;
            $newConfirm->created_on = 'offline-'. $siteName;
            $newConfirm->save();

            foreach ($itemDetailArr as $itemDetail){
                $itemObj = DB::table('items')
                    ->select('id','code','name', 'uom')
                    ->where('id', '=', $itemDetail['item_id'])
                    ->first();

                $qtyConfirmed = Utilities::toInt($itemDetail['qty']);

                $newConfirmDetail = new DeliveryOrderConfirmDetail();
                $newConfirmDetail->header_id = $newConfirm->id;
                $newConfirmDetail->item_id = $itemDetail['item_id'];
                $newConfirmDetail->item_code = $itemObj->code;
                $newConfirmDetail->item_name = $itemObj->name;
                $newConfirmDetail->item_uom = $itemObj->uom;
                $newConfirmDetail->qty = $qtyConfirmed;
                $newConfirmDetail->created_by = $user->id;
                $newConfirmDetail->updated_by = $user->id;
                $newConfirmDetail->save();

                $now = Carbon::now('Asia/Jakarta')->toDateTimeString();

                // Update qty confirmed
                DB::table('delivery_order_details')
                    ->where('header_id', '=', $deliveryOrder->id)
                    ->where('item_id', '=', $itemDetail['item_id'])
                    ->increment('quantity_confirmed', $qtyConfirmed);

                // Decrease transport warehouse stock
                DB::table('item_stocks')
                    ->where('warehouse_id', '=', 0)
                    ->where('item_id', '=', $itemDetail['item_id'])
                    ->decrement('stock', $qtyConfirmed, ['updated_at' => $now]);

                // Increase arrival warehouse stock
                $stockArrival = ItemStock::where('warehouse_id', $deliveryOrder->to_warehouse_id)
                    ->where('item_id', $itemDetail['item_id'])
                    ->first();

                if(empty($stockArrival)){
                    $newStock = new ItemStock();
                    $newStock->warehouse_id = $deliveryOrder->to_warehouse_id;
                    $newStock->item_id = $itemDetail['item_id'];
                    $newStock->stock = $qtyConfirmed;
                    $newStock->stock_min = 0;
                    $newStock->stock_max = 0;
                    $newStock->is_stock_warning = false;
                    $newStock->created_by = $user->id;
                    $newStock->created_at = $now;
                    $newStock->updated_by = $user->id;
                    $newStock->updated_at = $now;
                    $newStock->save();

                    $stockArrivalId = $newStock->id;
                    $stockResultWarehouse = $qtyConfirmed;
                }
                else{
                    $stockArrivalId = $stockArrival->id;
                    $stockResultWarehouse = $stockArrival->stock + $qtyConfirmed;
                }

                DB::table('item_stocks')
                    ->where('id', '=', $stockArrivalId)
                    ->increment('stock', $qtyConfirmed, ['updated_at' => $now]);

                // Add stock card
                StockCard::create([
                    'item_id'               => $itemDetail['item_id'],
                    'in_qty'                => $qtyConfirmed,
                    'out_qty'               => 0,
                    'result_qty'            => 0,
                    'result_qty_warehouse'  => $stockResultWarehouse,
                    'warehouse_id'          => $deliveryOrder->to_warehouse_id,
                    'created_by'            => $user->id,
                    'created_at'            => $now,
                    'updated_by'            => $user->id,
                    'updated_at'            => $now,
                    'reference'             => 'Surat Jalan '. $deliveryOrder->code. ' Confirm'
                ]);
            }

            // Check DO confirmation status
            $deliveryOrder = DeliveryOrderHeader::with(['delivery_order_details'])->find($doId);

            $isConfirmed = true;
            foreach ($deliveryOrder->delivery_order_details as $detail){
                if($detail->quantity !== $detail->quantity_confirmed){
                    $isConfirmed = false;
                }
            }

            if($isConfirmed){
                $deliveryOrder->is_partial_confirmed = false;
                $deliveryOrder->is_all_confirmed = true;
                $deliveryOrder->status_id = 4;
                $deliveryOrder->is_synced = false;
                $deliveryOrder->save();
            }
            else{
                $deliveryOrder->is_partial_confirmed = true;
                $deliveryOrder->is_all_confirmed = false;
                $deliveryOrder->is_synced = false;
                $deliveryOrder->save();
            }

            Session::flash('message', 'Berhasil konfirmasi parsial Surat Jalan ', $deliveryOrder->code);
            return redirect()->route('admin.delivery_orders.show', ['delivery_order' => $deliveryOrder->id]);
        }
        catch (\Exception $ex){
            Log::error('DeliveryOrderHeaderController - partialConfirmSubmit '. $ex);
            return redirect()->back()->withErrors('Terjadi kesalahan internal sistem', 'default')->withInput($request->all());
        }
    }
}