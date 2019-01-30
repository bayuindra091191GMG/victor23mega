<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 07 Sep 2018 10:57:56 +0700.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ItemStockNotification
 * 
 * @property int $id
 * @property int $item_id
 * @property int $item_stock_id
 * @property int $warehouse_id
 * @property \Carbon\Carbon $created_at
 * @property int $created_by
 * 
 * @property \App\Models\ItemStock $item_stock
 * @property \App\Models\Item $item
 * @property \App\Models\Auth\User\User $user
 * @property \App\Models\Warehouse $warehouse
 *
 * @package App\Models
 */
class ItemStockNotification extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'item_id' => 'int',
		'item_stock_id' => 'int',
		'warehouse_id' => 'int',
		'created_by' => 'int'
	];

	protected $fillable = [
		'item_id',
		'item_stock_id',
		'warehouse_id',
		'created_by'
	];

	public function item_stock()
	{
		return $this->belongsTo(\App\Models\ItemStock::class);
	}

	public function item()
	{
		return $this->belongsTo(\App\Models\Item::class);
	}

	public function user()
	{
		return $this->belongsTo(\App\Models\Auth\User\User::class, 'created_by');
	}

	public function warehouse()
	{
		return $this->belongsTo(\App\Models\Warehouse::class);
	}
}
