<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 02/03/2018
 * Time: 15:04
 */

namespace App\Transformer\Inventory;


use App\Models\DeliveryOrderHeader;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class DeliveryOrderHeaderTransformer extends TransformerAbstract
{
    public function transform(DeliveryOrderHeader $header){
        $date = Carbon::parse($header->date)->format('d M Y');

        $doShowUrl = route('admin.delivery_orders.show', ['delivery_order' => $header->id]);

        $code = "<a name='". $header->code. "' href='" . $doShowUrl. "'>". $header->code. "</a>";

        $prCode = "-";
        $grCode = "-";
        if(!empty($header->item_receipt_id)){
            $poShowUrl = route('admin.purchase_requests.show', ['purchase_request' => $header->item_receipt_header->purchase_order_header->purchase_request_id]);
            $grShowUrl = route('admin.item_receipts.show', ['item_receipt' => $header->item_receipt_id]);
            $prCode =  "<a name='". $header->item_receipt_header->purchase_order_header->purchase_request_header->code. "' href='" . $poShowUrl. "'>". $header->item_receipt_header->purchase_order_header->purchase_request_header->code. "</a>";
            $grCode =  "<a name='". $header->item_receipt_header->code. "' href='" . $grShowUrl. "'>". $header->item_receipt_header->code. "</a>";
        }

        $action = "<a class='btn btn-xs btn-primary' href='". $doShowUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-eye'></i></a>";

        $status = $header->status->description;
        if($header->is_partial_confirmed){
            $status .= " - Terkonfirmasi Parsial";
        }

        return[
            'code'              => $code,
            'pr_code'           => $prCode,
            'gr_code'           => $grCode,
            'from_warehouse'    => $header->fromWarehouse->name,
            'to_warehouse'      => $header->toWarehouse->name,
            'machinery'         => $header->machinery->code ?? "-",
            'remark'            => $header->remark ?? "-",
            'created_at'        => $date,
            'status'            => $status,
            'action'            => $action
        ];
    }
}