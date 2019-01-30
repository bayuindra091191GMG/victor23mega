<?php
/**
 * Created by PhpStorm.
 * User: hellb
 * Date: 2/18/2018
 * Time: 4:51 PM
 */

namespace App\Transformer\Purchasing;


use App\Models\PurchaseOrderHeader;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PurchaseOrderHeaderTransformer extends TransformerAbstract
{
    protected $mode = 'default';

    public function __construct($mode)
    {
        $this->mode = $mode;
    }

    public function transform(PurchaseOrderHeader $header){
        try{
//            $date = Carbon::parse($header->date)->format('d M Y');
            $date = Carbon::parse($header->date)->toIso8601String();

            $poRoute = route('admin.purchase_orders.show', ['purchase_order' => $header->id]);
            $poEditRoute = route('admin.purchase_orders.edit', ['purchase_order' => $header->id]);
            $prRoute = route('admin.purchase_requests.show', ['purchase_request' => $header->purchase_request_id]);

            if($header->is_approved === 0 && $header->status_id === 3){
                $code = "<a name='". $header->code. "' style='text-decoration: underline; font-weight: 800;' href='" . $poRoute. "' target='_blank'>". $header->code. "</a>";
            }
            else{
                $code = "<a name='". $header->code. "' style='text-decoration: underline;' href='" . $poRoute. "' target='_blank'>". $header->code. "</a>";
            }
            $prCode =  "<a name='". $header->purchase_request_header->code. "' style='text-decoration: underline;' href='" . $prRoute. "' target='_blank'>". $header->purchase_request_header->code. "</a>";

            $action = "";

            if($this->mode === 'default'){
                $action = "<a class='btn btn-xs btn-primary' href='". $poRoute. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-eye'></i></a>";
                $action .= "<a class='btn btn-xs btn-info' href='". $poEditRoute. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-pencil'></i></a>";
            }
            else if($this->mode === 'before_create_rfp'){
                $action = "<input type='checkbox' class='flat' id='chk". $header->id ."' name='chk[]' onclick='changeInput(". $header->id .");'/>";
                $action .= "<input type='text' id='" . $header->id ."' hidden='true' name='ids[]' value='' readonly />";
            }
            else if($this->mode === 'before_create_gr'){
                $route = route('admin.item_receipts.create', ['po' => $header->id]);
                $action = "<a class='btn btn-xs btn-success' href='". $route. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-file-text'></i> Proses GR </a>";
            }
            else{
                $route = route('admin.purchase_invoices.create', ['po' => $header->id]);
                $action = "<a class='btn btn-xs btn-success' href='". $route. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-file-text'></i> Proses Invoice </a>";
            }

            if($header->status_id === 13){
                $approvalStr = 'Rejected';
            }
            else{
                if($header->is_approved === 1){
                    $approvalStr = 'Approved';
                }
                else{
                    $approvalStr = 'Pending';
                }
            }

            return[
                'code'              => $code,
                'pr_code'           => $prCode,
                'created_at'        => $date,
                'priority'          => $header->purchase_request_header->priority,
                'supplier'          => $header->supplier->name,
                'total_payment'     => $header->total_payment,
                'is_approved'       => $approvalStr,
                'status'            => $header->status->description,
                'action'            => $action
            ];
        }catch(\Exception $ex){
            error_log($ex);
        }
    }
}