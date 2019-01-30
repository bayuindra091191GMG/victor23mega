<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 19 Apr 2018 11:20:20 +0700.
 */

namespace App\Models;

use Carbon\Carbon;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ApprovalPurchaseOrder
 * 
 * @property int $id
 * @property int $purchase_order_id
 * @property int $user_id
 * @property int $status_id
 * @property \Carbon\Carbon $created_at
 * @property int $created_by
 * @property \Carbon\Carbon $updated_at
 * @property int $updated_by
 * 
 * @property \App\Models\User $user
 * @property \App\Models\PurchaseOrderHeader $purchase_order_header
 *
 * @package App\Models
 */
class ApprovalPurchaseOrder extends Eloquent
{
	protected $casts = [
		'purchase_order_id' => 'int',
		'user_id' => 'int',
        'status_id' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

    protected $appends = [
        'created_at_string'
    ];

	protected $fillable = [
		'purchase_order_id',
		'user_id',
        'status_id',
        'created_at',
		'created_by',
		'updated_by',
        'updated_at'
	];

    public function getCreatedAtStringAttribute(){
        return Carbon::parse($this->attributes['created_at'])->format('d M Y');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(\App\Models\Status::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'updated_by');
    }

	public function purchase_order_header()
	{
		return $this->belongsTo(\App\Models\PurchaseOrderHeader::class, 'purchase_order_id');
	}
}
