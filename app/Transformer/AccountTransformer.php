<?php
/**
 * Created by PhpStorm.
 * User: hellb
 * Date: 10/2/2018
 * Time: 9:15 AM
 */

namespace App\Transformer;


use App\Models\Account;
use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;

class AccountTransformer extends TransformerAbstract
{
    public function transform(Account $account){
        $editUrl = route('admin.accounts.edit', ['account' => $account->id]);

        $action = "<a class='btn btn-xs btn-info' href='". $editUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-pencil'></i></a>";
        $action .= "<a class='delete-modal btn btn-xs btn-danger' data-id='". $account->id ."' ><i class='fa fa-trash'></i></a>";

        return[
            'code'          => $account->code,
            'location'      => $account->location ?? '-',
            'department'    => $account->department ?? '-',
            'division'      => $account->division ?? '-',
            'description'   => $account->description ?? '-',
            'created_by'    => $account->createdBy->email,
            'created_at'    => Carbon::parse($account->created_at)->toIso8601String(),
            'updated_by'    => $account->updatedBy->email,
            'updated_at'    => Carbon::parse($account->updated_at)->toIso8601String(),
            'action'        => $action
        ];
    }
}