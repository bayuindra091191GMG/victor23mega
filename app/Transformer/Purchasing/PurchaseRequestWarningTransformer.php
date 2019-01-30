<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 30/08/2018
 * Time: 10:22
 */

namespace App\Transformer\Purchasing;


use App\Models\PurchaseRequestHeader;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PurchaseRequestWarningTransformer extends TransformerAbstract
{
    public function __construct()
    {

    }

    public function transform(PurchaseRequestHeader $header){
        $date = Carbon::parse($header->date)->toIso8601String();
        $expiredDate = Carbon::parse($header->priority_limit_date)->toIso8601String();

        $prShowUrl = route('admin.purchase_requests.show', ['purchase_request' => $header->id]);
        $code = "<a name='". $header->code. "' href='". $prShowUrl. "' style='text-decoration: underline;'>". $header->code. "</a>";

        // Check MR type
//        if($header->material_request_header->type === 1){
//            $url = 'other';
//        }
//        else if($header->material_request_header->type === 2){
//            $url = 'fuel';
//        }
//        else if($header->material_request_header->type === 3){
//            $url = 'oil';
//        }
//        else{
//            $url = 'service';
//        }
//
//        $mrShowUrl = route('admin.material_requests.'. $url. '.show', ['material_requests' => $header->material_request_id]);
//
//        $mrCode = "<a name='". $header->material_request_header->code. "' href='". $mrShowUrl. "' style='text-decoration: underline;' target='_blank'>". $header->material_request_header->code. "</a>";

        $action = "<a class='btn btn-xs btn-primary' href='". $prShowUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-eye'></i></a>";

//        $machinery = '-';
//        if(!empty($header->machinery_id)){
//            $machinery = $header->machinery->code;
//        }

        // Get expired detail
        if($header->priority_expired){
            $expiredStr = "SUDAH JATUH TEMPO";
        }
        else{
            $expiredStr = "AKAN JATUH TEMPO";
        }

        return[
            'code'              => $code,
            'date'              => $date,
            'priority'          => $header->priority,
            'expired_warning'   => $expiredStr,
            'expired_date'      => $expiredDate,
            'department'        => $header->department->name,
            'created_by'        => $header->createdBy->email,
            'status'            => $header->status->description,
            'action'            => $action
        ];
    }
}