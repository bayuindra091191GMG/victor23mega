<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 19/02/2018
 * Time: 10:29
 */

namespace App\Transformer\Purchasing;


use App\Models\QuotationHeader;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class QuotationHeaderTransformer extends TransformerAbstract
{
    public function transform(QuotationHeader $header){
        try{
            $createdDate = Carbon::parse($header->date)->format('d M Y');

            $rfqShowUrl = route('admin.quotations.show', ['quotation' => $header->id]);
            $rfqEditUrl = route('admin.quotations.edit', ['quotation' => $header->id]);

            $code = "<a name='". $header->code. "' href='" . $rfqShowUrl. "' target='_blank'>". $header->code. "</a>";

            $prShowurl = route('admin.purchase_requests.show', ['purchase_request' => $header->purchase_request_id]);
            $prCode = "<a name='". $header->purchase_request_header->code. "' href='" . $prShowurl. "' target='_blank'>". $header->purchase_request_header->code. "</a>";

            $action = "<a class='btn btn-xs btn-primary' href='". $rfqShowUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-eye'></i></a>";
            $action .= "<a class='btn btn-xs btn-info' href='". $rfqEditUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-pencil'></i></a>";

            return[
                'code'              => $code,
                'pr_code'           => $prCode,
                'vendor'            => $header->supplier->name ?? '-',
                'total_price'       => $header->total_price_string,
                'total_discount'    => $header->all_discount_string,
                'delivery_fee'      => $header->delivery_fee_string ?? '0',
                'ppn'               => $header->ppn_string ?? '0',
                'pph'               => $header->pph_string ?? '0',
                'total_payment'     => $header->total_payment_string,
                'status'            => $header->status->description,
                'created_at'        => $createdDate,
                'action'            => $action
            ];
        }
        catch(\Exception $ex){
            error_log($ex);
        }
    }
}