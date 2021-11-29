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
use App\Transformer\Inventory\StockCardTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use PDF3;

class StockCardController extends Controller
{
    public function index(Request $request){
        $filterDateStart = Carbon::today()->subDays(5)->format('d M Y');
        $filterDateEnd = Carbon::today()->format('d M Y');

        if($request->date_start != null && $request->date_end != null){
            $dateStartDecoded = rawurldecode($request->date_start);
            $dateEndDecoded = rawurldecode($request->date_end);
            $start = Carbon::createFromFormat('!d M Y', $dateStartDecoded, 'Asia/Jakarta');
            $end = Carbon::createFromFormat('!d M Y', $dateEndDecoded, 'Asia/Jakarta');

            if($end->greaterThanOrEqualTo($start)){
                $filterDateStart = $dateStartDecoded;
                $filterDateEnd = $dateEndDecoded;
            }
        }

        $data = [
            'filterDateStart' => $filterDateStart,
            'filterDateEnd' => $filterDateEnd
        ];

        return View('admin.inventory.stock_cards.index')->with($data);
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
        $now = Carbon::now('Asia/Jakarta');
        $increase = (int) str_replace('.','', $request->input('increase'));
        $selectedItems = $request->input('item');
        $selectedItem = $selectedItems[0];

        $item = StockIn::create([
            'item_id'          => $selectedItem,
            'increase'          => $increase,
            'warehouse_id'  => $request->input('warehouse'),
            'created_by'    => $user->id,
            'created_at'    => $now
        ]);

        //edit item_stock
        $itemStockDB = ItemStock::where('item_id', $selectedItem)->where('warehouse_id', $request->input('warehouse'))->first();
        if(empty($itemStockDB)){
            $itemStock = ItemStock::create([
                'item_id'          => $selectedItem,
                'warehouse_id'  => $request->input('warehouse'),
                'stock'        => $increase,
                'created_by'    => $user->id,
                'created_at'    => $now
            ]);
        }
        else{
            $oldStock = $itemStockDB->stock;
            $itemStockDB->stock = $oldStock + $increase;
            $itemStockDB->updated_by = $user->id;
            $itemStockDB->updated_at = $now;

            $itemStockDB->save();
        }

        //edit item
        $itemDB = Item::find($selectedItem);
        $itemDB->stock += $increase;
        $itemDB->save();


        Session::flash('message', 'Berhasil membuat data Stock In baru!');

        return redirect()->route('admin.stock_ins');
    }

    public function getIndex(Request $request){
        $start = Carbon::createFromFormat('!d M Y', $request->input('date_start'), 'Asia/Jakarta');
        $end = Carbon::createFromFormat('!d M Y', $request->input('date_end'), 'Asia/Jakarta');
        $end->addDays(1);

        $stockCards = StockCard::with(['warehouse', 'createdBy', 'item'])
            ->whereBetween('created_at', array($start->toDateTimeString(), $end->toDateTimeString()));

        return DataTables::of($stockCards)
            ->setTransformer(new StockCardTransformer())
            ->addIndexColumn()
            ->make(true);
    }

    public function report(){
        $item = null;
        if(!empty(request()->item)){
            $item = Item::find(request()->item);
        }

        $warehouses = Warehouse::where('id', '!=', 0)->get();

        $data = [
            'item'          => $item,
            'warehouses'    => $warehouses
        ];

        return View('admin.inventory.stock_cards.report')->with($data);
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

        // Validate inventory
        if(!$request->filled('item') && !$request->filled('item_id')){
            return redirect()->back()->withErrors('Mohon pilih inventory!', 'default')->withInput($request->all());
        }

        // Validate date
        if($start->gt($end)){
            return redirect()->back()->withErrors('Dari Tanggal tidak boleh lebih besar dari Sampai Tanggal!', 'default')->withInput($request->all());
        }

        $start = $start->addDays(-1);
        $end = $end->addDays(1);

        $stockCards = StockCard::whereBetween('created_at', array($start->toDateTimeString(), $end->toDateTimeString()));

        // Filter item
        $itemId = '-1';
        if($request->filled('item')){
            $itemId = $request->input('item');
        }
        else{
            $itemId = $request->input('item_id');
        }
        $item = Item::find($itemId);

        // Filter Warehouse
        $filterWarehouse = 'Semua';
        if($request->input('warehouse') !== '-1'){
            $warehouseId = $request->input('warehouse');
            $stockCards = $stockCards->where('warehouse_id', $warehouseId);
            $filterWarehouse = Warehouse::find($warehouseId)->name;
        }

        $stockCards = $stockCards->where('item_id', $itemId)->get();

        // Validate Data
        if($stockCards->count() == 0){
            return redirect()->back()->withErrors('Data tidak ditemukan!', 'default')->withInput($request->all());
        }

        // Count total qty
        $totalInQty = 0;
        $totalOutQty = 0;
        foreach($stockCards as $card){
            $inQty = $card->in_qty ?? 0;
            $totalInQty += $inQty;

            $outQty = $card->out_qty ?? 0;
            $totalOutQty += $outQty;
        }

        // Get early balance
        $earlyBalanceStock = null;
        if($request->filled('warehouse')){
            $earlyBalanceStock = StockCard::where('item_id', $itemId)
                ->where('warehouse_id', $request->input('warehouse'))
                ->where('created_at', '<=', $start->toDateTimeString())
                ->orderBy('created_at', 'desc')
                ->first();
        }
        else{
            $earlyBalanceStock = StockCard::where('item_id', $itemId)
                ->where('created_at', '<=', $start->toDateTimeString())
                ->orderBy('created_at', 'desc')
                ->first();
        }

        $data =[
            'item'              => $item,
            'stockCards'        => $stockCards,
            'balanceStock'      => $earlyBalanceStock,
            'start_date'        => $request->input('start_date'),
            'finish_date'       => $request->input('end_date'),
            'filterWarehouse'   => $filterWarehouse,
            'totalInQty'        => $totalInQty,
            'totalOutQty'       => $totalOutQty
        ];

//        return view('documents.items.item_stock_report_pdf')->with($data);

        $pdf = PDF3::loadView('documents.items.item_stock_report_pdf', $data)
            ->setOption('footer-right', '[page] of [toPage]');

        $now = Carbon::now('Asia/Jakarta');
        $filename = 'ITEM_STOCK_REPORT_' . $now->toDateTimeString();

        return $pdf->download($filename.'.pdf');
    }
}