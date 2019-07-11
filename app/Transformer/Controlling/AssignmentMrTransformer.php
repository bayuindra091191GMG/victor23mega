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

            if($history->material_request_header->type === 1){
                $mrShowRoute = route('admin.material_requests.other.show', ['material_request' => $history->material_request_header]);
            }
            elseif($history->material_request_header->type === 2){
                $mrShowRoute = route('admin.material_requests.fuel.show', ['material_request' => $history->material_request_header]);
            }
            elseif($history->material_request_header->type === 3){
                $mrShowRoute = route('admin.material_requests.oil.show', ['material_request' => $history->material_request_header]);
            }
            else{
                $mrShowRoute = route('admin.material_requests.service.show', ['material_request' => $history->material_request_header]);
            }

            $mrCode = "<a name='". $history->material_request_header->code. "' style='text-decoration: underline; font-weight: bold;' href='" . $mrShowRoute. "' target='_blank'>". $history->material_request_header->code. "</a>";

            $differentProcessor = '-';
            if($history->status_id === 18){
                $differentProcessor = $history->is_different_processor === 1 ? 'Tidak Sesuai' : 'Sesuai';
            }

            $status = $history->status->description;

            // Track PO status
            if($history->material_request_header->is_pr_created === 1){
                $prHeader = $history->material_request_header->purchase_request_headers->first();
                if($prHeader->is_all_poed === 2){
                    $status = 'Partial PO';
                }
                elseif($prHeader->is_all_poed === 1){
                    $status = 'Complete PO';
                }
            }

            return[
                'created_at'            => $createdDate,
                'mr_code'               => $mrCode,
                'doc_created_at'        => $docCreatedDate,
                'assigned_user'         => $history->assignedUser->name ?? '-',
                'assigner_user'         => $history->assignerUser->name ?? '-',
                'processed_by'          => $history->processedBy->name ?? 'Belum Diproses',
                'processed_date'        => $processedDate ?? '-',
                'different_processor'   => $differentProcessor,
                'status'                => $status
            ];
        }
        catch (\Exception $exception){
            error_log($exception);
        }
    }
}