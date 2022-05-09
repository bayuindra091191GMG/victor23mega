<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 05 Oct 2018 13:11:19 +0700.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Account
 * 
 * @property int $id
 * @property string $code
 * @property string $description
 * @property int $status_id
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property int $updated_by
 * @property \Carbon\Carbon $updated_at
 * @property string $location
 * @property string $department
 * @property string $division
 * @property string $remark
 * @property string $brand
 * @property bool $is_synced
 * @property string $created_on
 * 
 * @property \App\Models\Status $status
 * @property \App\Models\Auth\User\User $user
 * @property \Illuminate\Database\Eloquent\Collection $issued_docket_headers
 *
 * @package App\Models
 */
class Account extends Eloquent
{
	protected $casts = [
		'status_id' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int',
        'is_synced' => 'bool'
	];

	protected $fillable = [
		'code',
		'description',
		'status_id',
		'created_by',
		'updated_by',
		'location',
		'department',
		'division',
		'remark',
		'brand',
        'is_synced',
        'created_on'
	];

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

	public function issued_docket_headers()
	{
		return $this->hasMany(\App\Models\IssuedDocketHeader::class);
	}
}
