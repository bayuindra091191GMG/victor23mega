<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 08/02/2018
 * Time: 9:33
 */

namespace App\Transformer\Inventory;


use App\Models\ItemReceiptHeader;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ItemReceiptTransformer extends TransformerAbstract
{
    public function transform(ItemReceiptHeader $header){
//        $createdDate = Carbon::parse($header->created_at)->format('d M Y');
//        $date = Carbon::parse($header->date)->format('d M Y');
        $createdDate = Carbon::parse($header->created_at)->toIso8601String();
        $date = Carbon::parse($header->date)->toIso8601String();

        $grShowUrl = route('admin.item_receipts.show', ['item_receipt' => $header->id]);

        $code = "<a name='". $header->code. "' style='text-decoration: underline;' href='" . $grShowUrl. "'>". $header->code. "</a>";

        $poRoute = route('admin.purchase_orders.show', ['purchase_order' => $header->purchase_order_id]);
        $poCode =  "<a name='". $header->purchase_order_header->code. "' style='text-decoration: underline;' href='" . $poRoute. "' target='_blank'>". $header->purchase_order_header->code. "</a>";

        $action = "<a class='btn btn-xs btn-primary' href='". $grShowUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-eye'></i></a>";

        return[
            'code'              => $code,
            'po_code'           => $poCode,
            'no_sj_spb'         => $header->delivery_order_vendor ?? '-',
            'date'              => $date,
            'created_at'        => $createdDate,
            'created_by'        => $header->createdBy->email,
            'action'            => $action
        ];
    }
}