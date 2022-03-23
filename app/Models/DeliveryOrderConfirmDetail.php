<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 04 Mar 2022 15:59:01 +0700.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class DeliveryOrderConfirmDetail
 * 
 * @property int $id
 * @property int $header_id
 * @property int $item_id
 * @property string $item_code
 * @property string $item_name
 * @property string $item_uom
 * @property int $qty
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property int $updated_by
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\DeliveryOrderConfirmHeader $delivery_order_confirm_header
 * @property \App\Models\Item $item
 *
 * @package App\Models
 */
class DeliveryOrderConfirmDetail extends Eloquent
{
	protected $casts = [
		'header_id' => 'int',
		'item_id' => 'int',
		'qty' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $fillable = [
		'header_id',
		'item_id',
		'item_code',
		'item_name',
		'item_uom',
		'qty',
		'created_by',
		'updated_by'
	];

	public function delivery_order_confirm_header()
	{
		return $this->belongsTo(\App\Models\DeliveryOrderConfirmHeader::class, 'header_id');
	}

	public function item()
	{
		return $this->belongsTo(\App\Models\Item::class);
	}
}
