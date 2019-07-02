<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 13/02/2018
 * Time: 11:34
 */

namespace App\Transformer\Controlling;


use App\Models\AssignmentMaterialRequest;
use App\Models\AssignmentPurchaseRequest;
use App\Models\Auth\User\User;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class AssignmentPrTransformer extends TransformerAbstract
{
    public function transform(AssignmentPurchaseRequest $history){
        try{
            $createdDate = "-";
            if(!empty($history->created_at)){
                $createdDate = Carbon::parse($history->created_at)->toIso8601String();
            }
            $docCreatedDate = "-";
            if(!empty($history->purchase_request_header->created_at)){
                $docCreatedDate = Carbon::parse($history->purchase_request_header->created_at)->toIso8601String();
            }
            $processedDate = "-";
            if(!empty($history->processed_date)){
                $processedDate = Carbon::parse($history->processed_date)->toIso8601String();
            }

//        $action = "<a class='btn btn-xs btn-info' href='users/".$user->id."/ubah' data-toggle='tooltip' data-placement='top'><i class='fa fa-pencil'></i></a>";
//        $action .= "<a class='delete-modal btn btn-xs btn-danger' data-id='". $user->id ."' ><i class='fa fa-trash'></i></a>";

            $prShowRoute = route('admin.purchase_requests.show', ['purchase_request' => $history->purchase_request_header]);
            $prCode = "<a name='". $history->purchase_request_header->code. "' style='text-decoration: underline; font-weight: bold;' href='" . $prShowRoute. "' target='_blank'>". $history->purchase_request_header->code. "</a>";

            return[
                'created_at'    => $createdDate,
                'pr_code'       => $prCode,
                'doc_created_at'=> $docCreatedDate,
                'assigned_user' => $history->assignedUser->name,
                'assigner_user' => $history->assignerUser->name ?? '-',
                'processed_by'  => $history->processedBy->name ?? 'Belum Diproses',
                'processed_date'=> $processedDate,
                'status'        => $history->status->description,
            ];
        }
        catch (\Exception $exception){
            error_log("error transformer = " . $exception);
        }
    }
}