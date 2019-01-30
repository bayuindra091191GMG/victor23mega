<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 31 Aug 2018 15:09:43 +0700.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Site
 * 
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $location
 * @property string $phone
 * @property string $pic
 * @property \Carbon\Carbon $created_at
 * @property int $created_by
 * @property \Carbon\Carbon $updated_at
 * @property int $updated_by
 * 
 * @property \App\Models\Auth\User\User $user
 * @property \Illuminate\Database\Eloquent\Collection $delivery_order_headers
 * @property \Illuminate\Database\Eloquent\Collection $employees
 * @property \Illuminate\Database\Eloquent\Collection $issued_docket_headers
 * @property \Illuminate\Database\Eloquent\Collection $item_receipt_headers
 * @property \Illuminate\Database\Eloquent\Collection $material_request_headers
 * @property \Illuminate\Database\Eloquent\Collection $purchase_invoice_headers
 * @property \Illuminate\Database\Eloquent\Collection $purchase_order_headers
 * @property \Illuminate\Database\Eloquent\Collection $purchase_request_headers
 * @property \Illuminate\Database\Eloquent\Collection $warehouses
 *
 * @package App\Models
 */
class Site extends Eloquent
{
	protected $casts = [
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $fillable = [
		'code',
		'name',
		'location',
		'phone',
		'pic',
		'created_by',
		'updated_by'
	];

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'updated_by');
    }

	public function delivery_order_headers()
	{
		return $this->hasMany(\App\Models\DeliveryOrderHeader::class, 'to_site_id');
	}

	public function employees()
	{
		return $this->hasMany(\App\Models\Employee::class);
	}

	public function issued_docket_headers()
	{
		return $this->hasMany(\App\Models\IssuedDocketHeader::class);
	}

	public function item_receipt_headers()
	{
		return $this->hasMany(\App\Models\ItemReceiptHeader::class);
	}

	public function material_request_headers()
	{
		return $this->hasMany(\App\Models\MaterialRequestHeader::class);
	}

	public function purchase_invoice_headers()
	{
		return $this->hasMany(\App\Models\PurchaseInvoiceHeader::class);
	}

	public function purchase_order_headers()
	{
		return $this->hasMany(\App\Models\PurchaseOrderHeader::class);
	}

	public function purchase_request_headers()
	{
		return $this->hasMany(\App\Models\PurchaseRequestHeader::class);
	}

	public function warehouses()
	{
		return $this->hasMany(\App\Models\Warehouse::class);
	}
}
