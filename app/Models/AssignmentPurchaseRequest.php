<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 01 Jul 2019 10:39:02 +0700.
 */

namespace App\Models;

use Carbon\Carbon;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AssignmentPurchaseRequest
 * 
 * @property int $id
 * @property int $purchase_request_id
 * @property int $assigned_user_id
 * @property int $assigner_user_id
 * @property int $processed_by
 * @property int $is_different_processor
 * @property \Carbon\Carbon $processed_date
 * @property int $status_id
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property int $updated_by
 * @property \Carbon\Carbon $updated_at
 *
 * @property \App\Models\Auth\User\User $assignedUser
 * @property \App\Models\Auth\User\User $assignerUser
 * @property \App\Models\Auth\User\User $processedBy
 * @property \App\Models\PurchaseRequestHeader $purchase_request_header
 * @property \App\Models\Status $status
 *
 * @package App\Models
 */
class AssignmentPurchaseRequest extends Eloquent
{
	protected $casts = [
		'purchase_request_id' => 'int',
		'assigned_user_id' => 'int',
		'assigner_user_id' => 'int',
		'processed_by' => 'int',
		'is_different_processor' => 'int',
		'status_id' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $dates = [
		'processed_date'
	];

	protected $fillable = [
		'purchase_request_id',
		'assigned_user_id',
		'assigner_user_id',
		'processed_by',
		'is_different_processor',
		'processed_date',
		'status_id',
		'created_by',
        'created_at',
		'updated_by',
        'updated_at'
	];

    protected $appends = [
        'created_at_string'
    ];

    public function getCreatedAtStringAttribute(){
        return Carbon::parse($this->attributes['created_at'])->format('d M Y');
    }

    public function assignedUser()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'assigned_user_id');
    }

    public function assignerUser()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'assigner_user_id');
    }
    public function processedBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'processed_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'updated_by');
    }

	public function purchase_request_header()
	{
		return $this->belongsTo(\App\Models\PurchaseRequestHeader::class, 'purchase_request_id');
	}

	public function status()
	{
		return $this->belongsTo(\App\Models\Status::class);
	}
}
