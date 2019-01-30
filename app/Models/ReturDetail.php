<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 17 May 2018 11:39:11 +0700.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ReturDetail
 * 
 * @property int $id
 * @property int $header_id
 * @property int $item_id
 * @property int $warehouse_id
 * @property int $quantity
 * @property float $price
 * @property int $discount
 * @property float $subtotal
 * @property string $remark
 * 
 * @property \App\Models\Item $item
 * @property \App\Models\ReturHeader $retur_header
 * @property \App\Models\Warehouse $warehouse
 *
 * @package App\Models
 */
class ReturDetail extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'header_id' => 'int',
		'item_id' => 'int',
        'warehouse_id' => 'int',
		'quantity' => 'int',
		'price' => 'float',
		'discount' => 'int',
		'subtotal' => 'float'
	];

	protected $fillable = [
		'header_id',
		'item_id',
        'warehouse_id',
		'quantity',
		'price',
		'discount',
		'subtotal',
		'remark'
	];

    protected $appends = [
        'price_string',
        'discount_string',
        'discount_amount_string',
        'subtotal_string'
    ];

    public function getPriceStringAttribute(){
        return number_format($this->attributes['price'], 0, ",", ".");
    }

    public function getDiscountStringAttribute(){
        return number_format($this->attributes['discount'], 0, ",", ".");
    }

    public function getDiscountAmountStringAttribute(){
        return number_format($this->attributes['discount'], 0, ",", ".");
    }

    public function getSubtotalStringAttribute(){
        return number_format($this->attributes['subtotal'], 0, ",", ".");
    }

	public function item()
	{
		return $this->belongsTo(\App\Models\Item::class);
	}

	public function retur_header()
	{
		return $this->belongsTo(\App\Models\ReturHeader::class, 'header_id');
	}

    public function warehouse()
    {
        return $this->belongsTo(\App\Models\Warehouse::class, 'warehouse_id');
    }
}
