<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 19 Mar 2018 13:33:45 +0700.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class DeliveryOrderHeader
 * 
 * @property int $id
 * @property string $code
 * @property int $item_receipt_id
 * @property int $from_site_id
 * @property int $to_site_id
 * @property int $from_warehouse_id
 * @property int $to_warehouse_id
 * @property int $machinery_id
 * @property string $remark
 * @property \Carbon\Carbon $date
 * @property int $confirm_by
 * @property \Carbon\Carbon $confirm_date
 * @property int $lead_time
 * @property int $cancel_by
 * @property \Carbon\Carbon $cancel_date
 * @property bool $is_partial_confirmed
 * @property bool $is_all_confirmed
 * @property bool $is_synced
 * @property int $status_id
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property int $updated_by
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\Machinery $machinery
 * @property \App\Models\ItemReceiptHeader $item_receipt_header
 * @property \App\Models\Site $site
 * @property \App\Models\Status $status
 * @property \App\Models\Auth\User\User $user
 * @property \App\Models\Warehouse $warehouse
 * @property \Illuminate\Database\Eloquent\Collection $delivery_order_details
 *
 * @package App\Models
 */
class DeliveryOrderHeader extends Eloquent
{
	protected $casts = [
		'item_receipt_id' => 'int',
		'from_site_id' => 'int',
		'to_site_id' => 'int',
		'from_warehouse_id' => 'int',
		'to_warehouse_id' => 'int',
		'machinery_id' => 'int',
		'confirm_by' => 'int',
        'lead_time' => 'int',
		'cancel_by' => 'int',
        'is_partial_confirmed' => 'bool',
        'is_all_confirmed' => 'bool',
        'is_synced' => 'bool',
		'status_id' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $dates = [
		'confirm_date',
		'cancel_date'
	];

	protected $fillable = [
		'code',
		'item_receipt_id',
		'from_site_id',
		'to_site_id',
		'from_warehouse_id',
		'to_warehouse_id',
		'machinery_id',
		'remark',
        'date',
		'confirm_by',
		'confirm_date',
        'lead_time',
		'cancel_by',
		'cancel_date',
        'is_partial_confirmed',
        'is_all_confirmed',
        'is_synced',
		'status_id',
		'created_by',
		'updated_by'
	];

    protected $appends = [
        'date_string',
        'confirm_date_string'
    ];

    public function scopeDateDescending(Builder $query){
        return $query->orderBy('date','DESC');
    }

    public function getDateStringAttribute(){
        return Carbon::parse($this->attributes['date'])->format('d M Y');
    }

    public function getConfirmDateStringAttribute(){
        return Carbon::parse($this->attributes['confirm_date'])->format('d M Y');
    }

	public function machinery()
	{
		return $this->belongsTo(\App\Models\Machinery::class);
	}

    public function toSite()
    {
        return $this->belongsTo(\App\Models\Site::class, 'to_site_id');
    }

    public function fromSite()
    {
        return $this->belongsTo(\App\Models\Site::class, 'from_site_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(\App\Models\Warehouse::class, 'to_warehouse_id');
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(\App\Models\Warehouse::class, 'from_warehouse_id');
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

    public function confirmBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'confirm_by');
    }

    public function cancelBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'cancel_by');
    }

	public function delivery_order_details()
	{
		return $this->hasMany(\App\Models\DeliveryOrderDetail::class, 'header_id');
	}

	public function item_receipt_header(){
        return $this->belongsTo(\App\Models\ItemReceiptHeader::class, 'item_receipt_id');
    }
}
