<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 07 Sep 2018 10:59:07 +0700.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Warehouse
 * 
 * @property int $id
 * @property string $code
 * @property int $site_id
 * @property string $name
 * @property string $phone
 * @property int $pic
 * 
 * @property \App\Models\Site $site
 * @property \App\Models\Auth\User\User $user
 * @property \Illuminate\Database\Eloquent\Collection $delivery_order_headers
 * @property \Illuminate\Database\Eloquent\Collection $issued_docket_headers
 * @property \Illuminate\Database\Eloquent\Collection $item_mutations
 * @property \Illuminate\Database\Eloquent\Collection $item_receipt_headers
 * @property \Illuminate\Database\Eloquent\Collection $item_stock_notifications
 * @property \Illuminate\Database\Eloquent\Collection $item_stocks
 * @property \Illuminate\Database\Eloquent\Collection $items
 * @property \Illuminate\Database\Eloquent\Collection $retur_details
 * @property \Illuminate\Database\Eloquent\Collection $serials
 * @property \Illuminate\Database\Eloquent\Collection $stock_adjustments
 * @property \Illuminate\Database\Eloquent\Collection $stock_cards
 * @property \Illuminate\Database\Eloquent\Collection $stock_ins
 *
 * @package App\Models
 */
class Warehouse extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'site_id' => 'int',
		'pic' => 'int'
	];

	protected $fillable = [
		'code',
		'site_id',
		'name',
		'phone',
		'pic'
	];

	public function site()
	{
		return $this->belongsTo(\App\Models\Site::class);
	}

	public function user()
	{
		return $this->belongsTo(\App\Models\Auth\User\User::class, 'pic');
	}

	public function delivery_order_headers()
	{
		return $this->hasMany(\App\Models\DeliveryOrderHeader::class, 'to_warehouse_id');
	}

	public function issued_docket_headers()
	{
		return $this->hasMany(\App\Models\IssuedDocketHeader::class);
	}

	public function item_mutations()
	{
		return $this->hasMany(\App\Models\ItemMutation::class, 'to_warehouse_id');
	}

	public function item_receipt_headers()
	{
		return $this->hasMany(\App\Models\ItemReceiptHeader::class);
	}

	public function item_stock_notifications()
	{
		return $this->hasMany(\App\Models\ItemStockNotification::class);
	}

	public function item_stocks()
	{
		return $this->hasMany(\App\Models\ItemStock::class);
	}

	public function items()
	{
		return $this->hasMany(\App\Models\Item::class);
	}

	public function retur_details()
	{
		return $this->hasMany(\App\Models\ReturDetail::class);
	}

	public function serials()
	{
		return $this->hasMany(\App\Models\Serial::class);
	}

	public function stock_adjustments()
	{
		return $this->hasMany(\App\Models\StockAdjustment::class);
	}

	public function stock_cards()
	{
		return $this->hasMany(\App\Models\StockCard::class);
	}

	public function stock_ins()
	{
		return $this->hasMany(\App\Models\StockIn::class);
	}
}
