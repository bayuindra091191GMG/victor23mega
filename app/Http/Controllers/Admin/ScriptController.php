<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\ItemReceiptHeader;
use App\Models\ItemStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ScriptController extends Controller
{
    public function revertItemReceipt(int $id){
        try{
            $itemReceipt = ItemReceiptHeader::find($id);
            if(empty($itemReceipt)){
                return 'INVALID';
            }

            foreach ($itemReceipt->item_receipt_details as $detail){
                // Revert stock
                $itemStockData = ItemStock::where('item_id', $detail->item_id)
                    ->where('warehouse_id', $itemReceipt->warehouse_id)
                    ->first();

                if(!empty($itemStockData)){
                    $itemStockData->stock = $itemStockData->stock - $detail->quantity;
                    $itemStockData->stock_on_order += (double) $detail->quantity;
                    $itemStockData->save();
                }

                // Revert PO data
                $poDetail = $itemReceipt->purchase_order_header->purchase_order_details->where('item_id', $detail->item_id)->first();
                $poDetail->received_quantity = $detail->received_quantity - $detail->quantity;
                $poDetail->save();

                // Revert average value
                $itemData = $poDetail->item;

                $oldValue = $itemData->stock * $itemData->value;
                $newValue = $poDetail->quantity * $poDetail->price;
                if($itemData->stock - $poDetail->quantity > 0){
                    $averageValue = ($oldValue - $newValue) / ($itemData->stock - $poDetail->quantity);
                    $itemData->value = round($averageValue);
                }
                else{
                    $itemData->value = 0;
                }

                // Revert master item data
                $itemData->stock -= $detail->quantity;
                $itemData->stock_on_order += $detail->quantity;
                $itemData->save();

                // Delete stock card
                DB::table('stock_cards')
                    ->where('item_id', $detail->item_id)
                    ->where('warehouse_id', $itemReceipt->warehouse_id)
                    ->where('reference', 'Goods Receipt '. $itemReceipt->code)
                    ->delete();

                // Revert MR data
                $mrDetail = $itemReceipt->purchase_order_header->purchase_request_header->material_request_header->material_request_details->where('item_id', $detail->item_id)->first();
                $mrDetail->quantity_received -= $detail->quantity;
                $mrDetail->save();

                // Delete item receipt detail
                $detail->delete();
            }

            // Revert MR status
            $mrHeader = $itemReceipt->purchase_order_header->purchase_request_header->material_request_header;
            $mrHeader->status_id = 3;
            $mrHeader->save();

            // Revert PO data
            $poHeader = $itemReceipt->purchase_order_header;
            $isPartialReceived = false;
            foreach ($poHeader->purchase_order_details as $detail){
                if($detail->received_quantity > 0){
                    $isPartialReceived = true;
                }
            }

            if($isPartialReceived){
                $poHeader->is_all_received = 2;
                $poHeader->save();
            }
            else{
                $poHeader->is_all_received = 0;
                $poHeader->save();
            }

            // Delete item receipt header
            $itemReceipt->delete();

            return 'SCRIPT SUCCESS!!';
        }
        catch (\Exception $ex){
            return $ex;
        }
    }
}