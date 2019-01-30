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
use App\Models\ItemMutation;
use App\Models\ItemStock;
use App\Models\StockCard;
use App\Models\Warehouse;
use App\Transformer\Inventory\ItemMutationTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class ItemMutationController extends Controller
{
    public function index(){
        return View('admin.inventory.item_mutations.index');
    }

    public function create(){
        $warehouses = Warehouse::where('id', '>', 0)->get();

        return View('admin.inventory.item_mutations.create', compact('warehouses'));
    }


    public function store(Request $request){
//        dd($request->input('item'));

        $validator = Validator::make($request->all(),[
            'item'              => 'required',
            'warehouse_from'    => 'required',
            'warehouse_to'      => 'required',
            'mutation_quantity' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        if(Input::get('warehouse_from') === '-1'){
            return redirect()->back()->withErrors('Pilih gudang asal!', 'default')->withInput($request->all());
        }
        if(Input::get('warehouse_to') === '-1'){
            return redirect()->back()->withErrors('Pilih gudang tujuan!', 'default')->withInput($request->all());
        }
        if(Input::get('warehouse_from') === Input::get('warehouse_to')){
            return redirect()->back()->withErrors('Pilih gudang asal dan gudang tujuan yang berbeda', 'default')->withInput($request->all());
        }
        if(Input::get('mutation_quantity') <= 0){
            return redirect()->back()->withErrors('Jumlah barang harus lebih dari 0!', 'default')->withInput($request->all());
        }

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $qty = (int) $request->input('mutation_quantity');
        $toWarehouse = $request->input('warehouse_to');
        $fromWarehouse = $request->input('warehouse_from');
        $itemId = $request->input('item');

        $fromItemStock = ItemStock::where('warehouse_id', $fromWarehouse)
            ->where('item_id', $itemId)->first();

        if(empty($fromItemStock)){
            return redirect()->back()->withErrors('Kuantitas perpindahan tidak boleh melebihi stok gudang asal!', 'default')->withInput($request->all());
        }
        elseif($fromItemStock->stock < $qty){
            return redirect()->back()->withErrors('Kuantitas perpindahan tidak boleh melebihi stok gudang asal!', 'default')->withInput($request->all());
        }

        // Decrease from Warehouse item stock
        $fromItemStock->stock -= $qty;
        $fromItemStock->updated_by = $user->id;
        $fromItemStock->updated_at = $now->toDateTimeString();
        $fromItemStock->save();

        $stockResultFromWarehouse = $fromItemStock->stock;

        $itemMutation = ItemMutation::create([
            'item_id'               => $itemId,
            'from_warehouse_id'     => $fromWarehouse,
            'to_warehouse_id'       => $toWarehouse,
            'mutation_quantity'     => $qty,
            'created_by'            => $user->id,
            'created_at'            => $now
        ]);

        $item = Item::find($itemId);

        $toItemStock = ItemStock::where('warehouse_id', Input::get('warehouse_to'))
            ->where('item_id', $itemId)
            ->first();

        // Get warehouse stock result
        $stockResultToWarehouse = 0;
        if(empty($toItemStock)){
            $newStock = ItemStock::create([
                'item_id'           => $itemId,
                'warehouse_id'      => $toWarehouse,
                'stock'             => $qty,
                'stock_min'         => 0,
                'stock_max'         => 0,
                'is_stock_warning'  => false,
                'created_by'        => $user->id,
                'created_at'        => $now->toDateTimeString(),
                'updated_by'        => $user->id,
                'updated_at'        => $now->toDateTimeString()
            ]);

            $stockResultToWarehouse = $qty;
        }
        else{
            $toItemStock->stock += $qty;
            $toItemStock->updated_by = $user->id;
            $toItemStock->updated_at = $now->toDateTimeString();
            $toItemStock->save();

            $stockResultToWarehouse = $toItemStock->stock;
        }

        // Create stock card
        $stockCardOut = StockCard::create([
            'item_id'               => $itemId,
            'warehouse_id'          => $fromWarehouse,
            'in_qty'                => 0,
            'out_qty'               => $qty,
            'result_qty'            => $item->stock,
            'result_qty_warehouse'  => $stockResultFromWarehouse,
            'reference'             => "Mutasi Keluar",
            'created_by'            => $user->id,
            'created_at'            => $now->toDateTimeString(),
            'updated_by'            => $user->id,
            'updated_at'            => $now->toDateTimeString()
        ]);

        $stockCardIn = StockCard::create([
            'item_id'               => $itemId,
            'warehouse_id'          => $toWarehouse,
            'in_qty'                => $qty,
            'out_qty'               => 0,
            'result_qty'            => $item->stock,
            'result_qty_warehouse'  => $stockResultToWarehouse,
            'reference'             => "Mutasi Masuk",
            'created_by'            => $user->id,
            'created_at'            => $now->toDateTimeString(),
            'updated_by'            => $user->id,
            'updated_at'            => $now->toDateTimeString()
        ]);

        Session::flash('message', 'Berhasil membuat Mutasi inventory baru!');

        return redirect()->route('admin.item_mutations');
    }

    public function getIndex(){
        $stockIns = ItemMutation::all();
        return DataTables::of($stockIns)
            ->setTransformer(new ItemMutationTransformer())
            ->addIndexColumn()
            ->make(true);
    }
}