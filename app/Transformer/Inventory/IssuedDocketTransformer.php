<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 08/02/2018
 * Time: 9:33
 */

namespace App\Transformer\Inventory;


use App\Models\IssuedDocketHeader;
use App\Models\MaterialRequestHeader;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class IssuedDocketTransformer extends TransformerAbstract
{
    public function transform(IssuedDocketHeader $header){
//        $date = Carbon::parse($header->date)->format('d M Y');
        $date = Carbon::parse($header->date)->toIso8601String();

        $idShowUrl = route('admin.issued_dockets.show', ['issued_docket' => $header->id]);

        $code = "<a style='text-decoration: underline;' href='" . $idShowUrl. "'>". $header->code. "</a>";

        if(!empty($header->material_request_header_id)){
            if($header->material_request_header->type === 1){
                $url = 'other';
            }
            else if($header->material_request_header->type === 2){
                $url = 'fuel';
            }
            else if($header->material_request_header->type === 3){
                $url = 'oil';
            }
            else{
                $url = 'service';
            }

            $mrShowUrl = route('admin.material_requests.'. $url. '.show', ['material_requests' => $header->material_request_header_id]);

            $mrCode = "<a style='text-decoration: underline;' href='". $mrShowUrl. "' target='_blank'>". $header->material_request_header->code. "</a>";
        }
        else{
            $mrCode = "-";
        }

        $action = "<a class='btn btn-xs btn-primary' href='". $idShowUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-eye'></i></a>";
//        $action .= "<a style='text-decoration: underline;' class='btn btn-xs btn-info' href='issued_dockets/". $header->id."/ubah' data-toggle='tooltip' data-placement='top'><i class='fa fa-pencil'></i></a>";

        $machinery = '-';
        if(!empty($header->unit_id)){
            $machinery = $header->machinery->code;
        }

        if($header->is_retur === 0){
            $returStr = 'Tidak Ada';
        }
        else if($header->is_retur === 1){
            $returStr = 'Sebagian';
        }
        else{
            $returStr = 'Semua';
        }

        return[
            'code'  => $code,
            'department'        => $header->department->name,
            'no_unit'           => $machinery,
            'type'              => $header->type === 2 ? 'BBM' : 'NON-BBM',
            'no_mr'             => $mrCode,
            'cost_code'         => $header->account->code ?? '-',
            'retur'             => $returStr ?? '-',
            'created_at'        => $date,
            'created_by'        => $header->createdBy->email,
            'action'            => $action
        ];
    }
}