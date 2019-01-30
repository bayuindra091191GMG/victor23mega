<?php

/**
 * Created by Reliese Model.
 * Date: Sun, 24 Jun 2018 17:01:49 +0700.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ApprovalMaterialRequest
 * 
 * @property int $id
 * @property int $material_request_id
 * @property int $user_id
 * @property int $status_id
 * @property \Carbon\Carbon $created_at
 * @property int $created_by
 * @property \Carbon\Carbon $updated_at
 * @property int $updated_by
 * @property string $priority
 * 
 * @property \App\Models\MaterialRequestHeader $material_request_header
 * @property \App\Models\Auth\User\User $user
 *
 * @package App\Models
 */
class ApprovalMaterialRequest extends Eloquent
{
	protected $casts = [
		'material_request_id' => 'int',
		'user_id' => 'int',
        'status_id' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $fillable = [
		'material_request_id',
		'user_id',
        'status_id',
		'created_by',
		'updated_by',
        'priority'
	];

	public function material_request_header()
	{
		return $this->belongsTo(\App\Models\MaterialRequestHeader::class, 'material_request_id');
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
}
