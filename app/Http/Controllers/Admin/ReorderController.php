<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;

use App\Jobs\ItemIssuedCalibrationJob;
use App\Libs\Utilities;
use App\Models\Department;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\NumberingSystem;
use App\Models\Reorder;
use App\Models\Warehouse;
use App\Transformer\ReorderTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Editor\Fields\Number;

class ReorderController extends Controller
{
    public function menu (Request $request){
        return View('admin.reorder.menu');
    }

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

        $filterMovement = 'ALL';
        if($request->movement != null){
            $filterMovement = $request->movement;
        }

        $itemStocks = ItemStock::with(['item', 'item.group'])
            ->where('stock_min', '>', 0)
            ->whereRaw('(stock_on_order + stock + stock_on_reorder) <= stock_min');

        $itemStocks = $itemStocks->where('warehouse_id', $filterWarehouse);
        $itemStocks = $itemStocks->whereHas('item', function($query) use($filterType){
            $query->whereHas('group', function($query) use($filterType){
                $query->where('type', $filterType);
            });
        });

        if($filterMovement != 'ALL'){
            $itemStocks = $itemStocks->where('movement_status', $filterMovement);
        }

        $itemStocks = $itemStocks->get();

        $data = [
            'warehouses'        => $warehouses,
            'filterWarehouse'   => $filterWarehouse,
            'filterType'        => $filterType,
            'filterMovement'    => $filterMovement,
            'itemStocks'        => $itemStocks
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
                    ->whereRaw('(item_stocks.stock_on_order + item_stocks.stock + item.stock_on_reorder) <= item_stocks.stock_min')
                    ->where('item_stocks.stock_min', '>', 0);

        $warehouseId = $request->input('warehouse');
        $itemStocks = $itemStocks->where('item_stocks.warehouse_id', $warehouseId);

        $typeId = $request->input('type');
        $itemStocks = $itemStocks->where('groups.type', $typeId);

        $movement = $request->input('movement');
        if($movement != 'ALL'){
            $itemStocks = $itemStocks->where('item_stocks.movement_status', $movement);
        }

        return DataTables::of($itemStocks)
            ->setTransformer(new ReorderTransformer)
            ->addIndexColumn()
            ->make(true);
    }

    public function store(Request $request)
    {
//        if(empty($request->input('item_stock_ids'))){
//            return redirect()->back()->withErrors('Pilih Inventory Reorder!', 'default')->withInput($request->all());
//        }

        //dd($request->input('removed_stock_ids'));

        // Add the Itemcode and warehouseId to Session
        Session::put('removed_stock_ids', $request->input('removed_stock_ids'));
        //Session::put('item_stock_maxs', $request->input('item_stock_maxs'));
        Session::put('warehouse_id', $request->input('warehouse_id'));
        Session::put('type_id', $request->input('type_id'));
        Session::put('movement_status_id', $request->input('movement_status_id'));
        //Session::put('reorderQtys', $request->input('stock_max'));

        $type = $request->input('type_id');
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
        $warehouseId = Session::get('warehouse_id');
        $typeId = Session::get('type_id');
        $movementStatusId = Session::get('movement_status_id');

        // Get list of removed stock id and convert to array
        $removedStockIds = Session::get('removed_stock_ids');

        // Get item stock data
        $itemStocks = ItemStock::with(['item', 'item.group'])
            ->where('stock_min', '>', 0)
            ->whereRaw('(stock_on_order + stock + stock_on_reorder) <= stock_min');

        if(!empty($removedStockIds)){
            $removedStockIdArr = explode(',',$removedStockIds);
            $itemStocks = $itemStocks->whereNotIn('id', $removedStockIdArr);
        }

        $itemStocks = $itemStocks->where('warehouse_id', $warehouseId);
        $itemStocks = $itemStocks->whereHas('item', function($query) use($typeId){
            $query->whereHas('group', function($query) use($typeId){
                $query->where('type', $typeId);
            });
        });

        if($movementStatusId != 'ALL'){
            $itemStocks = $itemStocks->where('movement_status', $movementStatusId);
        }

        $itemStocks = $itemStocks->get();

        // Numbering System
        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        //$sysNo = NumberingSystem::where('doc_id', '9')->first();
        $mrPrepend = 'MR/'. $now->year. '/'. $now->month;
        $sysNo = Utilities::GetNextAutoNumber($mrPrepend);

        $docCode = 'MR-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumber($docCode, $sysNo);

        $dateToday = Carbon::today()->format('d M Y');

        $departments = Department::all();
        $warehouses = Warehouse::where('id', '!=', 0)->orderBy('name')->get();

        $data = [
            'departments'   => $departments,
            'autoNumber'    => $autoNumber,
            'warehouses'    => $warehouses,
            'warehouseId'   => $warehouseId,
            'dateToday'     => $dateToday,
            'itemStocks'    => $itemStocks
        ];
        return view('admin.reorder.other')->with($data);
    }

    public function fuel(){
        $warehouseId = Session::get('warehouse_id');
        $typeId = Session::get('type_id');
        $movementStatusId = Session::get('movement_status_id');

        // Get list of removed stock id and convert to array
        $removedStockIds = Session::get('removed_stock_ids');

        // Get item stock data
        $itemStocks = ItemStock::with(['item', 'item.group'])
            ->where('stock_min', '>', 0)
            ->whereRaw('(stock_on_order + stock + stock_on_reorder) <= stock_min');

        if(!empty($removedStockIds)){
            $removedStockIdArr = explode(',',$removedStockIds);
            $itemStocks = $itemStocks->whereNotIn('id', $removedStockIdArr);
        }

        $itemStocks = $itemStocks->where('warehouse_id', $warehouseId);
        $itemStocks = $itemStocks->whereHas('item', function($query) use($typeId){
            $query->whereHas('group', function($query) use($typeId){
                $query->where('type', $typeId);
            });
        });

        if($movementStatusId != 'ALL'){
            $itemStocks = $itemStocks->where('movement_status', $movementStatusId);
        }

        $itemStocks = $itemStocks->get();

        // Numbering System
        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        //$sysNo = NumberingSystem::where('doc_id', '10')->first();
        $mrPrepend = 'MR-F/'. $now->year. '/'. $now->month;
        $sysNo = Utilities::GetNextAutoNumber($mrPrepend);

        $docCode = 'MR-F-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumber($docCode, $sysNo);

        $dateToday = Carbon::today()->format('d M Y');

        $departments = Department::all();
        $warehouses = Warehouse::where('id', '!=', 0)->orderBy('name')->get();

        $data = [
            'departments'   => $departments,
            'autoNumber'    => $autoNumber,
            'warehouses'    => $warehouses,
            'warehouseId'   => $warehouseId,
            'dateToday'     => $dateToday,
            'itemStocks'    => $itemStocks
        ];
        return view('admin.reorder.other')->with($data);
    }

    public function oil(){
        $warehouseId = Session::get('warehouse_id');
        $typeId = Session::get('type_id');
        $movementStatusId = Session::get('movement_status_id');

        // Get list of removed stock id and convert to array
        $removedStockIds = Session::get('removed_stock_ids');

        // Get item stock data
        $itemStocks = ItemStock::with(['item', 'item.group'])
            ->where('stock_min', '>', 0)
            ->whereRaw('(stock_on_order + stock + stock_on_reorder) <= stock_min');

        if(!empty($removedStockIds)){
            $removedStockIdArr = explode(',',$removedStockIds);
            $itemStocks = $itemStocks->whereNotIn('id', $removedStockIdArr);
        }

        $itemStocks = $itemStocks->where('warehouse_id', $warehouseId);
        $itemStocks = $itemStocks->whereHas('item', function($query) use($typeId){
            $query->whereHas('group', function($query) use($typeId){
                $query->where('type', $typeId);
            });
        });

        if($movementStatusId != 'ALL'){
            $itemStocks = $itemStocks->where('movement_status', $movementStatusId);
        }

        $itemStocks = $itemStocks->get();

        // Numbering System
        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        //$sysNo = NumberingSystem::where('doc_id', '11')->first();
        $mrPrepend = 'MR-O/'. $now->year. '/'. $now->month;
        $sysNo = Utilities::GetNextAutoNumber($mrPrepend);

        $docCode = 'MR-O-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumber($docCode, $sysNo);

        $dateToday = Carbon::today()->format('d M Y');

        $departments = Department::all();
        $warehouses = Warehouse::where('id', '!=', 0)->orderBy('name')->get();

        $data = [
            'departments'   => $departments,
            'autoNumber'    => $autoNumber,
            'warehouses'    => $warehouses,
            'warehouseId'   => $warehouseId,
            'dateToday'     => $dateToday,
            'itemStocks'    => $itemStocks
        ];

        return view('admin.reorder.other')->with($data);
    }

    /**
     * Calibrate issued docket qty, minimum stock & maximum stock
     * @param Request $request
     */
    public function calibrate(Request $request){
        try{
            $dateBefore1Year = Carbon::now()->subMonths(12);
            $dateBefore1YearStr = $dateBefore1Year->toDateTimeString();

            $issuedDocketDetails = DB::table('issued_docket_details')
                ->join('issued_docket_headers', 'issued_docket_details.header_id', '=', 'issued_docket_headers.id')
                ->select('issued_docket_details.*', 'issued_docket_headers.*')
                ->where('issued_docket_headers.date', '>=', $dateBefore1YearStr)
                ->get();

            //ini_set('memory_limit', '-1');

            DB::table('item_stocks')->chunkById(1500, function($itemStocks) use($issuedDocketDetails){
                dispatch(new ItemIssuedCalibrationJob($itemStocks, $issuedDocketDetails));
            });
        }
        catch (\Exception $ex){
            Log::error($ex->getMessage());
        }
    }
}
