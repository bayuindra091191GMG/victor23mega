<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 07 Sep 2018 10:38:49 +0700.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ItemStock
 * 
 * @property int $id
 * @property int $item_id
 * @property int $warehouse_id
 * @property string $location
 * @property int $stock
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property int $updated_by
 * @property \Carbon\Carbon $updated_at
 * @property int $stock_min
 * @property int $stock_max
 * @property bool $is_stock_warning
 * @property float $stock_on_order
 * 
 * @property \App\Models\Item $item
 * @property \App\Models\Warehouse $warehouse
 * @property \App\Models\Auth\User\User $user
 * @property \Illuminate\Database\Eloquent\Collection $item_stock_notifications
 *
 * @package App\Models
 */
class ItemStock extends Eloquent
{
	protected $casts = [
		'item_id' => 'int',
		'warehouse_id' => 'int',
		'stock' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int',
		'stock_min' => 'int',
		'stock_max' => 'int',
		'is_stock_warning' => 'bool',
        'stock_on_order' => 'float'
	];

	protected $appends = [
	    'site_name'
    ];

	protected $fillable = [
		'item_id',
		'warehouse_id',
		'location',
		'stock',
        'created_at',
		'created_by',
        'updated_at',
		'updated_by',
		'stock_min',
		'stock_max',
		'is_stock_warning',
        'stock_on_order'
	];

    public function getSiteNameAttribute(){
        $siteName = null;
        if ($this->warehouse) {
            $siteName = $this->warehouse->site->name ?? 'EKSPEDISI';
        }
        return $siteName;
    }

	public function item()
	{
		return $this->belongsTo(\App\Models\Item::class);
	}

	public function warehouse()
	{
		return $this->belongsTo(\App\Models\Warehouse::class);
	}

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'updated_by');
    }

	public function item_stock_notifications()
	{
		return $this->hasMany(\App\Models\ItemStockNotification::class);
	}
}
