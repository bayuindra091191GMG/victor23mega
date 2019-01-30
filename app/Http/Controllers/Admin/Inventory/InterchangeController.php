<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 25/01/2018
 * Time: 10:15
 */
namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Libs\Utilities;
use App\Models\Group;
use App\Models\Interchange;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\ItemStockNotification;
use App\Models\StockCard;
use App\Models\Uom;
use App\Models\Warehouse;
use App\Transformer\Inventory\InterchangeTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class InterchangeController extends Controller
{
    public function index(){
        return View('admin.inventory.interchanges.index');
    }

    public function create(){
        $warehouses = Warehouse::where('id', '!=', 0)->get();
        $groups = Group::where('id', '!=', 0)->get();

        $data = [
            'warehouses'        => $warehouses,
            'groups'            => $groups
        ];

        return View('admin.inventory.interchanges.create')->with($data);
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

        if(!$request->filled('item_previous')){
            return redirect()->back()->withErrors('Pilih Inventory sebelumnya!', 'default')->withInput($request->all());
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

        if($request->filled('description')){
            $item->description = $request->input('description');
        }

        $item->save();

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

        //Create Interchange
        $interchange = Interchange::create([
            'item_id_before'    => $request->input('item_previous'),
            'item_id_after'     => $item->id,
            'created_by'        => $user->id,
            'created_at'        => $now
        ]);

        Session::flash('message', 'Berhasil membuat Interchange baru!');

        if($request->input('is_repeat') === '1'){
            return redirect()->route('admin.interchanges.create');
        }
        else{
            return redirect()->route('admin.interchanges');
        }
    }

    public function getIndex(){
        $items = Interchange::all();
        return DataTables::of($items)
            ->setTransformer(new InterchangeTransformer)
            ->addIndexColumn()
            ->make(true);
    }
}