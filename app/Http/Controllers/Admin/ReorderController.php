<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;

use App\Libs\Utilities;
use App\Models\Department;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\NumberingSystem;
use App\Models\Reorder;
use App\Models\Warehouse;
use App\Transformer\ReorderTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Editor\Fields\Number;

class ReorderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $site = $user->employee->site;

        if($request->warehouse != null){
            $filterWarehouse = $request->warehouse;
        }
        else{
            $filterWarehouse = ''. $site->warehouses->first()->id;
        }

        $warehouses = Warehouse::where('id', '!=', 0)->orderBy('name')->get();

        if($request->type != null){
            $filterType = $request->type;
        }
        else{
            $filterType = '1';
        }

        $data = [
            'warehouses'        => $warehouses,
            'filterWarehouse'   => $filterWarehouse,
            'filterType'        => $filterType
        ];

        return View('admin.reorder.index')->with($data);
    }

    public function getIndex(Request $request){
//        $query = "SELECT * FROM item_stocks ".
//            "JOIN items ON items.id = item_stocks.item_id ".
//            "WHERE (item_stocks.stock_on_order + item_stocks.stock) <= item_stocks.stock_min ".
//            "AND item_stocks.is_stock_warning = 1";
        $itemStocks = ItemStock::join('items', 'items.id', '=', 'item_stocks.item_id')
                    ->join('groups', 'groups.id', '=', 'items.group_id')
                    ->select('item_stocks.*', 'items.code', 'items.part_number', 'items.name', 'groups.type')
                    ->whereRaw('(item_stocks.stock_on_order + item_stocks.stock) <= item_stocks.stock_min')
                    ->where('item_stocks.stock_min', '>', 0);

        $warehouseId = $request->warehouse;
        $itemStocks = $itemStocks->where('item_stocks.warehouse_id', $warehouseId);

        $typeId = $request->type;
        $itemStocks = $itemStocks->where('groups.type', $typeId);


        return DataTables::of($itemStocks)
            ->setTransformer(new ReorderTransformer)
            ->addIndexColumn()
            ->make(true);
    }

    public function store(Request $request)
    {
        if(empty($request->input('itemcode'))){
            return redirect()->back()->withErrors('Pilih Inventory Reorder!', 'default')->withInput($request->all());
        }

        // Add the Itemcode and warehouseId to Session
        Session::put('reorderItem', $request->input('itemcode'));
        Session::put('warehouseId', $request->input('warehouseId'));
        Session::put('reorderQtys', $request->input('reorderQtys'));

        $type = $request->input('type');
        if($type == 1){
            return redirect()->action('Admin\ReorderController@part');
        }
        else if($type == 2){
            return redirect()->action('Admin\ReorderController@fuel');
        }
        else if($type == 3){
            return redirect()->action('Admin\ReorderController@oil');
        }
    }

    public function part(){
        $warehouseId = Session::get('warehouseId');
        $items = Session::get('reorderItem');
        $qtys = Session::get('reorderQtys');
        $departments = Department::all();
        $warehouses = Warehouse::where('id', '!=', 0)->orderBy('name')->get();
        $itemsData = Item::whereIn('code', $items)->get();

        // Numbering System
        $user = Auth::user();
        $sysNo = NumberingSystem::where('doc_id', '9')->first();
        $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumber($docCode, $sysNo->next_no);

        $dateToday = Carbon::today()->format('d M Y');

        $data = [
            'departments'   => $departments,
            'autoNumber'    => $autoNumber,
            'warehouses'    => $warehouses,
            'warehouseId'   => $warehouseId,
            'reorderItems'  => $itemsData,
            'qtys'          => $qtys,
            'dateToday'     => $dateToday
        ];
        return view('admin.reorder.other')->with($data);
    }

    public function fuel(){
        $warehouseId = Session::get('warehouseId');
        $items = Session::get('reorderItem');
        $qtys = Session::get('reorderQtys');
        $departments = Department::all();
        $warehouses = Warehouse::where('id', '!=', 0)->orderBy('name')->get();
        $itemsData = Item::whereIn('code', $items)->get();

        // Numbering System
        $user = Auth::user();
        $sysNo = NumberingSystem::where('doc_id', '10')->first();
        $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumber($docCode, $sysNo->next_no);

        $dateToday = Carbon::today()->format('d M Y');

        $data = [
            'departments'   => $departments,
            'autoNumber'    => $autoNumber,
            'warehouses'    => $warehouses,
            'warehouseId'   => $warehouseId,
            'reorderItems'  => $itemsData,
            'qtys'          => $qtys,
            'dateToday'     => $dateToday
        ];
        return view('admin.reorder.fuel')->with($data);
    }

    public function oil(){
        $warehouseId = Session::get('warehouseId');
        $items = Session::get('reorderItem');
        $qtys = Session::get('reorderQtys');
        $departments = Department::all();
        $warehouses = Warehouse::where('id', '!=', 0)->orderBy('name')->get();
        $itemsData = Item::whereIn('code', $items)->get();

        // Numbering System
        $user = Auth::user();
        $sysNo = NumberingSystem::where('doc_id', '11')->first();
        $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumber($docCode, $sysNo->next_no);

        $dateToday = Carbon::today()->format('d M Y');

        $data = [
            'departments'   => $departments,
            'autoNumber'    => $autoNumber,
            'warehouses'    => $warehouses,
            'warehouseId'   => $warehouseId,
            'reorderItems'  => $itemsData,
            'qtys'          => $qtys,
            'dateToday'     => $dateToday
        ];
        return view('admin.reorder.oil')->with($data);
    }
}