<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 14/03/2018
 * Time: 15:02
 */

namespace App\Transformer\Purchasing;


use App\Models\PurchaseInvoiceHeader;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PurchaseInvoiceHeaderTransformer extends TransformerAbstract
{
    protected $mode = 'default';

    public function __construct($mode)
    {
        $this->mode = $mode;
    }

    public function transform(PurchaseInvoiceHeader $header){
        try{
//            $date = Carbon::parse($header->date)->format('d M Y');
            $date = Carbon::parse($header->date)->toIso8601String();

            $piShowUrl = route('admin.purchase_invoices.show', ['purchase_invoice' => $header->id]);
            $piEditUrl = route('admin.purchase_invoices.edit', ['purchase_invoice' => $header->id]);
            $poRoute = route('admin.purchase_orders.show', ['purchase_order' => $header->purchase_order_id]);

            $code = "<a name='". $header->code. "' style='text-decoration: underline;' href='" . $piShowUrl. "' target='_blank'>". $header->code. "</a>";
            $poCode =  "<a name='". $header->purchase_order_header->code. "' style='text-decoration: underline;' href='" . $poRoute. "' target='_blank'>". $header->purchase_order_header->code. "</a>";

            if($this->mode === 'default'){
                $action = "<a class='btn btn-xs btn-primary' href='". $piShowUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-eye'></i></a>";
                $action .= "<a class='btn btn-xs btn-info' href='". $piEditUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-pencil'></i></a>";
                $action .= "<a class='delete-modal btn btn-xs btn-danger' data-id='". $header->id ."' ><i class='fa fa-dollar'></i></a>";
            }
            else if($this->mode === 'before_create_retur'){
                $route = route('admin.returs.create', ['pi' => $header->id]);
                $action = "<a class='btn btn-xs btn-success' href='". $route. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-check-square'></i> Proses Retur </a>";
            }
            else{
                $action = "<input type='checkbox' class='flat' id='chk". $header->id ."' name='chk[]' onclick='changeInput(". $header->id .");'/>";
                $action .= "<input type='text' id='" . $header->id ."' hidden='true' name='ids[]' value='' readonly />";
            }

            $machinery = $header->purchase_order_header->purchase_request_header->machinery;

            return[
                'code'              => $code,
                'po_code'           => $poCode,
                'supplier'          => $header->purchase_order_header->supplier->name,
                'total_price'       => $header->total_price_string,
                'total_discount'    => $header->all_discount_string,
                'delivery_fee'      => $header->delivery_fee_string ?? '-',
                'total_payment'     => $header->total_payment,
                'repayment_amount'  => $header->repayment_amount,
                'department'        => $header->purchase_order_header->purchase_request_header->department->name,
                'machinery'         => !empty($machinery) ? $machinery->code. ' ('. $machinery->machinery_category->name. ' - '. $machinery->machinery_brand->name. ')' : '-',
                'date'              => $date,
                'created_at'        => $date,
                'action'            => $action
            ];
        }catch(\Exception $ex){
            error_log($ex);
        }
    }

}