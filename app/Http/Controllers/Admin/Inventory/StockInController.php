<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 25/01/2018
 * Time: 10:15
 */

namespace App\Http\Controllers\Admin\Inventory;


use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\StockCard;
use App\Models\StockIn;
use App\Models\Warehouse;
use App\Transformer\Inventory\StockInTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class StockInController extends Controller
{
    public function index(){
        return View('admin.inventory.stock_ins.index');
    }

    public function create(){
        $warehouses = Warehouse::where('id', '>', 0)->get();

        return View('admin.inventory.stock_ins.create', compact('warehouses'));
    }


    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'item'      => 'required',
            'increase'      => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        if(Input::get('warehouse') === '-1'){
            return redirect()->back()->withErrors('Pilih gudang!', 'default')->withInput($request->all());
        }

        //add to stock in table
        $user = Auth::user();
        $increase = (int) str_replace('.','', $request->input('increase'));
        $selectedItems = $request->input('item');
        $selectedItem = $selectedItems[0];

        // For offline sync
        $warehouseId = intval($request->input('warehouse'));
        $siteId = DB::table('warehouses')
            ->where('id', '=', $warehouseId)
            ->value('site_id');

        $newStockIn = StockIn::create([
            'item_id'       => $selectedItem,
            'increase'      => $increase,
            'warehouse_id'  => $warehouseId,
            'is_synced'     => $siteId !== 3,
            'created_by'    => $user->id,
            'created_at'    => Carbon::now('Asia/Jakarta')->toDateTimeString()
        ]);

        $itemStockDB = ItemStock::where('item_id', $selectedItem)
            ->where('warehouse_id', $warehouseId)
            ->first();

        $stockResultWarehouse = 0;
        if(empty($itemStockDB)){
            $newItemStock = ItemStock::create([
                'item_id'           => $selectedItem,
                'warehouse_id'      => $warehouseId,
                'stock'             => $increase,
                'stock_min'         => 0,
                'stock_max'         => 0,
                'is_stock_warning'  => false,
                'created_by'        => $user->id,
                'created_at'        => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                'updated_by'        => $user->id,
                'updated_at'        => Carbon::now('Asia/Jakarta')->toDateTimeString()
            ]);

            $itemStockId = $newItemStock->id;
        }
        else{
            $itemStockId = $itemStockDB->id;

            DB::table('item_stocks')
                ->where('id', '=', $itemStockDB->id)
                ->increment('stock', $increase, [
                    'updated_by' => $user->id,
                    'updated_at' => Carbon::now('Asia/Jakarta')->toDateTimeString()
                ]);
        }

        // Get latest stock
        $latestStock = DB::table('item_stocks')
            ->where('id', '=', $itemStockId)
            ->value('stock');

        // Get all stocks
        $totalStock = DB::table('item_stocks')
            ->where('item_id', '=', $selectedItem)
            ->sum('stock');

        // Update stock summary
        DB::table('items')
            ->where('id', '=', $selectedItem)
            ->update(['stock' => $totalStock]);

        //add stock card item
        $stockCard = StockCard::create([
            'item_id'               => $selectedItem,
            'warehouse_id'          => $request->input('warehouse'),
            'in_qty'                => $increase,
            'out_qty'               => 0,
            'result_qty'            => $totalStock,
            'result_qty_warehouse'  => $latestStock,
            'reference'             => "Stock In",
            'created_by'            => $user->id,
            'created_at'            => Carbon::now('Asia/Jakarta')->toDateTimeString(),
            'updated_by'            => $user->id,
            'updated_at'            => Carbon::now('Asia/Jakarta')->toDateTimeString()
        ]);

        Session::flash('message', 'Berhasil membuat Stock In baru!');

        return redirect()->route('admin.stock_ins');
    }

    public function getIndex(){
        $stockIns = StockIn::with(['item', 'warehouse']);
        return DataTables::of($stockIns)
            ->setTransformer(new StockInTransformer)
            ->addIndexColumn()
            ->make(true);
    }
}