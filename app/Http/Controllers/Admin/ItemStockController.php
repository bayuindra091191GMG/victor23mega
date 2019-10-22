<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 15/03/2018
 * Time: 11:27
 */

namespace App\Http\Controllers\Admin;


use App\Exports\InventoryStockExport;
use App\Http\Controllers\Controller;
use App\Models\DeliveryOrderDetail;
use App\Models\IssuedDocketDetail;
use App\Models\Item;
use App\Models\ItemMutation;
use App\Models\ItemReceiptDetail;
use App\Models\ItemStock;
use App\Models\ItemStockNotification;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseRequestDetail;
use App\Models\QuotationDetail;
use App\Models\ReturDetail;
use App\Models\StockAdjustment;
use App\Models\StockCard;
use App\Models\StockIn;
use App\Models\Warehouse;
use App\Transformer\Inventory\ItemStockTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use PDF2;
use PDF3;
use Yajra\DataTables\DataTables;

class ItemStockController extends Controller
{
    public function index(Request $request){
        $user = Auth::user();
        $site = $user->employee->site;

        $filterWarehouse = '-1';
        if($request->warehouse != null){
            $filterWarehouse = $request->warehouse;
        }
        else{
            $filterWarehouse = ''. $site->warehouses->first()->id;
        }

        if($site->id === 1){
            $warehouses = Warehouse::orderBy('name')->get();
        }
        else{
            $warehouses = Warehouse::where('site_id', $site->id)
                ->orderBy('name')
                ->get();
        }

        $filterStock = '0';
        if($request->stock != null){
            $filterStock = $request->stock;
        }

        $filterMovement = 'ALL';
        if($request->movement != null){
            $filterMovement = $request->movement;
        }

        $data = [
            'warehouses'        => $warehouses,
            'site'              => $site->id,
            'filterWarehouse'   => $filterWarehouse,
            'filterStock'       => $filterStock,
            'filterMovement'    => $filterMovement
        ];

        return View('admin.inventory.item_stocks.index')->with($data);
    }

    public function create(Item $item){
        try{

            $user = Auth::user();

            $site = $user->employee->site;

            if($site->id === 1){
                $warehouses = Warehouse::all();
            }
            else{
                $warehouseIds = array();
                foreach($site->warehouses as $warehouse){
                    array_push($warehouseIds, $warehouse->id);
                }

                $warehouses = Warehouse::whereIn('id', $warehouseIds)->get();
            }

            $data = [
                'item'          => $item,
                'warehouses'    => $warehouses
            ];

            return View('admin.inventory.item_stocks.create')->with($data);
        }
        catch (\Exception $ex){
            error_log($ex);
        }
    }

    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'location'      => 'max:50'
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            if($request->input('warehouse') === '-1'){
                return redirect()->back()->withErrors('Pilih Gudang!', 'default')->withInput($request->all());
            }

            // Validate warehouse exists in item stocks
            $itemStockValidate = ItemStock::where('item_id', $request->input('item_id'))
                ->where('warehouse_id', $request->input('warehouse'))
                ->first();
            if(!empty($itemStockValidate)){
                return redirect()->back()->withErrors('Gudang yang dipilih sudah terdaftar dalam sistem!', 'default')->withInput($request->all());
            }

            $user = Auth::user();
            $now = Carbon::now('Asia/Jakarta');

            $item_stock = new ItemStock();
            $item_stock->item_id = $request->input('item_id');
            $item_stock->warehouse_id = $request->input('warehouse');
            $item_stock->stock = 0;

            if($request->filled('location')){
                $item_stock->location = $request->input('location');
            }

            $item_stock->stock_min = $request->input('stock_min');
            $item_stock->stock_max = $request->input('stock_max');
            $item_stock->is_stock_warning = $request->filled('stock_warning') ? true : false;

            $item_stock->created_by = $user->id;
            $item_stock->created_at = $now->toDateTimeString();
            $item_stock->save();

            if($request->filled('stock_warning')) {
                $itemNotification = $item_stock->item_stock_notifications->first();
                if(empty($itemNotification)){
                    ItemStockNotification::create([
                        'item_id'       => $item_stock->item_id,
                        'item_stock_id' => $item_stock->id,
                        'warehouse_id'  => $item_stock->warehouse_id,
                        'created_at'    => $now->toDateTimeString(),
                        'created_by'    => $user->id
                    ]);
                }
            }

            Session::flash('message', 'Berhasil membuat baru pengaturan inventory!');

            return redirect()->route('admin.item_stocks.option.edit', ['item_stock' => $item_stock->id]);
        }
        catch(\Exception $ex){
            error_log($ex);
        }
    }

//    public function store(Request $request){
//        try{
//            $validator = Validator::make($request->all(),[
//                'warehouse_id'      => 'required',
//                'stock'             => 'required'
//            ]);
//
//            if ($validator->fails()) {
//                return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
//            }
//
//            $warehouseId = $request->input('warehouse_id');
//            $itemId = $request->input('item_id');
//            $stockAdd = (int) $request->input('stock');
//
//            // Check document usage
//            $isPrUsed = PurchaseRequestDetail::where('item_id', $itemId)->exists();
//            $isRfqUsed = QuotationDetail::where('item_id', $itemId)->exists();
//            $isPoUsed = PurchaseOrderDetail::where('item_id', $itemId)->exists();
//            $isGrUsed = ItemReceiptDetail::where('item_id', $itemId)->exists();
//            $isIdUsed = IssuedDocketDetail::where('item_id', $itemId)->exists();
//            $isDoUsed = DeliveryOrderDetail::where('item_id', $itemId)->exists();
//            $isReturUsed = ReturDetail::where('item_id', $itemId)->exists();
//
//            // Check stock usage
//            $isStockAdjustmentUsed = StockAdjustment::where('item_id', $itemId)->exists();
//            $isStockInUsed = StockIn::where('item_id', $itemId)->exists();
//            $isItemMutationUsed = ItemMutation::where('item_id', $itemId)->exists();
//
//            $isUsed = false;
//            if($isPrUsed || $isRfqUsed || $isPoUsed || $isGrUsed || $isIdUsed || $isDoUsed || $isReturUsed || $isStockAdjustmentUsed || $isStockInUsed || $isItemMutationUsed){
//                $isUsed = true;
//            }
//
//            if($isUsed){
//                return Response::json(array('errors' => 'used'));
//            }
//
//            $user = \Auth::user();
//            $now = Carbon::now('Asia/Jakarta');
//
//            // Check exists
//            $stock = ItemStock::where('item_id', $itemId)
//                ->where('warehouse_id', $warehouseId)
//                ->first();
//            if(!empty($stock)){
//                return Response::json(array('errors' => 'exists'));
//            }
//            else{
//                $stock = ItemStock::create([
//                    'item_id'       => $itemId,
//                    'warehouse_id'  => $warehouseId,
//                    'stock'         => $stockAdd,
//                    'created_by'    => $user->id,
//                    'updated_by'    => $user->id,
//                    'created_at'    => $now->toDateTimeString()
//                ]);
//            }
//
//            // Create stock card
//            $stockCard = StockCard::create([
//                'item_id'           => $itemId,
//                'warehouse_id'      => $warehouseId,
//                'in_qty'            => $stockAdd,
//                'out_qty'           => 0,
//                'result_qty'        => $stockAdd,
//                'reference'         => "Tambah Stock Item Baru",
//                'created_by'        => $user->id,
//                'created_at'        => $now->toDateTimeString(),
//                'updated_by'        => $user->id,
//                'updated_at'        => $now->toDateTimeString()
//            ]);
//
//            // Edit total stock
//            $item = Item::find($itemId);
//            $item->stock += $stockAdd;
//            $item->save();
//
//            $json = ItemStock::with('warehouse')->find($stock->id);
//            error_log($json->warehouse->name);
//            return new JsonResponse($json);
//        }
//        catch(\Exception $ex){
//            error_log($ex);
//        }
//    }

    public function edit(ItemStock $item_stock){
        try{
            $itemStock = $item_stock;

            return View('admin.inventory.item_stocks.edit', compact('itemStock'));
        }
        catch (\Exception $ex){
            error_log($ex);
        }
    }

    public function update(Request $request, ItemStock $item_stock){
        try{
            $validator = Validator::make($request->all(),[
                'location'      => 'max:50'
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $user = Auth::user();
            $now = Carbon::now('Asia/Jakarta');

            if($request->filled('location')){
                $item_stock->location = $request->input('location');
            }
            else{
                $item_stock->location = null;
            }

            //$item_stock->stock_min = $request->input('stock_min');
            //$item_stock->stock_max = $request->input('stock_max');

            if(!$request->filled('stock_warning')) {
                $item_stock->is_stock_warning = false;

                $itemNotification = $item_stock->item_stock_notifications->first();
                if (!empty($itemNotification)) {
                    $itemNotification->delete();
                }
            }
            else{
                $item_stock->is_stock_warning = true;

                $itemNotification = $item_stock->item_stock_notifications->first();
                if(empty($itemNotification)){
                    ItemStockNotification::create([
                        'item_id'       => $item_stock->item_id,
                        'item_stock_id' => $item_stock->id,
                        'warehouse_id'  => $item_stock->warehouse_id,
                        'created_at'    => $now->toDateTimeString(),
                        'created_by'    => $user->id
                    ]);
                }
            }

            $item_stock->save();

            Session::flash('message', 'Berhasil mengubah pengaturan inventory!');

            return redirect()->route('admin.item_stocks.option.edit', ['item_stock' => $item_stock->id]);
        }
        catch (\Exception $ex){
            //error_log($ex);
            Log::error("ItemStockController - update: ". $ex);
        }
    }

//    public function update(Request $request){
//        try{
//            $validator = Validator::make($request->all(),[
//                'stock'             => 'required'
//            ]);
//
//            if ($validator->fails()) {
//                return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
//            }
//
//            $warehouseId = $request->input('warehouse_id');
//            $itemId = $request->input('item_id');
//            $stockAdd = (int) $request->input('stock');
//
//            // Check document usage
//            $isPrUsed = PurchaseRequestDetail::where('item_id', $itemId)->exists();
//            $isRfqUsed = QuotationDetail::where('item_id', $itemId)->exists();
//            $isPoUsed = PurchaseOrderDetail::where('item_id', $itemId)->exists();
//            $isGrUsed = ItemReceiptDetail::where('item_id', $itemId)->exists();
//            $isIdUsed = IssuedDocketDetail::where('item_id', $itemId)->exists();
//            $isDoUsed = DeliveryOrderDetail::where('item_id', $itemId)->exists();
//            $isReturUsed = ReturDetail::where('item_id', $itemId)->exists();
//
//            // Check stock usage
//            $isStockAdjustmentUsed = StockAdjustment::where('item_id', $itemId)->exists();
//            $isStockInUsed = StockIn::where('item_id', $itemId)->exists();
//            $isItemMutationUsed = ItemMutation::where('item_id', $itemId)->exists();
//
//            $isUsed = false;
//            if($isPrUsed || $isRfqUsed || $isPoUsed || $isGrUsed || $isIdUsed || $isDoUsed || $isReturUsed || $isStockAdjustmentUsed || $isStockInUsed || $isItemMutationUsed){
//                $isUsed = true;
//            }
//
//            if($isUsed){
//                return Response::json(array('errors' => 'used'));
//            }
//
//            $user = \Auth::user();
//            $now = Carbon::now('Asia/Jakarta');
//
//            // Check exists
//            $oldStock = 0;
//            $stock = ItemStock::find($request->input('id'));
//            if(!empty($stock)){
//
//                $exists = ItemStock::where('item_id', $itemId)
//                    ->where('warehouse_id', $warehouseId)
//                    ->exists();
//
//                if($exists){
//                    return Response::json(array('errors' => 'exists'));
//                }
//                else{
//                    $oldStock = $stock->stock;
//
//                    if($request->filled('warehouse_id')) $stock->warehouse_id = $warehouseId;
//
//                    $stock->stock = $stockAdd;
//                    $stock->updated_by = $user->id;
//                    $stock->updated_at = $now->toDateTimeString();
//                    $stock->save();
//                }
//            }
//            else{
//                return Response::json(array('errors' => 'deleted'));
//            }
//
//            // Edit total stock
//            $item = Item::find($itemId);
//            $item->stock = $item->stock - $oldStock + $stockAdd;
//            $item->save();
//
//            $json = ItemStock::with('warehouse')->find($stock->id);
//            return new JsonResponse($json);
//        }
//        catch (\Exception $ex){
//            error_log($ex);
//        }
//    }

    public function destroy(Request $request){
        try{
            $itemId = $request->input('item_id');

            $isPrUsed = PurchaseRequestDetail::where('item_id', $itemId)->exists();
            $isPoUsed = PurchaseOrderDetail::where('item_id', $itemId)->exists();
            $isGrUsed = ItemReceiptDetail::where('item_id', $itemId)->exists();
            $isIdUsed = IssuedDocketDetail::where('item_id', $itemId)->exists();
            $isDoUsed = DeliveryOrderDetail::where('item_id', $itemId)->exists();

            $isUsed = false;
            if($isPrUsed || $isPoUsed || $isGrUsed || $isIdUsed || $isDoUsed){
                $isUsed = true;
            }

            if($isUsed){
                return Response::json(array('errors' => 'used'));
            }

            $stock = ItemStock::find($request->input('id'));



            if(empty($stock)) return new JsonResponse($stock);

            $oldWarehouse = $stock->warehouse_id;
            $oldStock = $stock->stock;

            $stock->delete();



            // Minus total stock
            $item = Item::find($itemId);
            $item->stock = $item->stock - $stock->stock;
            $item->save();

            $user = Auth::user();
            $now = Carbon::now('Asia/Jakarta');

            // Create stock card
            $stockCard = StockCard::create([
                'item_id'           => $itemId,
                'warehouse_id'      => $oldWarehouse,
                'in_qty'            => 0,
                'out_qty'           => $oldStock,
                'result_qty'        => $item->stock,
                'reference'         => "Hapus Stock Item Baru",
                'created_by'        => $user->id,
                'created_at'        => $now->toDateTimeString(),
                'updated_by'        => $user->id,
                'updated_at'        => $now->toDateTimeString()
            ]);

            return new JsonResponse($stock);
        }
        catch (\Exception $ex){
            error_log($ex);
        }
    }

    public function updateLocation(Request $request){
        try{
            $itemStock = ItemStock::find($request->input('item_stock_id'));
            $itemStock->location = $request->input('location');
            $itemStock-> save();

            Session::flash('message', 'Berhasil mengubah data Lokasi atau Rak');

            return redirect()->route('admin.items.show', ['item' => $itemStock->item_id]);
        }
        catch (\Exception $ex){
            dd($ex);
        }
    }

    public function report(){
        return view('admin.inventory.item_stocks.report');
    }

    public function downloadReport(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'date'              => 'required'
            ],[
                'date.required'     => 'Tanggal wajib diisi!'
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');
            $start = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');
            $end = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');

            $start->addDays(-1);
            $end->addDays(1);

            $itemStocks = new Collection();
            $items = Item::orderBy('code')->get();
            foreach($items as $item){
                if($item->stock_cards->count() > 0){
                    $exactStockCard = $item->stock_cards->where('created_at', '>', $start->toDateString())
                        ->where('created_at', '<', $end->toDateString())
                        ->sortByDesc('created_at')
                        ->first();
                    if(!empty($exactStockCard)){
                        $tmpItem = $item;
                        $tmpItem->setGetStock($exactStockCard->result_qty);
                        $itemStocks->add($tmpItem);
                    }
                    else{
                        $latestStockCard = $item->stock_cards->where('created_at', '<', $date->toDateString())->sortByDesc('created_at')->first();
                        if(!empty($latestStockCard)){
                            $tmpItem2 = $item;
                            $tmpItem2->setGetStock($latestStockCard->result_qty);
                            $itemStocks->add($tmpItem2);
                        }
                    }
                }
            }

            if($itemStocks->count() == 0){
                return redirect()->back()->withErrors('Semua inventory belum ada transaksi!', 'default')->withInput($request->all());
            }

            $data = [
                'itemStocks'        => $itemStocks,
                'date'              => $request->input('date')
            ];

            //ini_set("pcre.backtrack_limit", "500000000");

            $now = Carbon::now('Asia/Jakarta');
            $filename = 'STOCK_STATUS_REPORT_' . $now->toDateTimeString();

            $pdf = PDF3::loadView('documents.item_stocks.item_stock_status_report_pdf', $data)
                ->setOption('footer-right', '[page] of [toPage]');

            return $pdf->download($filename.'.pdf');
        }
        catch (\Exception $ex){
            Log::error('ItemStockController - downloadReport : '. $ex);
            return redirect()->back()->withErrors('INTERNAL SERVER ERROR!', 'default')->withInput($request->all());
        }
    }

    public function excel(){
        $user = Auth::user();
        $site = $user->employee->site;

        if($site->id === 1){
            $warehouses = Warehouse::orderBy('name')->get();
        }
        else{
            $warehouses = Warehouse::where('site_id', $site->id)
                ->orderBy('name')
                ->get();
        }

        $data = [
            'warehouses'    => $warehouses,
            'site'          => $site->id
        ];

        return View('admin.inventory.item_stocks.excel')->with($data);
    }

    public function downloadExcel(Request $request){

        $warehouseId = $request->input('warehouse');
        $siteName = "";
        if($warehouseId !== '-1'){
            $warehouse =  Warehouse::find($warehouseId);
            $siteName = "_". $warehouse->site->name;
        }

        $now = Carbon::now('Asia/Jakarta');
        $filename = 'INVENTORY_STOCK'. strtoupper($siteName). '_'. $now->toDateTimeString(). '.xlsx';

        return (new InventoryStockExport($warehouseId))->download($filename);
    }

    public function getIndex(Request $request){
        $itemStocks = ItemStock::join('items', 'items.id', '=', 'item_stocks.item_id');
        $itemStocks->select('item_stocks.*', 'items.code', 'items.name', 'items.machinery_type', 'items.uom', 'items.part_number');

        if($request->filled('warehouse')){
            $warehouse = $request->input('warehouse');
            if($warehouse != '-1'){
                $itemStocks = $itemStocks->where('item_stocks.warehouse_id', $warehouse);
            }
        }

        if($request->filled('stock')){
            $stock = $request->input('stock');
            if($stock === '1'){
                $itemStocks = $itemStocks->where('item_stocks.stock', '>', 0);
            }
            else if($stock === '2'){
                $itemStocks = $itemStocks->where('item_stocks.stock', 0);
            }
            else{
                $itemStocks = $itemStocks->where('item_stocks.stock', '>', -1);
            }
        }
        else{
            $itemStocks = $itemStocks->where('item_stocks.stock', '>', 0);
        }

        if($request->filled('movement')){
            $movement = $request->input('movement');
            if($movement === 'DEAD'){
                $itemStocks = $itemStocks->where('item_stocks.movement_status', 'DEAD');
            }
            else if($movement === 'SLOW'){
                $itemStocks = $itemStocks->where('item_stocks.movement_status', 'SLOW');
            }
            else if($movement === 'MEDIUM'){
                $itemStocks = $itemStocks->where('item_stocks.movement_status', 'MEDIUM');
            }
            else if($movement === 'FAST'){
                $itemStocks = $itemStocks->where('item_stocks.movement_status', 'FAST');
            }
        }

        return DataTables::of($itemStocks)
            ->setTransformer(new ItemStockTransformer)
            ->make(true);
    }
}