<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 07 May 2018 13:22:03 +0700.
 */

namespace App\Models;

use Carbon\Carbon;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ItemReceiptHeader
 * 
 * @property int $id
 * @property string $code
 * @property int $site_id
 * @property \Carbon\Carbon $date
 * @property int $purchase_order_id
 * @property int $warehouse_id
 * @property string $delivery_order_vendor
 * @property int $lead_time
 * @property int $status_id
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property int $updated_by
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\PurchaseOrderHeader $purchase_order_header
 * @property \App\Models\Status $status
 * @property \App\Models\Auth\User\User $user
 * @property \App\Models\Warehouse $warehouse
 * @property \Illuminate\Database\Eloquent\Collection $delivery_order_headers
 * @property \Illuminate\Database\Eloquent\Collection $item_receipt_details
 *
 * @package App\Models
 */
class ItemReceiptHeader extends Eloquent
{
	protected $casts = [
        'site_id' => 'int',
		'purchase_order_id' => 'int',
		'warehouse_id' => 'int',
        'lead_time' => 'int',
		'status_id' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $dates = [
		'date'
	];

	protected $fillable = [
		'code',
        'site_id',
		'date',
		'purchase_order_id',
		'warehouse_id',
		'delivery_order_vendor',
        'lead_time',
		'status_id',
		'created_by',
		'updated_by'
	];

    protected $appends = ['date_string'];

    public function getDateStringAttribute(){
        return Carbon::parse($this->attributes['date'])->format('d M Y');
    }

	public function purchase_order_header()
	{
		return $this->belongsTo(\App\Models\PurchaseOrderHeader::class, 'purchase_order_id');
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

	public function warehouse()
	{
		return $this->belongsTo(\App\Models\Warehouse::class);
	}

	public function delivery_order_headers()
	{
		return $this->hasMany(\App\Models\DeliveryOrderHeader::class, 'item_receipt_id');
	}

	public function item_receipt_details()
	{
		return $this->hasMany(\App\Models\ItemReceiptDetail::class, 'header_id');
	}
}
