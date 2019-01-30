<?php

namespace App\Http\Controllers;

use App\Models\ApprovalMaterialRequest;
use App\Models\ApprovalPurchaseOrder;
use App\Models\Auth\User\User;
use App\Models\IssuedDocketHeader;
use App\Models\Item;
use App\Models\ItemReceiptHeader;
use App\Models\ItemStock;
use App\Models\ItemStockNotification;
use App\Models\MaterialRequestDetail;
use App\Models\MaterialRequestHeader;
use App\Models\PurchaseInvoiceHeader;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseOrderHeader;
use App\Models\PurchaseRequestHeader;
use App\Models\StockCard;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mpdf\Tag\I;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('welcome');
    }

    public function test(){
        ini_set('max_execution_time', 0);

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');

        $idx = 0;

//        $poHeaders = PurchaseOrderHeader::all();
//        foreach($poHeaders as $header){
//            if($header->is_all_received === 0){
//                $poDetails = $header->purchase_order_details;
//                $isPartial = false;
//                foreach ($poDetails as $poDetail){
//                    if($poDetail->received_quantity > 0){
//                        $isPartial = true;
//                    }
//                }
//
//                if($isPartial){
//                    $header->is_all_received = 2;
//                    $header->save();
//                }
//            }
//        }

//        $idHeaders = IssuedDocketHeader::all();
//        foreach($idHeaders as $header){
//            if($header->material_request_header->type === 2){
//                $header->type = 2;
//            }
//            else{
//                $header->type = 1;
//            }
//
//            $header->site_id = $header->createdBy->employee->site_id;
//
//            $header->save();
//            $idx++;
//        }

//        $piHeaders = PurchaseInvoiceHeader::all();
//        foreach($piHeaders as $header){
//            $header->mr_type = $header->purchase_order_header->purchase_request_header->material_request_header->type;
//            $header->save();
//            $idx++;
//        }

//        $itemStockNotifs = ItemStockNotification::all();
//        foreach ($itemStockNotifs as $notif){
//            $site = $notif->user->employee->site;
//            $warehouse = $site->warehouses->first();
//
//            $notif->warehouse_id = $warehouse->id;
//
//            $itemStock = ItemStock::where('item_id', $notif->item_id)
//                ->where('warehouse_id', $warehouse->id)
//                ->first();
//
//            if(empty($itemStock)){
//                $stock = ItemStock::create([
//                    'item_id'           => $notif->item_id,
//                    'warehouse_id'      => $warehouse->id,
//                    'stock'             => 0,
//                    'stock_min'         => $notif->item->stock_minimum,
//                    'stock_max'         => 0,
//                    'is_stock_warning'  => true,
//                    'created_by'        => $user->id,
//                    'created_at'        => $now->toDateTimeString(),
//                    'updated_by'        => $user->id
//                ]);
//
//                $stockId = $stock->id;
//            }
//            else{
//                $itemStock->updated_by = $itemStock->created_by;
//                $itemStock->save();
//
//                $stockId = $itemStock->id;
//            }
//
//            $notif->item_stock_id = $stockId;
//            $notif->save();
//
//            $idx++;
//        }

//        $piHeader = PurchaseRequestHeader::find(156);
//
//        foreach($piHeader->purchase_request_details as $detail){
//            $detail->quantity_poed = 0;
//            $detail->save();
//        }
//
//        foreach($piHeader->purchase_order_headers as $poHeader){
//            foreach($poHeader->purchase_order_details as $poDetail){
//                $piDetail = $piHeader->purchase_request_details->where('item_id', $poDetail->item_id)->first();
//                if(!empty($piDetail)){
//                    $piDetail->quantity_poed = $poDetail->quantity;
//                    $piDetail->save();
//                }
//            }
//        }
//
//        $mrDetails = MaterialRequestDetail::all();
//        foreach ($mrDetails as $detail){
//            if(empty($detail->quantity_received)) $detail->quantity_received = 0;
//            if(empty($detail->quantity_issued)) $detail->quantity_issued = 0;
//            if(empty($detail->quantity_retur))  $detail->quantity_retur = 0;
//            $detail->save();
//        }

//        $prHeaders = PurchaseRequestHeader::all();
//        foreach ($prHeaders as $header){
//            if(empty($header->is_all_poed)){
//                $header->is_all_poed = 0;
//                $header->save();
//            }
//        }

//        $idHeaders = IssuedDocketHeader::all();
//        foreach ($idHeaders as $header){
//            if(empty($header->site_id)){
//                $header->site_id = $header->createdBy->employee->site_id;
//                $header->save();
//
//                $idx++;
//            }
//        }

//        $idHeaders = IssuedDocketHeader::all();
//        foreach ($idHeaders as $header){
//            if($header->type === 1){
//                if(!empty($header->unit_id)){
//                    foreach ($header->issued_docket_details as $detail){
//                        $detail->machinery_id = $header->unit_id;
//                        $detail->save();
//                        $idx++;
//                    }
//                }
//            }
//        }


//        $items = Item::all();
//
//        $warehouses = Warehouse::all();
//        foreach ($warehouses as $warehouse){
//            foreach ($items as $item){
//                $stockCards = $warehouse->stock_cards()
//                    ->where('item_id', $item->id)
//                    ->orderBy('created_at')
//                    ->get();
//
//                $resultQty = 0;
//                if($stockCards->count() > 0){
//                    foreach ($stockCards as $stockCard){
//                        if($stockCard->in_qty > 0){
//                            $resultQty += $stockCard->in_qty;
//                            $stockCard->result_qty_warehouse = $resultQty;
//                        }
//                        else if($stockCard->out_qty > 0){
//                            $resultQty -= $stockCard->out_qty;
//                            $stockCard->result_qty_warehouse = $resultQty;
//                        }
//                        $stockCard->save();
//                        $idx++;
//                    }
//                }
//            }
//        }

//        $items = Item::all();
//
//        foreach ($items as $item){
//            $totalQtyOnOrder = 0;
//            if($item->purchase_order_details->count() > 0){
//                foreach ($item->purchase_order_details as $poDetail){
//                    if($poDetail->purchase_order_header->status_id === 3){
//                        $qtyOnOrder = $poDetail->quantity - $poDetail->received_quantity;
//                        $totalQtyOnOrder +=  $qtyOnOrder;
//                        $idx++;
//                    }
//                }
//                $item->stock_on_order = $totalQtyOnOrder;
//                $item->save();
//            }
//        }


//        $poDetails = PurchaseOrderDetail::where('item_id', 2872)->get();
//        $totalQty = 0;
//        foreach ($poDetails as $poDetail){
//            if($poDetail->purchase_order_header->status_id === 3){
//                $qty = $poDetail->quantity - $poDetail->received_quantity;
//                $totalQty += $qty;
//            }
//        }
//
//        dd($totalQty);

//        $itemStocks = ItemStock::where('warehouse_id', 0)
//            ->where('stock', '<', 0)
//            ->get();
//
//        foreach ($itemStocks as $itemStock){
//            $resultQty = $itemStock->stock;
//
//            $warehouseStock = ItemStock::where('warehouse_id', 3)
//                ->where('item_id', $itemStock->item_id)
//                ->get()->last()
//
//            if(!empty($warehouseStock)){
//                $stockCard = StockCard::where('item_id', $itemStock->item_id)
//                    ->where('result_qty_warehouse', $warehouseStock->stock)
//                    ->first();
//
//                $stockCard->delete();
//
//                $warehouseStock->stock += $resultQty;
//                $warehouseStock->save();
//            }
//            else{
//                $i++;
//            }
//
//            $itemStock->stock = 0;
//            $itemStock->save();
//
//            $idx++;
//        }

        $i = 0;
//        $mrHeaders = MaterialRequestHeader::all();
//        foreach ($mrHeaders as $mrHeader){
//            if($mrHeader->purchase_request_headers->count() > 0){
//                $mrHeader->is_pr_created = 1;
//                $mrHeader->save();
//
//                $i++;
//            }
//        }

//        $prHeaders = PurchaseRequestHeader::all();
//        foreach ($prHeaders as $prHeader){
//            if($prHeader->is_all_poed === 0){
//                $isPartialPoed = false;
//                foreach ($prHeader->purchase_request_details as $prDetail){
//                    if($prDetail->quantity_poed > 0){
//                        $isPartialPoed = true;
//                    }
//                }
//
//                if($isPartialPoed){
//                    $prHeader->is_all_poed = 2;
//                    $prHeader->save();
//
//                    $i++;
//                }
//            }
//        }
//
//        $poHeaders = PurchaseOrderHeader::all();
//        foreach ($poHeaders as $poHeader){
//            if($poHeader->is_all_invoiced === 0){
//                $isPartialInvoiced = false;
//                foreach ($poHeader->purchase_order_details as $poDetail){
//                    if($poDetail->quantity_invoiced > 0){
//                        $isPartialInvoiced = true;
//                    }
//                }
//
//                if($isPartialInvoiced){
//                    $poHeader->is_all_invoiced = 2;
//                    $poHeader->save();
//
//                    $i++;
//                }
//            }
//        }

//        $date = new Carbon('2018-12-22');
//        $mrHeaders = MaterialRequestHeader::where('created_at', '>=', $date->toDateTimeString())
//            ->where('site_id', 3)
//            ->where('is_approved', 1)
//            ->where('is_pr_created', 0)
//            ->get();
//        foreach ($mrHeaders as $header){
//            $header->is_approved = 0;
//            $header->save();
//            $i++;
//        }

//        $mrHeaders = MaterialRequestHeader::where('is_approved', 1)->get();
//        foreach ($mrHeaders as $header){
//            if(empty($header->approved_date)){
//                $approvalMrHeaders = ApprovalMaterialRequest::where('material_request_id', $header->id)
//                    ->orderByDesc('created_at')
//                    ->first();
//
//                if(!empty($approvalMrHeaders)){
//                    $header->approved_date = $approvalMrHeaders->created_at;
//                    $header->save();
//
//                    $i++;
//                }
//            }
//        }

//        $users = User::all();
//        foreach ($users as $user){
//            $user->email_address = "ptvdtm.erp@gmail.com";
//            $user->save();
//        }

//        $itemStocks = ItemStock::all();

//        $warehouses = Warehouse::all();
////
////        $mrHeaders = MaterialRequestHeader::all();
////        foreach ($mrHeaders as $mrHeader){
////            if(!empty($mrHeader->site_id)){
////                $warehouse = $warehouses->where('site_id', $mrHeader->site_id)->first();
////                $mrHeader->warehouse_id = $warehouse->id;
////                $mrHeader->save();
////                $i++;
////            }
////        }
////
////        $prHeaders = PurchaseRequestHeader::all();
////        foreach ($prHeaders as $prHeader){
////            if(!empty($prHeader->material_request_header->warehouse_id)){
////                $prHeader->warehouse_id = $prHeader->material_request_header->warehouse_id;
////                $prHeader->save();
////
////                $i++;
////            }
////        }
////
////        $poHeaders = PurchaseOrderHeader::all();
////        foreach ($poHeaders as $poHeader){
////            if(!empty($poHeader->purchase_request_header->warehouse_id)){
////                $poHeader->warehouse_id = $poHeader->purchase_request_header->warehouse_id;
////                $poHeader->save();
////
////                $i++;
////            }
////        }

//        $poHeaders = PurchaseOrderHeader::where('is_approved', 1)
//            ->where('is_all_received', '!=', 1)
//            ->where('status_id', 3);

//        $poDetails = PurchaseOrderDetail::with('purchase_order_header')
//            ->whereHas('purchase_order_header', function($query){
//                $query->where('is_approved', 1)
//                    ->where('is_all_received', '!=', 1)
//                    ->where('status_id', 3);
//            })->get();

//        $itemStocks = ItemStock::skip(10000)->limit(2000)->get();
//        foreach ($itemStocks as $itemStock){
//            $stockOnOrder = 0;
//
//            $warehouseId = $itemStock->warehouse_id;
//            $poDetailsWithWH = PurchaseOrderDetail::with('purchase_order_header')
//                ->whereHas('purchase_order_header', function($query) use($warehouseId){
//                    $query->where('is_approved', 1)
//                        ->where('is_all_received', '!=', 1)
//                        ->where('status_id', 3)
//                        ->where('warehouse_id', $warehouseId);
//                })->get();
//
//
//            foreach ($poDetailsWithWH as $poDetail){
//                if($poDetail->item_id === $itemStock->item_id){
//                    $tmp = $poDetail->quantity - $poDetail->received_quantity;
//                    $stockOnOrder += $tmp;
//                    $i++;
//                }
//            }
//
//            $itemStock->stock_on_order = $stockOnOrder;
//            $itemStock->save();
//        }

        $itemReceipt = ItemReceiptHeader::find(1598);
        foreach ($itemReceipt->item_receipt_details as $detail){
            $itemStock = ItemStock::where('item_id', $detail->item_id)
                            ->where('warehouse_id', 3)
                            ->first();

            if(!empty($itemStock)){
                $itemStock->stock = $itemStock->stock - $detail->quantity;
                $itemStock->save();
            }

            $item = Item::find($detail->item_id);
            $undoStock = $item->stock - $detail->quantity;
            $item->stock = $undoStock;
            if($undoStock == 0){
                $item->value = 0;
            }

            $item->stock_on_order += $detail->quantity;
            $item->save();

            $stockCard = StockCard::where('item_id', $detail->item_id)
                            ->where('reference', 'Goods Receipt GR-HO/2019/1/1597')
                            ->first();

            if(!empty($stockCard)){
                $stockCard->delete();
            }

            $detail->delete();
        }

        $poHeader = $itemReceipt->purchase_order_header;
        foreach ($poHeader->purchase_order_details as $detail){
            $detail->received_quantity = 0;
            $detail->save();

            $item = Item::find($detail->item_id);
            if($item->stock - $detail->quantity > 0){
                $oldValuation = $item->value * $item->stock;
                $returValiation = $detail->quantity * $detail->price;
                $newValuation = ($oldValuation - $returValiation) / ($item->stock - $detail->quantity);
                $item->value = $newValuation;
                $item->save();
            }

            $mrDetail = $poHeader->purchase_request_header->material_request_header->material_request_details->where('item_id', $detail->item_id)->first();
            if(!empty($mrDetail)){
                $mrDetail->quantity_received -= $detail->quantity;
                $mrDetail->save();
            }
        }

        $mrHeader = $poHeader->purchase_request_header->material_request_header;
        $mrHeader->status_id = 3;
        $mrHeader->save();

        $poHeader->is_all_received = 0;
        $poHeader->save();



        $itemReceipt->delete();

        dd('SUCCESS!! '. $i);
    }
}
