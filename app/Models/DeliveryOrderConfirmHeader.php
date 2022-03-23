<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 04 Mar 2022 16:00:51 +0700.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class DeliveryOrderConfirmHeader
 * 
 * @property int $id
 * @property int $delivery_order_id
 * @property string $code
 * @property string $remark
 * @property int $confirm_by
 * @property \Carbon\Carbon $confirm_date
 * @property int $status_id
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property int $updated_by
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\DeliveryOrderHeader $delivery_order_header
 * @property \Illuminate\Database\Eloquent\Collection $delivery_order_confirm_details
 *
 * @package App\Models
 */
class DeliveryOrderConfirmHeader extends Eloquent
{
	protected $casts = [
		'delivery_order_id' => 'int',
		'confirm_by' => 'int',
		'status_id' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $dates = [
		'confirm_date'
	];

	protected $fillable = [
		'delivery_order_id',
		'code',
		'remark',
		'confirm_by',
		'confirm_date',
		'status_id',
		'created_by',
		'updated_by'
	];

	public function delivery_order_header()
	{
		return $this->belongsTo(\App\Models\DeliveryOrderHeader::class, 'delivery_order_id');
	}

	public function delivery_order_confirm_details()
	{
		return $this->hasMany(\App\Models\DeliveryOrderConfirmDetail::class, 'header_id');
	}
}
