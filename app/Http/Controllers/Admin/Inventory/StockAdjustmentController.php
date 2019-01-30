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
use App\Models\StockAdjustment;
use App\Models\StockCard;
use App\Models\Warehouse;
use App\Transformer\Inventory\StockAdjustmentTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class StockAdjustmentController extends Controller
{
    public function index(){
        return View('admin.inventory.stock_adjustments.index');
    }

    public function create(){
        $warehouses = Warehouse::where('id', '>', 0)->get();

        return View('admin.inventory.stock_adjustments.create', compact('warehouses'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'item'                  => 'required',
            'depreciation'          => 'required'
        ],[
            'item.required'         => 'Mohon pilih inventory!',
            'depreciation.required' => 'Mohon isi kuantitas pengurangan!'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        if($request->input('warehouse') === '-1'){
            return redirect()->back()->withErrors('Pilih gudang!', 'default')->withInput($request->all());
        }

        // Validate item stock
        $selectedItems = Input::get('item');
        $selectedItem = $selectedItems[0];
        $itemStock = ItemStock::where('item_id', $selectedItem)->where('warehouse_id', $request->input('warehouse'))->first();
        if(empty($itemStock)){
            return redirect()->back()->withErrors('Stock tidak mencukupi di gudang yang dipilih!', 'default')->withInput($request->all());
        }

        // Add to stock adjustment list
        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $depreciation = (int) str_replace('.','', Input::get('depreciation'));

        $stockAdjustment = StockAdjustment::create([
            'item_id'           => $selectedItem,
            'depreciation'      => $depreciation,
            'warehouse_id'      => $request->input('warehouse'),
            'created_by'        => $user->id,
            'created_at'        => $now->toDateTimeString()
        ]);

        // Update item stock
        $oldStock = $itemStock->stock;
        $itemStockPerWarehouse = $oldStock - $depreciation;

        $itemStock->stock = $itemStockPerWarehouse;
        $itemStock->updated_by = $user->id;
        $itemStock->updated_at = $now->toDateTimeString();

        $itemStock->save();

        // Get warehouse stock result
        $stockResultWarehouse = $itemStock->stock;

        //edit item
        $item = Item::find($selectedItem);
        $item->stock -= $depreciation;
        $item->save();

        //add stock card item
        $stockCard = StockCard::create([
            'item_id'               => $selectedItem,
            'warehouse_id'          => $request->input('warehouse'),
            'in_qty'                => 0,
            'out_qty'               => $depreciation,
            'result_qty'            => $item->stock,
            'result_qty_warehouse'  => $stockResultWarehouse,
            'reference'             => "Stock Adjustment",
            'created_by'            => $user->id,
            'created_at'            => $now->toDateTimeString(),
            'updated_by'            => $user->id,
            'updated_at'            => $now->toDateTimeString()
        ]);

        Session::flash('message', 'Berhasil membuat Stock Adjustment baru!');

        return redirect()->route('admin.stock_adjustments');
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getIndex(){
        $stockAdjustments = StockAdjustment::all();
        return DataTables::of($stockAdjustments)
            ->setTransformer(new StockAdjustmentTransformer)
            ->addIndexColumn()
            ->make(true);
    }
}