<?php
/**
 * Created by PhpStorm.
 * User: hellb
 * Date: 3/20/2018
 * Time: 11:35 AM
 */

namespace App\Transformer\Inventory;


use App\Models\MaterialRequestHeader;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class MaterialRequestHeaderTransformer extends TransformerAbstract
{
    protected $type = 'default';
    protected $mode = 'default';

    public function __construct($mode)
    {
        $this->mode = $mode;
    }

    public function transform(MaterialRequestHeader $header){
//        $date = Carbon::parse($header->date)->format('d M Y');
        $date = Carbon::parse($header->date)->toIso8601String();
//        $createdAt = Carbon::parse($header->created_at)->format('d M Y');

        if($header->type === 1){
            $url = 'other';
            $typeStr = 'Part/Non-Part';
        }
        else if($header->type === 2){
            $url = 'fuel';
            $typeStr = 'BBM';
        }
        else if($header->type === 3){
            $url = 'oil';
            $typeStr = 'Oli';
        }
        else{
            $url = 'service';
            $typeStr = 'Servis';
        }

        $mrShowUrl = route('admin.material_requests.'. $url. '.show', ['material_request' => $header->id]);
        $mrEditUrl = route('admin.material_requests.'. $url. '.edit', ['material_request' => $header->id]);

        if($header->is_approved === 0 && $header->status_id === 3){
            $code = "<a name='". $header->code. "' href='". $mrShowUrl. "' style='text-decoration: underline; font-weight: 800;'>". $header->code. "</a>";
        }
        else{
            $code = "<a name='". $header->code. "' href='". $mrShowUrl. "' style='text-decoration: underline;'>". $header->code. "</a>";
        }

        $action = "";
        $route = route('admin.purchase_requests.create', ['mr' => $header->id]);
        if($this->mode === 'before_create_pr'){
            $code = "<a name='". $header->code. "' href='". $mrShowUrl. "' style='text-decoration: underline;' target='_blank'>". $header->code. "</a>";
            $action = "<a class='btn btn-xs btn-success' href='". $route . "' data-toggle='tooltip' data-placement='top'><i class='fa fa-file-text'></i> Buat PR </a>";
        }
        else if($this->mode === 'before_create_id'){
            $code = "<a name='". $header->code. "' href='". $mrShowUrl. "' style='text-decoration: underline;' target='_blank'>". $header->code. "</a>";
            $routeIssuedDocket= route('admin.issued_dockets.create', ['mr' => $header->id]);
            $action = "<a class='btn btn-xs btn-success' href='". $routeIssuedDocket . "' data-toggle='tooltip' data-placement='top'><i class='fa fa-file-text'></i> Proses Issued Docket </a>";
        }
        else{
            $action .= "<a class='btn btn-xs btn-primary' href='". $mrShowUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-eye'></i></a>";
        }

        $machinery = '-';
        if(!empty($header->machinery_id)){
            $machinery = $header->machinery->code;
        }

//        if($header->status_id === 13){
//            $approvalStr = 'Rejected';
//        }
//        else{
//            if($header->is_approved === 1){
//                $approvalStr = 'Approved';
//            }
//            else{
//                $approvalStr = 'Pending';
//            }
//        }

        return[
            'code'          => $code,
            'type'          => $typeStr,
            'purpose'       => $header->purpose === 'stock' ? 'Stock' : 'Non-Stock',
            'priority'      => $header->priority,
            'department'    => $header->department->name,
            'machinery'     => $machinery,
            'created_by'    => $header->createdBy->email,
            'date'          => $date,
            'status'        => $header->status->description,
            'action'        => $action
        ];
    }
}