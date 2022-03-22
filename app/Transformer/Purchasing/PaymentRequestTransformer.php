<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 14/03/2018
 * Time: 15:02
 */

namespace App\Transformer\Purchasing;


use App\Models\PaymentRequest;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PaymentRequestTransformer extends TransformerAbstract
{
    public function transform(PaymentRequest $header){
        try {
//            $date = Carbon::parse($header->date)->format('d M Y');
            $date = Carbon::parse($header->date)->toIso8601String();

            $rfpShowUrl = route('admin.payment_requests.show', ['payment_request' => $header->id]);
            $rfpEditUrl = route('admin.payment_requests.edit', ['payment_request' => $header->id]);

            $code = "<a name='" . $header->code . "' style='text-decoration: underline;' href='" . $rfpShowUrl . "'>" . $header->code . "</a>";

            $action = "<a class='btn btn-xs btn-primary' href='" . $rfpShowUrl . "' data-toggle='tooltip' data-placement='top'><i class='fa fa-eye'></i></a>";
            $action .= "<a class='btn btn-xs btn-info' href='" . $rfpEditUrl . "' data-toggle='tooltip' data-placement='top'><i class='fa fa-pencil'></i></a>";

            // Get RFP total amount based on type
            if ($header->type === 'cbd') {
                $type = "CASH BEFORE DELIVERY";
                $amount = $header->total_amount;
            } elseif ($header->type === "dp") {
                $type = "DOWN PAYMENT";
                $amount = $header->dp_amount ?? $header->total_amount;
            } else {
                $type = "NORMAL";
                $amount = $header->total_amount;
            }

            $poCodeStr = '';
            foreach ($header->payment_requests_po_details as $rfpPoDetail) {
                $poCodeStr .= $rfpPoDetail->purchase_order_header->code. '<br/>';
            }

            foreach ($header->payment_requests_pi_details as $rfpPiDetail){
                $poCodeStr .= $rfpPiDetail->purchase_invoice_header->purchase_order_header->code. '<br/>';
            }

            return[
                'code'              => $code,
                'date'              => $date,
                'type'              => $type,
                'po_codes'          => $poCodeStr,
                'amount'            => $amount,
                'supplier'          => $header->supplier->name,
                'request_by'        => $header->createdBy->name,
                'action'            => $action
            ];
        }catch(\Exception $ex){
            error_log($ex);
        }
    }

}