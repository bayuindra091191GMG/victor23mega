<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 08/08/2018
 * Time: 14:07
 */

namespace App\Transformer\Controlling;


use App\Models\MaterialRequestDetail;
use App\Models\MaterialRequestHeader;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use League\Fractal\TransformerAbstract;

class MonitoringMRTransformer extends TransformerAbstract
{
    public function transform(MaterialRequestHeader $header){
        $date = Carbon::parse($header->date)->toIso8601String();

        if($header->type === 1){
            $url = 'other';
        }
        else if($header->type === 2){
            $url = 'fuel';
        }
        else if($header->type === 3){
            $url = 'oil';
        }
        else{
            $url = 'service';
        }

        $mrShowUrl = route('admin.material_requests.'. $url. '.show', ['material_request' => $header->id]);

        if($header->is_approved === 0 && $header->status_id === 3){
            $code = "<a name='". $header->code. "' href='". $mrShowUrl. "' style='text-decoration: underline; font-weight: 800;'>". $header->code. "</a>";
        }
        else{
            $code = "<a name='". $header->code. "' href='". $mrShowUrl. "' style='text-decoration: underline;'>". $header->code. "</a>";
        }

        $action = "";
        $action .= "<a class='btn btn-xs btn-primary' href='". $mrShowUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-eye'></i></a>";

        $machinery = '-';
        if(!empty($header->machinery_id)){
            $machinery = $header->machinery->code;
        }

        // MR Tracking
        $isTrackingAvailable = true;
        if($header->issued_docket_headers->count() === 0 && $header->purchase_request_headers->count() === 0){
            $isTrackingAvailable = false;
        }

        $trackedPrHeaderStr = "-";
        $trackedPoHeaderStr = "-";
        $trackedGrHeaderStr = "-";
        $trackedGrHeaderLeadTime = "-";
        $trackedSjHeaderStr = "-";
        $trackedSjStatus = "-";
        $trackedSjHeaderLeadTime = "-";
        $trackedInventoryIdStr = "";
        $trackedInventoryNameStr = "";
        $trackedInventoryPartStr = "";
        $trackedInventoryUnitStr = "";
        $trackedInventoryQtyStr = "";
        $trackedInventoryPricePerPieceStr = "";
        $trackedInventoryTotalPriceStr = "";
        $trackedInventoryTotalPpnStr = "";
        $trackedInventoryTotalAllStr = "";
        $trackedPoHandleBy = "";
        $trackedPoDate = "";
        $trackedSupplierName = "";
        $trackedSupplierLocation = "";
        $trackedSjStartDate = "";
        $trackedSjFinishDate = "";
        $trackedUnPoedItemCodes = "";
        $trackedUnPoedItemPartNumbers = "";
        $trackedUnPoedItemQty = "";
        $trackedUnPoedItemUom = "";

        if($isTrackingAvailable){
            $trackedPrHeader = $header->purchase_request_headers->first();
            if(!empty($trackedPrHeader)){
                $trackedPrHeaderStr = $trackedPrHeader->code;

                $trackedPoHeaders = $trackedPrHeader->purchase_order_headers;
                $trackedGrHeaders = new Collection();
                $trackedPoHeaderStr = "";
                foreach($trackedPoHeaders as $poHeader){
                    $trackedPoHeaderStr .= $poHeader->code. "<br/>";
                    foreach($poHeader->item_receipt_headers as $grHeader){
                        $trackedGrHeaders->add($grHeader);
                    }
                }

                $trackedSjHeaders = new Collection();
                $trackedGrHeaderStr = "";
                $trackedGrHeaderLeadTime = "";
                foreach($trackedGrHeaders as $trackedGrHeader){
                    $trackedGrHeaderStr .= $trackedGrHeader->code. "<br/>";
                    if(!empty($trackedGrHeader->lead_time)){
                        $grLeadTime = $trackedGrHeader->lead_time. " Hari<br/>";
                    }
                    else{
                        $mrDate = Carbon::parse($header->date);
                        $grDate = Carbon::parse($trackedGrHeader->date);
                        $grLeadTime = $mrDate->diffInDays($grDate). " Hari<br/>";
                    }

                    $trackedGrHeaderLeadTime .= $grLeadTime;
                    foreach ($trackedGrHeader->delivery_order_headers as $sjHeader){
                        $trackedSjHeaders->add($sjHeader);
                    }
                }

                $trackedSjHeaderStr = "";
                $trackedSjStatus = "";
                $trackedSjHeaderLeadTime = "";
                foreach($trackedSjHeaders as $trackedSjHeader){
                    $trackedSjHeaderStr .= $trackedSjHeader->code. "<br/>";
                    $trackedSjStatus .= strtoupper($trackedSjHeader->status->description). "<br/>";

                    if($trackedSjHeader->status_id === 4){
                        if(!empty($trackedSjHeader->lead_time)){
                            $sjLeadTime = $trackedSjHeader->lead_time. " Hari<br/>";
                        }
                        else{
                            $mrDate = Carbon::parse($header->date);
                            $sjConfirmDate = Carbon::parse($trackedSjHeader->confirm_date);
                            $sjLeadTime = $mrDate->diffInDays($sjConfirmDate). " Hari<br/>";
                        }
                    }
                    else{
                        $sjLeadTime = "-<br/>";
                    }

                    $trackedSjHeaderLeadTime .= $sjLeadTime;

                    //Add SJ Start Time and Finish Time
                    $trackedSjStartDate .= $trackedSjHeader->date_string . "<br/>";
                    $trackedSjFinishDate .= $trackedSjHeader->confirm_date_string . "<br/>";
                }
            }

//            $trackedPiHeaders = new Collection();
//            foreach($trackedPoHeaders as $poHeader){
//                foreach($poHeader->purchase_invoice_headers as $piHeader){
//                    $trackedPiHeaders->add($piHeader);
//                }
//            }

            // Add PO details
            $tmp = $header->purchase_request_headers->first();
            if(!empty($tmp)){
                $purchaseOrders = $tmp->purchase_order_headers;

                //Data Inventory
                if(!empty($purchaseOrders)){
                    foreach ($purchaseOrders as $headerData){
                        $details = $headerData->purchase_order_details;
                        if(!empty($details)){
                            foreach ($details as $detail){
                                $trackedInventoryIdStr .= $detail->item->code . "<br/>";
                                $trackedInventoryNameStr .= $detail->item->name . "<br/>";
                                $trackedInventoryPartStr .= $detail->item->part_number . "<br/>";
                                $trackedInventoryUnitStr .= $detail->item->uom . "<br/>";
                                $trackedInventoryQtyStr .= $detail->quantity . "<br/>";
                                $trackedInventoryPricePerPieceStr .= number_format($detail->price, 2, ",", ".") . "<br/>";
                                $trackedInventoryTotalPriceStr .= number_format($detail->subtotal, 2, ",", ".") . "<br/>";

                                //Data PPN dan total harga plus PPN
                                if($headerData->ppn_amount != null){
                                    $tmpPpn = $detail->subtotal * 10 / 100;
                                    $tmpPpnTotal = $detail->subtotal + $tmpPpn;

                                    $trackedInventoryTotalPpnStr .= number_format($tmpPpn, 2, ",", ".") . "<br/>";
                                    $trackedInventoryTotalAllStr .= number_format($tmpPpnTotal, 2, ",", ".") . "<br/>";
                                }
                            }
                        }

                        //Handle By
                        $trackedPoHandleBy .= $headerData->createdBy->name . "<br/>";
                        $trackedPoDate .= $headerData->date_string . "<br/>";
                        $trackedSupplierName .= $headerData->supplier_name . "<br/>";
                        $trackedSupplierLocation .= $headerData->supplier->city . "<br/>";
                    }
                }
            }
        }

//        $trackedIdHeaderStr = "";
//        $trackedIdHeaders = $header->issued_docket_headers;
//        foreach ($trackedIdHeaders as $idHeader){
//            $trackedIdHeaderStr .= $idHeader->code. "<br/>";
//        }

        // Add item not poed details
        $prHeader2 = $header->purchase_request_headers->first();
        if(!empty($prHeader2)){
            foreach ($prHeader2->purchase_request_details as $prDetail2){
                if($prDetail2->quantity_poed < $prDetail2->quantity){
                    $qtyUnPoed = $prDetail2->quantity - $prDetail2->quantity_poed;

                    $trackedUnPoedItemCodes .= $prDetail2->item->code . "<br/>";
                    $trackedUnPoedItemPartNumbers .= $prDetail2->item->part_number . "<br/>";
                    $trackedUnPoedItemQty .= $qtyUnPoed . "<br/>";
                    $trackedUnPoedItemUom .= $prDetail2->item->uom . "<br/>";
                }
            }
        }
        else{
            foreach ($header->material_request_details as $detail){
                $trackedUnPoedItemCodes .= $detail->item->code . "<br/>";
                $trackedUnPoedItemPartNumbers .= $detail->item->part_number . "<br/>";
                $trackedUnPoedItemQty .= $detail->quantity . "<br/>";
                $trackedUnPoedItemUom .= $detail->item->uom . "<br/>";
            }
        }

        //Add more Fields
        //Check first
        $machineCode = '';
        if($header->machinery != null){
            $machineCode = $header->machinery->code;
        }

        return[
            'code_mr'       => $code,
            'date_mr'       => $date,
            'site'          => $header->site->name,
            'department'    => $header->department->name,
            'priority'      => $header->priority,
            'machinery'     => $machineCode !== "" ? $machineCode : "-",
            'item_code_unpoed'         => $trackedUnPoedItemCodes !== "" ? $trackedUnPoedItemCodes: "-",
            'item_part_number_unpoed'  => $trackedUnPoedItemPartNumbers !== "" ? $trackedUnPoedItemPartNumbers: "-",
            'qty_unpoed'    => $trackedUnPoedItemQty !== "" ? $trackedUnPoedItemQty: "-",
            'uom_unpoed'    => $trackedUnPoedItemUom !== "" ? $trackedUnPoedItemUom: "-",
            'inventory_id'  => $trackedInventoryIdStr !== "" ? $trackedInventoryIdStr : "-",
            'inventory_name'=> $trackedInventoryNameStr !== "" ? $trackedInventoryNameStr : "-",
            'part_number'   => $trackedInventoryPartStr !== "" ? $trackedInventoryPartStr : "-",
            'unit'          => $trackedInventoryUnitStr !== "" ? $trackedInventoryUnitStr : "-",
            'qty'           => $trackedInventoryQtyStr !== "" ? $trackedInventoryQtyStr : "-",
            'price'         => $trackedInventoryPricePerPieceStr !== "" ? $trackedInventoryPricePerPieceStr : "-",
            'subtotal'      => $trackedInventoryTotalPriceStr !== "" ? $trackedInventoryTotalPriceStr : "-",
            'ppn'           => $trackedInventoryTotalPpnStr !== "" ? $trackedInventoryTotalPpnStr : "-",
            'total'         => $trackedInventoryTotalAllStr !== "" ? $trackedInventoryTotalAllStr : "-",
            'user'          => $trackedPoHandleBy !== "" ? $trackedPoHandleBy : "-",
            'code_pr'       => $trackedPrHeaderStr !== "" ? $trackedPrHeaderStr : "-",
            'code_po'       => $trackedPoHeaderStr !== "" ? $trackedPoHeaderStr : "-",
            'po_date'       => $trackedPoDate !== "" ? $trackedPoDate : "-",
            'supplier_name' => $trackedSupplierName !== "" ? $trackedSupplierName : "-",
            'supplier_location' => $trackedSupplierLocation !== "" ? $trackedSupplierLocation : "-",
            'code_gr'       => $trackedGrHeaderStr !== "" ? $trackedGrHeaderStr : "-",
            'lead_time_gr'  => $trackedGrHeaderLeadTime !== "" ? $trackedGrHeaderLeadTime : "-",
            'code_sj'       => $trackedSjHeaderStr !== "" ? $trackedSjHeaderStr : "-",
            'start_sj'      => $trackedSjStartDate !== "" ? $trackedPrHeaderStr : "-",
            'finish_sj'     => $trackedSjFinishDate !== "" ? $trackedPrHeaderStr : "-",
            'status_sj'     => $trackedSjStatus !== "" ? $trackedSjStatus : "-",
            'lead_time_sj'  => $trackedSjHeaderLeadTime !== "" ? $trackedSjHeaderLeadTime : "-"
//            'code_id'       => $trackedIdHeaderStr !== "" ? $trackedIdHeaderStr : "-",
        ];
    }
}