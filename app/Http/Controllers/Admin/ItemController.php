<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 25/01/2018
 * Time: 10:15
 */

namespace App\Http\Controllers\Admin;


use App\Exports\InventoryMasterExport;
use App\Http\Controllers\Controller;
use App\Libs\Utilities;
use App\Models\DeliveryOrderDetail;
use App\Models\Group;
use App\Models\IssuedDocketDetail;
use App\Models\Item;
use App\Models\ItemMutation;
use App\Models\ItemReceiptDetail;
use App\Models\ItemStock;
use App\Models\ItemStockNotification;
use App\Models\PermissionMenu;
use App\Models\PurchaseInvoiceHeader;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseRequestDetail;
use App\Models\QuotationDetail;
use App\Models\ReturDetail;
use App\Models\StockAdjustment;
use App\Models\StockCard;
use App\Models\StockIn;
use App\Models\Uom;
use App\Models\Warehouse;
use App\Transformer\MasterData\ItemTransformer;
use App\Transformer\Notification\ItemStockNotificationTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use PDF;

class ItemController extends Controller
{
    public function index(Request $request){

        $filterStock = '1';
        if($request->stock != null){
            $filterStock = $request->stock;
        }

        // Get role
        $isSite = false;
        $user = Auth::user();
        $roleId = $user->roles->pluck('id')[0];
        if($roleId === 17 || $roleId === 18 || $roleId === 21){
            $isSite = true;
        }

        return View('admin.items.index', compact('filterStock', 'isSite'));
    }

    public function indexStockNotification(){
        return View('admin.items.stock_notification_index');
    }

    public function show(Item $item){
        $selectedItem = $item;

        $user = Auth::user();
        $site = $user->employee->site;

        if($site->id === 1){
            $itemStocks = ItemStock::where('item_id', $item->id)->get();
        }
        else{
            $warehouseIds = array();
            foreach($site->warehouses as $warehouse){
                array_push($warehouseIds, $warehouse->id);
            }

            $itemStocks = ItemStock::where('item_id', $item->id)
                ->whereIn('warehouse_id', $warehouseIds)
                ->get();
        }

        $data =[
            'selectedItem'      => $selectedItem,
            'itemStocks'        => $itemStocks
        ];

        return View('admin.items.show')->with($data);
    }

    public function create(){
        $warehouses = Warehouse::where('id', '!=', 0)->get();
        $groups = Group::where('id', '!=', 0)->get();

        $data = [
            'warehouses'    => $warehouses,
            'groups'        => $groups
        ];

        return View('admin.items.create')->with($data);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'code'          => 'required|max:45|unique:items',
            'name'          => 'required|max:100',
            'uom'           => 'required|max:30',
            'part_number'   => 'max:45',
            'description'   => 'max:200'
        ],[
            'code.unique'   => 'Kode inventory telah terpakai'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        if($request->input('group') === '-1'){
            return redirect()->back()->withErrors('Pilih Kategori Inventory!', 'default')->withInput($request->all());
        }

        // Validate warehouse
        $warehouses = $request->input('warehouse');
        $qtys = $request->input('qty');
        $locations = $request->input('location');
        $minStocks = $request->input('min');
        $maxStocks = $request->input('max');
        $warnings = $request->input('warning');
        $isStockEntry = false;
        if(count($warehouses) > 0){
            $idx = 0;
            foreach($warehouses as $warehouse){

                if(!empty($qtys[$idx])){
                    $qtyTmp = (int) $qtys[$idx];
                    if($qtyTmp > 0){
                        $isStockEntry = true;
                    }
                }
                $idx++;
            }

            // Validate duplicated warehouse values
            $valid = Utilities::arrayIsUnique($warehouses);
            if(!$valid){
                return redirect()->back()->withErrors('Detail Gudang tidak boleh kembar!', 'default')->withInput($request->all());
            }
        }

        if($isStockEntry){
            // Validate value
            if(!$request->filled('valuation')){
                return redirect()->back()->withErrors('Apabila stok awal diisi, maka wajib isi Nilai Beli per UOM!', 'default')->withInput($request->all());
            }
        }
        else{
            // Validate value
            if($request->filled('valuation')){
                return redirect()->back()->withErrors('Apabila stok awal tidak diisi, maka nilai beli awal tidak boleh diisi!', 'default')->withInput($request->all());
            }
        }

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');

        $item = Item::create([
            'name'                  => $request->input('name'),
            'code'                  => $request->input('code'),
            'part_number'           => $request->input('part_number'),
            'uom'                   => $request->input('uom'),
            'group_id'              => $request->input('group'),
            'machinery_type'        => $request->input('machinery_type'),
            'stock'                 => 0,
            'value'                 => 0,
            'stock_minimum'         => 0,
            'stock_notification'    => 0,
            'created_by'            => $user->id,
            'created_at'            => $now->toDateTimeString(),
            'updated_by'            => $user->id,
            'updated_at'            => $now
        ]);

        if($request->filled('valuation') && $request->input('valuation') != "0"){
            $value = Utilities::toFloat($request->input('valuation'));
            $item->value = $value;
        }

//        $item->stock_minimum = 0;
//        if($request->filled('stock_min')){
//            $item->stock_minimum = $request->input('stock_min');
//        }
//
//        $item->stock_notification = 0;
//        if($request->filled('stock_notif')){
//            $item->stock_notification = 1;
//        }

        if($request->filled('description')){
            $item->description = $request->input('description');
        }

        $item->save();

        // Add new item stock notification
//        if($request->filled('stock_notif')){
//
//            if(!ItemStockNotification::where('item_id', $item->id)->exists()){
//                ItemStockNotification::create([
//                    'item_id'       => $item->id,
//                    'created_at'    => $now->toDateTimeString(),
//                    'created_by'    => $user->id
//                ]);
//            }
//        }

        // Get stock
        if(count($warehouses) > 0){
            $idx = 0;
            $totalStock = 0;
            foreach($warehouses as $warehouse){
                if(!empty($warehouse)){
                    $qtyInt = (int) $qtys[$idx];
                    $minStockInt = (int) $minStocks[$idx];
                    $maxStockInt = (int) $maxStocks[$idx];

                    if($qtyInt > 0 ||
                        $minStockInt > 0 ||
                        $maxStockInt > 0 ||
                        $warnings[$idx] === '1'){

                        $itemStock = ItemStock::where('item_id', $item->id)
                            ->where('warehouse_id', $warehouse)
                            ->first();
                        if(empty($itemStock)){
                            $itemStockAdd = ItemStock::create([
                                'item_id'           => $item->id,
                                'warehouse_id'      => $warehouse,
                                'stock'             => $qtyInt,
                                'stock_min'         => $minStockInt,
                                'stock_max'         => $maxStockInt,
                                'is_stock_warning'  => $warnings[$idx] === '1' ? true: false,
                                'created_by'        => $user->id,
                                'created_at'        => $now->toDateTimeString()
                            ]);

                            if(!empty($locations[$idx])){
                                $itemStockAdd->location = $locations[$idx];
                                $itemStockAdd->save();
                            }

                            $stockId = $itemStockAdd->id;
                        }
                        else{
                            $itemStock->stock_min = $minStockInt;
                            $itemStock->stock_max = $maxStockInt;
                            $itemStock->is_stock_warning = $warnings[$idx] === '1' ? true: false;

                            if(!empty($locations[$idx])){
                                $itemStock->location = $locations[$idx];
                            }

                            $itemStock->save();

                            $stockId = $itemStock->id;
                        }

                        $totalStock += $qtyInt;

                        if($qtyInt > 0){
                            // Create stock card
                            StockCard::create([
                                'item_id'               => $item->id,
                                'warehouse_id'          => $warehouse,
                                'in_qty'                => $qtyInt,
                                'out_qty'               => 0,
                                'result_qty'            => $totalStock,
                                'result_qty_warehouse'  => $qtyInt,
                                'reference'             => "Pembuatan Item Baru",
                                'created_by'            => $user->id,
                                'created_at'            => $now->toDateTimeString(),
                                'updated_by'            => $user->id,
                                'updated_at'            => $now->toDateTimeString()
                            ]);
                        }

                        if($warnings[$idx] === '1'){
                            $stockNotif = ItemStockNotification::where('item_stock_id', $stockId)->first();
                            if(empty($stockNotif)){
                                ItemStockNotification::create([
                                    'item_id'       => $item->id,
                                    'item_stock_id' => $stockId,
                                    'warehouse_id'  => $warehouse,
                                    'created_at'    => $now->toDateTimeString(),
                                    'created_by'    => $user->id
                                ]);
                            }
                        }
                    }
                }
                $idx++;
            }

            $item->stock = $totalStock;
            $item->save();
        }

        Session::flash('message', 'Berhasil membuat data barang baru!');

        if($request->input('is_repeat') === '1'){
            return redirect()->route('admin.items.create');
        }
        else{
            return redirect()->route('admin.items');
        }
    }

    public function edit(Item $item){
        $warehouses = Warehouse::all();
        $groups = Group::all();

        $itemId = $item->id;
//        $isPrUsed = PurchaseRequestDetail::where('item_id', $itemId)->exists();
//        $isRfqUsed = QuotationDetail::where('item_id', $itemId)->exists();
//        $isPoUsed = PurchaseOrderDetail::where('item_id', $itemId)->exists();
//        $isGrUsed = ItemReceiptDetail::where('item_id', $itemId)->exists();
//        $isIdUsed = IssuedDocketDetail::where('item_id', $itemId)->exists();
//        $isDoUsed = DeliveryOrderDetail::where('item_id', $itemId)->exists();
//        $isReturUsed = ReturDetail::where('item_id', $itemId)->exists();
//
//        // Check stock usage
//        $isStockInUsed = StockIn::where('item_id', $itemId)->exists();
//        $isItemMutationUsed = ItemMutation::where('item_id', $itemId)->exists();
//
//        $isUsed = false;
//        if($isPrUsed || $isRfqUsed || $isPoUsed || $isGrUsed || $isIdUsed || $isDoUsed || $isReturUsed  || $isStockInUsed || $isItemMutationUsed){
//            $isUsed = true;
//        }

        $user = Auth::user();

        $site = $user->employee->site;

        if($site->id === 1){
            $itemStocks = ItemStock::where('item_id', $item->id)->get();
        }
        else{
            $warehouseIds = array();
            foreach($site->warehouses as $warehouse){
                array_push($warehouseIds, $warehouse->id);
            }

            $itemStocks = ItemStock::where('item_id', $item->id)
                ->whereIn('warehouse_id', $warehouseIds)
                ->get();
        }

        $data = [
            'item'          => $item,
            'warehouses'    => $warehouses,
            'groups'        => $groups,
            'itemStocks'    => $itemStocks
        ];

        return View('admin.items.edit')->with($data);
    }

    public function update(Request $request, Item $item){
        $validator = Validator::make($request->all(),[
            'name'          => 'required|max:100',
            'part_number'   => 'max:45',
            'description'   => 'max:200'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');

        $item->name = $request->input('name');
        $item->part_number = $request->input('part_number');
        $item->uom = $request->input('uom');
        $item->group_id = $request->input('group');
//        $item->stock_minimum = $request->input('stock_min');
//        $item->stock_notification = $request->filled('stock_notif') ? 1 : 0;
        $item->description = $request->input('description');
        $item->updated_by = $user->id;
        $item->updated_at = $now;

//        if($request->input('is_used') === '0'){
//            if($request->filled('valuation')){
//                $value = Utilities::toFloat($request->input('valuation'));
//                $item->value = $value;
//            }
//            else{
//                $item->value = null;
//            }
//        }

        $item->save();

        // Delete item stock notification
//        if(!$request->filled('stock_notif')){
//            $itemNotification = $item->item_stock_notifications->first();
//            if(!empty($itemNotification)){
//                $itemNotification->delete();
//            }
//        }
//        else{
//            if(!ItemStockNotification::where('item_id', $item->id)->exists()){
//                ItemStockNotification::create([
//                    'item_id'       => $item->id,
//                    'created_at'    => $now->toDateTimeString(),
//                    'created_by'    => $user->id
//                ]);
//            }
//        }

        Session::flash('message', 'Berhasil mengubah data barang!');

        return redirect()->route('admin.items.edit', ['item' => $item]);
    }

    public function destroy(Request $request)
    {
        try{
            $itemId = $request->input('id');

            // Check document usage
            $isPrUsed = PurchaseRequestDetail::where('item_id', $itemId)->exists();
            $isRfqUsed = QuotationDetail::where('item_id', $itemId)->exists();
            $isPoUsed = PurchaseOrderDetail::where('item_id', $itemId)->exists();
            $isGrUsed = ItemReceiptDetail::where('item_id', $itemId)->exists();
            $isIdUsed = IssuedDocketDetail::where('item_id', $itemId)->exists();
            $isDoUsed = DeliveryOrderDetail::where('item_id', $itemId)->exists();
            $isReturUsed = ReturDetail::where('item_id', $itemId)->exists();

            // Check stock usage
            $isStockAdjustmentUsed = StockAdjustment::where('item_id', $itemId)->exists();
            $isStockInUsed = StockIn::where('item_id', $itemId)->exists();
            $isItemMutationUsed = ItemMutation::where('item_id', $itemId)->exists();

            $isUsed = false;
            if($isPrUsed || $isRfqUsed || $isPoUsed || $isGrUsed || $isIdUsed || $isDoUsed || $isReturUsed || $isStockAdjustmentUsed || $isStockInUsed || $isItemMutationUsed){
                $isUsed = true;
            }

            if($isUsed){
                return Response::json(array('errors' => 'INVALID'));
            }

            $itemStocks = ItemStock::where('item_id', $itemId)->get();
            foreach($itemStocks as $stock){
                $stock->delete();
            }

            $stockCards = StockCard::where('item_id', $itemId)->get();
            foreach ($stockCards as $stockCard){
                $stockCard->delete();
            }

            $item = Item::find($itemId);
            $item->delete();

            Session::flash('message', 'Berhasil menghapus data barang '. $item->code. ' - '. $item->name);
            return Response::json(array('success' => 'VALID'));
        }
        catch(\Exception $ex){
            return Response::json(array('errors' => 'INVALID'));
        }
    }

    public function getIndex(Request $request){

//        $start = microtime(true);

        $items = Item::query();
        $stock = '0';
        if($request->filled('stock')){
            $stock = $request->input('stock');
            if($stock === '1'){
                $items = $items->where('stock', '>', 0);
            }
            else if($stock === '2'){
                $items = $items->where('stock', 0);
            }
            else{
                $items = $items->where('stock', '>', -1);
            }
        }
        else{
            $items = $items->where('stock', '>', 0);
        }

        $items = $items->where('id', '!=', 0);

//        $time = microtime(true) - $start;
//        error_log($time);

        return (new \Yajra\DataTables\DataTables)->eloquent($items)
            ->setTransformer(new ItemTransformer)
            ->make(true);
    }

    public function getIndexStockNotification(){
        $user = Auth::user();

        $site = $user->employee->site;
        $warehouseIds = array();
        if($site->id === 1){
            $warehouseAll = Warehouse::all();
            foreach($warehouseAll as $warehouse){
                array_push($warehouseIds, $warehouse->id);
            }
        }
        else{
            foreach($site->warehouses as $warehouse){
                array_push($warehouseIds, $warehouse->id);
            }
        }

        $stockWarnings = ItemStockNotification::with('item', 'item_stock')
            ->whereIn('warehouse_id', $warehouseIds)
            ->whereHas('item_stock', function($query){
                $query->whereColumn('item_stocks.stock', '<=', 'item_stocks.stock_min');
            })
            ->get();

        return DataTables::of($stockWarnings)
            ->setTransformer(new ItemStockNotificationTransformer)
            ->addIndexColumn()
            ->make(true);
    }

    public function getItems(Request $request){
        $term = trim($request->q);

        $items = null;
        if(!empty($request->type)){
            $type = $request->type;

            if($type === 'part'){
                $items = Item::where('id', '!=', 0)
                        ->whereHas('group', function($query){
                        $query->where('type', 1);
                    })
                    ->where(function($q) use ($term) {
                        $q->where('code', 'LIKE', '%'. $term. '%')
                            ->orWhere('name', 'LIKE', '%'. $term. '%');
                    })
                    ->get();
            }
            elseif($type === 'fuel'){

                $items = Item::where('id', '!=', 0)
                    ->whereHas('group', function($query){
                        $query->where('type', 2);
                    })
                    ->where(function($q) use ($term) {
                        $q->where('code', 'LIKE', '%'. $term. '%')
                            ->orWhere('name', 'LIKE', '%'. $term. '%');
                    })
                    ->get();
            }
            elseif($type === 'oil'){

                $items = Item::where('id', '!=', 0)
                    ->whereHas('group', function($query){
                        $query->where('type', 3);
                    })
                    ->where(function($q) use ($term) {
                        $q->where('code', 'LIKE', '%'. $term. '%')
                            ->orWhere('name', 'LIKE', '%'. $term. '%');
                    })
                    ->get();
            }
            else{
                $items = Item::where('id', '!=', 0)
                    ->where(function ($q) use ($term) {
                        $q->where('code', 'LIKE', '%' . $term . '%')
                            ->orWhere('name', 'LIKE', '%' . $term . '%');
                    })->get();
            }
        }
        else {
            $items = Item::where('id', '!=', 0)
                ->where(function ($q) use ($term) {
                    $q->where('code', 'LIKE', '%' . $term . '%')
                        ->orWhere('name', 'LIKE', '%' . $term . '%');
                })->get();
        }
        $formatted_tags = [];

        foreach ($items as $item) {
            $createdDate = Carbon::parse($item->created_at)->format('d M Y');
            $formatted_tags[] = ['id' => $item->id, 'text' => $item->code. ' - '. $item->name. ' - '. $createdDate];
        }

        return \Response::json($formatted_tags);
    }

    public function getExtendedItems(Request $request){
        $term = trim($request->q);

        $items = null;
        if(!empty($request->type)){
            $type = $request->type;

            if($type === 'part'){
                $items = Item::where('id', '!=', 0)
                    ->whereHas('group', function($query){
                        $query->where('type', 1);
                    })
                    ->where(function($q) use ($term) {
                        $q->where('code', 'LIKE', '%'. $term. '%')
                            ->orWhere('name', 'LIKE', '%'. $term. '%');
                    })
                    ->get();
            }
            elseif($type === 'fuel'){

                $items = Item::where('id', '!=', 0)
                    ->whereHas('group', function($query){
                        $query->where('type', 2);
                    })
                    ->where(function($q) use ($term) {
                        $q->where('code', 'LIKE', '%'. $term. '%')
                            ->orWhere('name', 'LIKE', '%'. $term. '%');
                    })
                    ->get();
            }
            elseif($type === 'oil'){

                $items = Item::where('id', '!=', 0)
                    ->whereHas('group', function($query){
                        $query->where('type', 3);
                    })
                    ->where(function($q) use ($term) {
                        $q->where('code', 'LIKE', '%'. $term. '%')
                            ->orWhere('name', 'LIKE', '%'. $term. '%');
                    })
                    ->get();
            }
            else{
                $items = Item::where('id', '!=', 0)
                    ->where(function ($q) use ($term) {
                        $q->where('code', 'LIKE', '%' . $term . '%')
                            ->orWhere('name', 'LIKE', '%' . $term . '%');
                    })->get();
            }
        }
        else {
            $items = Item::where('id', '!=', 0)
                ->where(function ($q) use ($term) {
                    $q->where('code', 'LIKE', '%' . $term . '%')
                        ->orWhere('name', 'LIKE', '%' . $term . '%');
                })->get();
        }
        $formatted_tags = [];

        foreach ($items as $item) {
            $createdDate = Carbon::parse($item->created_at)->format('d M Y');
            $partNumber = $item->part_number ?? 'null';
            $formatted_tags[] = ['id' => $item->id. '#'. $item->code. '#'. $item->name. '#'. $item->uom. '#'. $createdDate. '#'. $partNumber, 'text' => $item->code. ' - '. $item->name. ' - '. $createdDate];
        }

        return \Response::json($formatted_tags);
    }

    public function reportInvoice(Item $item){
        // Check access permission
        $user = \Auth::user();
        $roleId = $user->roles->pluck('id')[0];
        if(!PermissionMenu::where('role_id', $roleId)->where('menu_id', 27)->first()){
            return redirect()->back();
        }

        return View('admin.items.report_invoice', compact('item'));
    }

    public function downloadReportInvoice(Request $request) {

        // Validate date
        $isDateFiltered = false;
        if(($request->filled('start_date') && !$request->filled('end_date')) ||
            (!$request->filled('start_date') && $request->filled('end_date'))){
            return redirect()->back()->withErrors('Dari Tanggal tidak boleh lebih besar dari Sampai Tanggal!', 'default')->withInput($request->all());
        }
        if($request->filled('start_date') && $request->filled('end_date')){
            $isDateFiltered = true;

            $start = Carbon::createFromFormat('d M Y', $request->input('start_date'), 'Asia/Jakarta');
            $end = Carbon::createFromFormat('d M Y', $request->input('end_date'), 'Asia/Jakarta');

            // Validate date
            if($start->gt($end)){
                return redirect()->back()->withErrors('Dari Tanggal tidak boleh lebih besar dari Sampai Tanggal!', 'default')->withInput($request->all());
            }
        }

        $piHeaders = PurchaseInvoiceHeader::whereHas('purchase_invoice_details', function($query) use ($request){
                    $query->where('item_id', $request->input('item'));
                });

        if($isDateFiltered){
            $start = Carbon::createFromFormat('d M Y', $request->input('start_date'), 'Asia/Jakarta');
            $end = Carbon::createFromFormat('d M Y', $request->input('end_date'), 'Asia/Jakarta');

            $start = $start->addDays(-1);
            $end = $end->addDays(1);

            $piHeaders = $piHeaders->whereBetween('date', array($start->toDateTimeString(), $end->toDateTimeString()));
        }

        $piHeaders = $piHeaders->orderByDesc('date')
            ->get();

        // Validate Data
        if($piHeaders->count() == 0){
            return redirect()->back()->withErrors('Data tidak ditemukan!', 'default')->withInput($request->all());
        }

//        $total = $piHeaders->sum('total_payment');
//        $totalStr = number_format($total, 0, ",", ".");

        $item = Item::find($request->input('item'));

        $data = [
            'piHeaders'     => $piHeaders,
            'startDate'     => $isDateFiltered ? $request->input('start_date') : null,
            'endDate'       => $isDateFiltered ? $request->input('end_date') : null,
            'item'          => $item
        ];

//        return view('documents.items.item_purchase_report')->with($data);

        $pdf = PDF::loadView('documents.items.item_purchase_report', $data)
            ->setPaper('a4', 'portrait');
        $now = Carbon::now('Asia/Jakarta');
        $filename = 'ITEM_PURCHASE_REPORT_' . $now->toDateTimeString();
        $pdf->setOptions(["isPhpEnabled"=>true]);

        return $pdf->download($filename.'.pdf');
    }

    public function excel(){
        $user = Auth::user();
        $site = $user->employee->site;

        $categories = Group::orderBy('name')->get();

        $data = [
            'categories'    => $categories
        ];

        return View('admin.items.excel')->with($data);
    }

    public function downloadExcel(Request $request){

        $now = Carbon::now('Asia/Jakarta');
        $filename = 'INVENTORY_MASTER_'. $now->toDateTimeString(). '.xlsx';

        // Get role
        $isSite = false;
        $user = Auth::user();
        $roleId = $user->roles->pluck('id')[0];
        if($roleId === 17 || $roleId === 18 || $roleId === 21){
            $isSite = true;
        }

        // Filter category
        $categoryId = $request->input('category');

        return (new InventoryMasterExport($isSite, $categoryId))->download($filename);
    }
}