<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 18 Jan 2018 07:30:31 +0000.
 */

namespace App\Models;

use Carbon\Carbon;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PurchaseRequestDetail
 * 
 * @property int $id
 * @property int $header_id
 * @property int $item_id
 * @property int $quantity
 * @property int $quantity_poed
 * @property int $quantity_invoiced
 * @property int $quantity_retur
 * @property string $remark
 * 
 * @property \App\Models\Item $item
 * @property \App\Models\PurchaseRequestHeader $purchase_request_header
 *
 * @package App\Models
 */
class PurchaseRequestDetail extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'header_id' => 'int',
		'item_id' => 'int',
		'quantity' => 'int',
		'quantity_poed' => 'int',
		'quantity_invoiced' => 'int',
        'quantity_retur' => 'int',
	];

	protected $dates = [
		'delivery_date'
	];

	protected $fillable = [
		'header_id',
		'item_id',
		'quantity',
        'quantity_poed',
        'quantity_invoiced',
        'quantity_retur',
		'remark'
	];

	public function getDeliveryDateAttribute(){
        return Carbon::parse($this->attributes['delivery_date'])->format('d M Y');
    }

	public function item()
	{
		return $this->belongsTo(\App\Models\Item::class);
	}

	public function purchase_request_header()
	{
		return $this->belongsTo(\App\Models\PurchaseRequestHeader::class, 'header_id');
	}
}
