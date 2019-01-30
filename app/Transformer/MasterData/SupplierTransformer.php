<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 23/01/2018
 * Time: 10:45
 */

namespace App\Transformer\MasterData;

use App\Models\ApprovalRule;
use App\Models\Supplier;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class SupplierTransformer extends TransformerAbstract
{
    protected $mode = 'default';

    public function __construct($mode)
    {
        $this->mode = $mode;
    }

    public function transform(Supplier $supplier){

        $createdDate = Carbon::parse($supplier->created_at)->format('d M Y');
        $route = route('admin.payment_requests.before_create_pi', ['supplier' => $supplier->id]);

        $supplierEditUrl = route('admin.suppliers.edit', ['supplier' => $supplier->id]);

        if($this->mode === 'default'){
            $action =
                "<a class='btn btn-xs btn-info' href='". $supplierEditUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-pencil'></i></a>";
            $action .= "<a class='delete-modal btn btn-xs btn-danger' data-id='". $supplier->id ."' ><i class='fa fa-trash'></i></a>";
        }
        else if($this->mode === 'before_create_po'){
            $route = route('admin.payment_requests.before_create_po', ['supplier' => $supplier->id]);
            $action = "<a class='btn btn-xs btn-success' href='". $route. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-check-square'></i> Pilih Vendor </a>";
        }
        else{
            $action = "<a class='btn btn-xs btn-success' href='". $route. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-check-square'></i> Pilih Vendor </a>";
        }

        $email = $supplier->email1 ?? '-';
        if(!empty($supplier->email2)) $email .= ', '. $supplier->email2;

        $phone = $supplier->phone1 ?? '-';
        if(!empty($supplier->phone2)) $phone .= ', '. $supplier->phone2;

        return[
            'code'              => $supplier->code ?? "-",
            'name'              => $supplier->name,
            'type'              => $supplier->type === 'REGULAR' ? 'TIDAK TETAP' : 'TETAP',
            'category'          => $supplier->category ?? '-',
            'email'             => $email,
            'phone'             => $phone,
            'contact_person'    => $supplier->contact_person ?? "-",
            'city'              => $supplier->city ?? "-",
            'status'            => strtoupper($supplier->status->description),
            'created_at'        => $createdDate,
            'action'            => $action
        ];
    }
}