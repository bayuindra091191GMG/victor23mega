<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 17/05/2018
 * Time: 11:47
 */

namespace App\Transformer\Purchasing;


use App\Models\ReturHeader;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ReturHeaderTransformer extends TransformerAbstract
{
    public function transform(ReturHeader $header){
        try{
            $createdDate = Carbon::parse($header->date)->format('d M Y');

            $returShowUrl = route('admin.returs.show', ['retur' => $header->id]);
            $returEditUrl = route('admin.returs.edit', ['retur' => $header->id]);

            $code = "<a href='returs/detil/" . $header->id. "' target='_blank'>". $header->code. "</a>";
            $action = "<a class='btn btn-xs btn-primary' href='". $returShowUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-eye'></i></a>";
            $action .= "<a class='btn btn-xs btn-info' href='". $returEditUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-pencil'></i></a>";

            return[
                'code'          => $code,
                'pi_code'       => $header->purchase_invoice_header->code,
                'supplier'      => $header->purchase_invoice_header->purchase_order_header->supplier->name,
                'total_price'   => $header->total_price_string,
                'discount'      => $header->total_discount_string ?? '0',
                'delivery_fee'  => $header->delivery_fee_string ?? '0',
                'ppn'           => $header->ppn_string ?? '0',
                'total_payment' => $header->total_payment_string,
                'created_at'    => $createdDate,
                'action'        => $action
            ];
        }
        catch(\Exception $ex){
            error_log($ex);
        }
    }
}