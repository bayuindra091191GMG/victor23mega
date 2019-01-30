<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 08/02/2018
 * Time: 9:33
 */

namespace App\Transformer\Purchasing;


use App\Models\PurchaseRequestHeader;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PurchaseRequestHeaderTransformer extends TransformerAbstract
{
    protected $mode = 'default';

    public function __construct($mode)
    {
        $this->mode = $mode;
    }

    public function transform(PurchaseRequestHeader $header){
//        $date = Carbon::parse($header->date)->format('d M Y');
        $date = Carbon::parse($header->date)->toIso8601String();
//        $createdAt = Carbon::parse($header->created_at)->format('d M Y - H:i');
//        $priorityLimitDate = Carbon::parse($header->priority_limit_date)->format('d M Y');

        $prShowUrl = route('admin.purchase_requests.show', ['purchase_request' => $header->id]);
        $prEditUrl = route('admin.purchase_requests.edit', ['purchase_request' => $header->id]);
        $code = "<a name='". $header->code. "' href='". $prShowUrl. "' style='text-decoration: underline;'>". $header->code. "</a>";

        // Check MR type
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

        $mrShowUrl = route('admin.material_requests.'. $url. '.show', ['material_requests' => $header->material_request_id]);

        $mrCode = "<a name='". $header->material_request_header->code. "' href='". $mrShowUrl. "' style='text-decoration: underline;' target='_blank'>". $header->material_request_header->code. "</a>";

        $action = "";
        if($this->mode === 'default'){
            $action = "<a class='btn btn-xs btn-primary' href='". $prShowUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-eye'></i></a>";
            $action .= "<a class='btn btn-xs btn-info' href='". $prEditUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-pencil'></i></a>";
        }
        elseif($this->mode === 'before_create_rfq'){
            $route = route('admin.quotations.create', ['pr' => $header->id]);
            $action = "<a class='btn btn-xs btn-success' href='". $route. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-file-text'></i> Proses RFQ </a>";
        }
        elseif($this->mode === 'before_create_empty_rfq'){
            $route = route('admin.quotations.create_empty', ['pr' => $header->id]);
            $action = "<a class='btn btn-xs btn-success' href='". $route. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-file-text'></i> Proses RFQ Kosong</a>";
        }
        else{
            $code = "<a href='" . $prShowUrl. "' style='text-decoration: underline;' target='_blank'>". $header->code. "</a>";
            $route = route('admin.purchase_orders.create', ['pr' => $header->id]);
            $action = "<a class='btn btn-xs btn-success' href='". $route. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-file-text'></i> Proses PO </a>";
        }

        $machinery = '-';
        if(!empty($header->machinery_id)){
            $machinery = $header->machinery->code;
        }

        return[
            'code'          => $code,
            'mr_code'       => $mrCode,
            'priority'      => $header->priority,
            'is_all_poed'   => $header->is_all_poed,
            'department'    => $header->department->name,
            'machinery'     => $machinery,
            'created_by'    => $header->createdBy->email,
            'date'          => $date,
            'status'        => $header->status->description,
            'action'        => $action
        ];
    }
}