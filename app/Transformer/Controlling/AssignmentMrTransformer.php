<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 13/02/2018
 * Time: 11:34
 */

namespace App\Transformer\Controlling;


use App\Models\AssignmentMaterialRequest;
use App\Models\Auth\User\User;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class AssignmentMrTransformer extends TransformerAbstract
{
    public function transform(AssignmentMaterialRequest $history){

        try{
        $createdDate = Carbon::parse($history->created_at)->toIso8601String();
        $docCreatedDate = Carbon::parse($history->material_request_header->created_at)->toIso8601String();

        $processedDate = "-";
        if(!empty($history->processed_date)){
            $processedDate = Carbon::parse($history->processed_date)->toIso8601String();
        }

//        $action = "<a class='btn btn-xs btn-info' href='users/".$user->id."/ubah' data-toggle='tooltip' data-placement='top'><i class='fa fa-pencil'></i></a>";
//        $action .= "<a class='delete-modal btn btn-xs btn-danger' data-id='". $user->id ."' ><i class='fa fa-trash'></i></a>";

        return[
            'created_at'    => $createdDate,
            'mr_code'       => $history->material_request_header->code,
            'doc_created_at'=> $docCreatedDate,
            'assigned_user' => $history->assignedUser->name ?? '-',
            'assigner_user' => $history->assignerUser->name ?? '-',
            'processed_by'  => $history->processedBy->name ?? 'Belum Diproses',
            'processed_date'=> $processedDate ?? '-',
            'status'        => $history->status->description,
        ];
        }
        catch (\Exception $exception){
            error_log($exception);
        }
    }
}