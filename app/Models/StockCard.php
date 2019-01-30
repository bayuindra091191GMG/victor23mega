<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 16 May 2018 19:24:28 +0700.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class StockCard
 * 
 * @property int $id
 * @property int $item_id
 * @property int $warehouse_id
 * @property int $in_qty
 * @property float $in_cost
 * @property float $in_value
 * @property int $out_qty
 * @property float $out_cost
 * @property float $out_value
 * @property int $result_qty
 * @property int $result_qty_warehouse
 * @property string $reference
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property int $updated_by
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\Item $item
 * @property \App\Models\Auth\User\User $user
 * @property \App\Models\Warehouse $warehouse
 *
 * @package App\Models
 */
class StockCard extends Eloquent
{
	protected $casts = [
		'item_id' => 'int',
		'warehouse_id' => 'int',
		'in_qty' => 'int',
		'in_cost' => 'float',
		'in_value' => 'float',
		'out_qty' => 'int',
		'out_cost' => 'float',
		'out_value' => 'float',
        'result_qty' => 'int',
        'result_qty_warehouse' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $fillable = [
		'item_id',
		'warehouse_id',
		'in_qty',
		'in_cost',
		'in_value',
		'out_qty',
		'out_cost',
		'out_value',
        'result_qty',
        'result_qty_warehouse',
		'reference',
		'created_by',
        'created_at',
		'updated_by',
        'updated_at'
	];

	protected $appends = [
	    'date_string'
    ];

    public function scopeDateDescending(Builder $query){
        return $query->orderBy('date','DESC');
    }

    public function getDateStringAttribute(){
        return Carbon::parse($this->attributes['created_at'])->format('d M Y');
    }

	public function item()
	{
		return $this->belongsTo(\App\Models\Item::class);
	}

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'updated_by');
    }

	public function warehouse()
	{
		return $this->belongsTo(\App\Models\Warehouse::class);
	}
}
