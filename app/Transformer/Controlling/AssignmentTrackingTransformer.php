<?php


namespace App\Transformer\Controlling;


use App\Models\AssignmentMaterialRequest;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class AssignmentTrackingTransformer extends TransformerAbstract
{
    public function transform(AssignmentMaterialRequest $history){

        try{
            $createdDate = Carbon::parse($history->created_at)->toIso8601String();
            //$docCreatedDate = Carbon::parse($history->material_request_header->created_at)->toIso8601String();

            $processedMrDate = "-";
            if(!empty($history->processed_date)){
                $processedMrDate = Carbon::parse($history->processed_date)->toIso8601String();
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

            $differentMrProcessor = '-';
            if($history->status_id === 18){
                $differentMrProcessor = $history->is_different_processor === 1 ? 'Tidak Sesuai' : 'Sesuai';
            }

            // Tracking data
            $differentPrProcessor = '-';
            $trackStatus = 'Belum Proses MR ke PR';

            $prCode = '-';
            $poCode = '-';
            $processedPrDate = '-';
            if($history->material_request_header->is_pr_created === 1){
                $trackStatus = 'Sudah Proses MR ke PR';
                $prHeader = $history->material_request_header->purchase_request_headers->first();
                $processedMrDate = Carbon::parse($prHeader->created_at)->toIso8601String();
                $processedPrDate = !empty($prHeader->all_poed_processed_date) ? Carbon::parse($prHeader->all_poed_processed_date)->toIso8601String() : '-';

                // Get PR detail url
                $prShowRoute = route('admin.purchase_requests.show', ['purchase_request' => $prHeader]);
                $prCode = "<a name='". $prHeader->code. "' style='text-decoration: underline; font-weight: bold;' href='" . $prShowRoute. "' target='_blank'>". $prHeader->code. "</a>";

                // Get PO datas
                if($prHeader->purchase_order_headers->count() > 0){
                    $poCode = '';
                    foreach ($prHeader->purchase_order_headers as $poHeader){
                        $poShowRoute = route('admin.purchase_orders.show', ['purchase_order' => $poHeader]);
                        $poCode .= "<a name='". $poHeader->code. "' style='text-decoration: underline; font-weight: bold;' href='" . $poShowRoute. "' target='_blank'>". $poHeader->code. "</a><br/>";
                    }
                }

                if($prHeader->is_all_poed === 1){
                    $differentPrProcessor = $prHeader->processed_by !== $history->assigned_user_id ? 'Tidak Sesuai' : 'Sesuai';
                    $trackStatus = 'Sudah Proses PR ke PO';
                }
                elseif ($prHeader->is_all_poed === 2){
                    $trackStatus = 'Sebagian Proses PR ke PO';
                }
            }

            return[
                'assigned_user'         => $history->assignedUser->name ?? '-',
                'assigner_user'         => $history->assignerUser->name ?? '-',
                'created_at'            => $createdDate,
                'track_status'          => $trackStatus,
                'mr_code'               => $mrCode,
                'processed_mr_date'     => $processedMrDate,
                'different_mr_processor'=> $differentMrProcessor,
                'pr_code'               => $prCode,
                'processed_pr_date'     => $processedPrDate,
                'different_pr_processor'=> $differentPrProcessor,
                'po_code'               => $poCode
            ];
        }
        catch (\Exception $exception){
            error_log($exception);
        }
    }
}