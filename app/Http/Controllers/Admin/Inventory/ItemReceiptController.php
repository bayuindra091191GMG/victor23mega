<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Libs\Utilities;
use App\Models\Auth\Role\Role;
use App\Models\DeliveryOrderHeader;
use App\Models\Department;
use App\Models\Item;
use App\Models\ItemReceiptDetail;
use App\Models\ItemReceiptHeader;
use App\Models\ItemStock;
use App\Models\NumberingSystem;
use App\Models\PurchaseOrderHeader;
use App\Models\StockCard;
use App\Models\Warehouse;
use App\Notifications\GoodsReceiptCreated;
use App\Transformer\Inventory\ItemReceiptTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;
use PDF3;

class ItemReceiptController extends Controller
{
    //
    public function index(){
        return View('admin.inventory.item_receipts.index');
    }

    public function show(ItemReceiptHeader $item_receipt){
        $header = $item_receipt;

        return View('admin.inventory.item_receipts.show', compact('header'));
    }

    public function beforeCreate(){
        return View('admin.inventory.item_receipts.before_create');
    }

    public function create(){
        $purchaseOrder = null;
        if(empty(request()->po)){
            return redirect()->route('admin.item_receipts.before_create');
        }

        $purchaseOrder = PurchaseOrderHeader::find(request()->po);

        // Validate PO approval
        if($purchaseOrder->is_approved === 0){
            return redirect()->route('admin.item_receipts.before_create');
        }

        // Validate GR exists
        if($purchaseOrder->is_all_received === 1){
            return redirect()->route('admin.item_receipts.before_create');
        }

        $deliveries = DeliveryOrderHeader::all();

        $user = Auth::user();
        $sysNo = NumberingSystem::where('doc_id', '2')->first();
        $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumber($docCode, $sysNo->next_no);
        $warehouse = Warehouse::where('id', '!=', 0)->get();

        $data = [
            'purchaseOrder' => $purchaseOrder,
            'warehouse'     => $warehouse,
            'deliveries'    => $deliveries,
            'autoNumber'    => $autoNumber
        ];

        return View('admin.inventory.item_receipts.create')->with($data);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'code'          => 'max:40',
            'date'          => 'required',
            'po_code'       => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate warehouse
        if(!$request->filled('warehouse') || $request->input('warehouse') === '-1'){
            return redirect()->back()->withErrors('Mohon pilih gudang!', 'default')->withInput($request->all());
        }

        // Validate details
        $items = Input::get('item');
        $qtys = Input::get('qty');
        $valid = true;
        $i = 0;
        $purchaseOrderCode = Input::get('po_code');
        $qty = Input::get('qty');

        foreach($items as $item){
            if(empty($item)) $valid = false;
            if(empty($qtys[$i]) || $qtys[$i] == '0') $valid = false;

            $i++;
        }

        if(!$valid){
            return redirect()->back()->withErrors('Kuantitas inventory wajib diisi!', 'default')->withInput($request->all());
        }

        // Check duplicate inventory
        $valid = Utilities::arrayIsUnique($items);
        if(!$valid){
            return redirect()->back()->withErrors('Detail inventory tidak boleh kembar!', 'default')->withInput($request->all());
        }

        $validPo = true;
        $validQtyPo = true;
        $i = 0;
        $purchaseOrder = PurchaseOrderHeader::where('code', $purchaseOrderCode)->first();

        foreach ($items as $item){
            //Check Data
            //Data Check with PO
            $detail = $purchaseOrder->purchase_order_details->where('item_id', $item)->first();

            if($detail == null || $detail->count() == 0){
                $validPo = false;
            }
            else{
                $notReceivedQty = $detail->quantity - $detail->received_quantity;
                if($qty[$i] > $notReceivedQty){
                    $validQtyPo = false;
                }
            }
            $i++;
        }

        if(!$validPo){
            return redirect()->back()->withErrors('Inventory tidak ada dalam PO!', 'default')->withInput($request->all());
        }
        if(!$validQtyPo){
            return redirect()->back()->withErrors('Kuantitas barang GR tidak boleh melebihi barang PO yang sudah diterima!', 'default')->withInput($request->all());
        }

        $user = Auth::user();

        // Generate auto number
        if(Input::get('auto_number')){
            $sysNo = NumberingSystem::where('doc_id', '2')->first();
            $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
            $itemReceiptNumber = Utilities::GenerateNumber($docCode, $sysNo->next_no);

            // Check existing number
            $check = ItemReceiptHeader::where('code', $itemReceiptNumber)->first();
            if($check != null){
                return redirect()->back()->withErrors('Nomor Goods Receipt sudah terdaftar!', 'default')->withInput($request->all());
            }

            $sysNo->next_no++;
            $sysNo->save();
        }
        else{
            if(empty(Input::get('code'))){
                return redirect()->back()->withErrors('Nomor Goods Receipt Wajib Diisi!', 'default')->withInput($request->all());
            }

            $itemReceiptNumber = Input::get('code');

            // Check existing number
            $check = ItemReceiptHeader::where('code', $itemReceiptNumber)->first();
            if($check != null){
                return redirect()->back()->withErrors('Nomor Goods Receipt sudah terdaftar!', 'default')->withInput($request->all());
            }
        }

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $date = Carbon::parse(Input::get('date'));

        // Get lead time
        $mrHeader = $purchaseOrder->purchase_request_header->material_request_header;
        $mrCreatedDate = Carbon::parse($mrHeader->date);

        $itemReceiptHeader = ItemReceiptHeader::create([
            'code'                  => $itemReceiptNumber,
            'site_id'               => $user->employee->site_id,
            'date'                  => $date->toDateString(),
            'purchase_order_id'     => $purchaseOrder->id,
            'warehouse_id'          => Input::get('warehouse'),
            'delivery_order_vendor' => Input::get('delivery_order'),
            'lead_time'             => $mrCreatedDate->diffInDays($now),
            'status_id'             => 1,
            'created_by'            => $user->id,
            'updated_by'            => $user->id,
            'created_at'            => $now->toDateTimeString(),
        ]);

        // Create Item Receipt Detail
        $remark = Input::get('remark');

        $updatedMrId = 0;
        $idx = 0;
        foreach($items as $item){
            if(!empty($item)){
                $qtyInt = (int) $qty[$idx];

                $itemReceiptDetail = ItemReceiptDetail::create([
                    'header_id'         => $itemReceiptHeader->id,
                    'item_id'           => $item,
                    'remark'            => $remark[$idx],
                    'quantity'          => $qtyInt
                ]);

                if(!empty($remark[$idx])) $itemReceiptDetail->remark = $remark[$idx];
                $itemReceiptDetail->save();

                // Check MR service
                $stockResultWarehouse = 0;
                if($purchaseOrder->purchase_request_header->material_request_header->type != 4){
                    // Update Stock
                    $itemStockData = ItemStock::where('item_id', $item)
                        ->where('warehouse_id',Input::get('warehouse'))
                        ->first();

                    if(!empty($itemStockData)){
                        $itemStockData->stock = $itemStockData->stock + $qtyInt;
                        $itemStockData->stock_on_order -= (double) $qtyInt;
                        $itemStockData->save();

                        $stockResultWarehouse = $itemStockData->stock;
                    }
                    else{
                        $newStock = new ItemStock();
                        $newStock->warehouse_id = $request->input('warehouse');
                        $newStock->item_id = $item;
                        $newStock->stock = $qtyInt;
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
                }

                // Flagging related PO detail
                $detail = $purchaseOrder->purchase_order_details->where('item_id', $item)->first();
                $detail->received_quantity = $detail->received_quantity + $qtyInt;
                $detail->save();

                if($purchaseOrder->purchase_request_header->material_request_header->type != 4){
                    $itemData = Item::find($item);

                    // Penghitungan Value
                    if($itemData->stock == 0){
                        $itemData->value = $detail->price;
                    }
                    else{
                        // Count average
                        $oldValue = $itemData->stock * $itemData->value;
                        $newValue = $qtyInt * $detail->price;
                        $averageValue = ($oldValue + $newValue) / ($itemData->stock + $qtyInt);
                        $itemData->value = round($averageValue);
                    }

                    //Penghitungan Stock
                    $itemData->stock += $qtyInt;

                    //Penghitungan Stock On Order
                    if($itemData->stock_on_order >= $qtyInt){
                        $itemData->stock_on_order -= (double) $qtyInt;
                    }

                    $itemData->save();

                    // Stock Card
                    StockCard::create([
                        'item_id'               => $item,
                        'reference'             => 'Goods Receipt ' . $itemReceiptHeader->code,
                        'in_qty'                => $qtyInt,
                        'out_qty'               => 0,
                        'result_qty'            => $itemData->stock,
                        'result_qty_warehouse'  => $stockResultWarehouse,
                        'warehouse_id'          => $request->input('warehouse'),
                        'created_by'            => $user->id,
                        'created_at'            => $now->toDateTimeString(),
                        'updated_by'            => $user->id,
                        'updated_at'            => $now->toDateTimeString()
                    ]);
                }

                // Update MR
                $mrDetail = $purchaseOrder->purchase_request_header->material_request_header->material_request_details->where('item_id', $item)->first();
                if(!empty($mrDetail)){
                    $mrDetail->quantity_received += $qtyInt;
                    $mrDetail->save();

                    $updatedMrId = $purchaseOrder->purchase_request_header->material_request_id;
                }
            }
            $idx++;
        }

        if($updatedMrId > 0){
            // Flag unsynced MR
            DB::table('material_request_headers')
                ->where('id', '=', $updatedMrId)
                ->update(['is_synced' => 0]);
        }

        // Check MR purpose
        $mrHeader = $purchaseOrder->purchase_request_header->material_request_header;
        if($mrHeader->purpose === 'stock'){
            $isStockReceived = true;
            foreach($mrHeader->material_request_details as $detail){
                if($detail->quantity > $detail->quantity_received){
                    $isStockReceived = false;
                }
            }

            if($isStockReceived){
                $mrHeader->status_id = 4;
                $mrHeader->save();
            }
        }

        // Check all received PO details
        $isAllReceived = true;
        foreach ($purchaseOrder->purchase_order_details as $detail){
            if($detail->quantity > $detail->received_quantity){
                $isAllReceived = false;
            }
        }

        if($isAllReceived){
            $purchaseOrder->is_all_received = 1;
            $purchaseOrder->save();
        }

        // Check partial received PO details
        if(!$isAllReceived){
            $isPartialReceived = false;
            foreach ($purchaseOrder->purchase_order_details as $detail){
                if($detail->received_quantity > 0){
                    $isPartialReceived = true;
                }
            }

            if($isPartialReceived){
                $purchaseOrder->is_all_received = 2;
                $purchaseOrder->save();
            }
        }

        try{
            // Send notification
            $mrCreator = $itemReceiptHeader->purchase_order_header->purchase_request_header->material_request_header->createdBy;
            $mrCreator->notify(new GoodsReceiptCreated($itemReceiptHeader, 'true', 'false'));

            $prCreator = $itemReceiptHeader->purchase_order_header->purchase_request_header->createdBy;
            $prCreator->notify(new GoodsReceiptCreated($itemReceiptHeader, 'false', 'true'));

            $roles = Role::where('id', 13)->get();
            foreach($roles as $role){
                $users =  $role->users()->get();
                if($users->count() > 0){
                    foreach ($users as $notifiedUser){
                        if($notifiedUser->id !== $mrCreator->id && $notifiedUser->id !== $prCreator->id){
                            $notifiedUser->notify(new GoodsReceiptCreated($itemReceiptHeader, 'false', 'false'));
                        }
                    }
                }
            }
        }
        catch (\Exception $ex){
            error_log($ex);
        }

        Session::flash('message', 'Berhasil membuat Goods Receipt!');

        return redirect()->route('admin.item_receipts.show', ['item_receipts' => $itemReceiptHeader]);
    }

    public function edit($id){
        $header = ItemReceiptHeader::find($id);

        return View('admin.inventory.item_receipts.edit', compact('header'));
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(),[
            'date'          => 'required',
            'no_sj_spb'     => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = \Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $formatedDate = Carbon::createFromFormat('d M Y', Input::get('date'), 'Asia/Jakarta');

        $itemReceiptHeader = ItemReceiptHeader::find($id);

        if(!empty(Input::get('delivery_order'))){
            $itemReceiptHeader->delivery_order_id = Input::get('delivery_order');
        }

        $itemReceiptHeader->date = $formatedDate->toDateTimeString();
        $itemReceiptHeader->delivered_from = Input::get('delivered_from');
        $itemReceiptHeader->angkutan = Input::get('angkutan');
        $itemReceiptHeader->updated_by = $user->id;
        $itemReceiptHeader->updated_at = $now->toDateTimeString();
        $itemReceiptHeader->save();

        Session::flash('message', 'Berhasil mengubah Goods Receipt!');

        return redirect()->route('admin.item_receipts.edit', ['item_receipts' => $itemReceiptHeader->id]);
    }

    public function delete(){

    }

    public function getIndex(){
        $itemReceipts = ItemReceiptHeader::all();
        return DataTables::of($itemReceipts)
            ->setTransformer(new ItemReceiptTransformer())
            ->addIndexColumn()
            ->make(true);
    }

    public function getItemReceipts(Request $request){
        $term = trim($request->q);
        $itemReceipts = ItemReceiptHeader::where('code', 'LIKE', '%'. $term. '%')
            ->get();

        $formatted_tags = [];

        foreach ($itemReceipts as $itemReceipt) {
            $formatted_tags[] = ['id' => $itemReceipt->id, 'text' => $itemReceipt->code];
        }

        return \Response::json($formatted_tags);
    }

    public function printDocument($id){
        $itemReceipt = ItemReceiptHeader::find($id);
        $itemReceiptDetails = ItemReceiptDetail::where('header_id', $itemReceipt->id)->get();

        $itemTotal = 0;
        foreach($itemReceiptDetails as $detail){
            $itemTotal += $detail->quantity;
        }

        return view('documents.item_receipts.item_receipts', compact('itemReceipt', 'itemReceiptDetails', 'itemTotal'));
    }

    public function report(){
        $departments = Department::all();

        return View('admin.inventory.item_receipts.report', compact('departments'));
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

        $grHeaders = ItemReceiptHeader::whereBetween('date', array($start->toDateTimeString(), $end->toDateTimeString()));

        // Filter departemen
        $filterDepartment = 'Semua';
        $department = $request->input('department');
        if($department != '0'){
            $grHeaders = $grHeaders->whereHas('purchase_order_header', function ($query) use ($department){
                $query->whereHas('purchase_request_header', function ($query) use ($department){
                    $query->where('department_id', $department);
                });
            });;
            $filterDepartment = Department::find($department)->name;
        }

        $grHeaders = $grHeaders->orderByDesc('date')
            ->get();

        // Validate Data
        if($grHeaders->count() == 0){
            return redirect()->back()->withErrors('Data tidak ditemukan!', 'default')->withInput($request->all());
        }

        if($request->input('is_preview') === 'false'){
            $data =[
                'grHeaders'         => $grHeaders,
                'start_date'        => $request->input('start_date'),
                'finish_date'       => $request->input('end_date'),
                'filterDepartment'  => $filterDepartment
            ];

            //return view('documents.material_requests.material_requests_pdf')->with($data);

            $now = Carbon::now('Asia/Jakarta');
            $filename = 'PURCHASE_INVOICE_REPORT_' . $now->toDateTimeString();

            $pdf = PDF3::loadView('documents.item_receipts.item_receipts_pdf', $data)
                ->setOption('footer-right', '[page] of [toPage]');

            return $pdf->download($filename.'.pdf');
        }
        else{
            $data =[
                'grHeaders'         => $grHeaders,
                'start_date'        => $request->input('start_date'),
                'finish_date'       => $request->input('end_date'),
                'filterDepartment'  => $filterDepartment,
                'department'        => $request->input('department')
            ];

            return view('documents.item_receipts.item_receipts_pdf_preview')->with($data);
        }
    }

    public function download($id){
        $itemReceipt = ItemReceiptHeader::find($id);
        $itemReceiptDetails = ItemReceiptDetail::where('header_id', $itemReceipt->id)->get();

        $pdf = PDF::loadView('documents.item_receipts.item_receipts_doc', ['itemReceipt' => $itemReceipt, 'itemReceiptDetails' => $itemReceiptDetails])->setPaper('A4');
        $now = Carbon::now('Asia/Jakarta');
        $filename = $itemReceipt->code. '_' . $now->toDateTimeString();

        return $pdf->download($filename.'.pdf');
    }
}
