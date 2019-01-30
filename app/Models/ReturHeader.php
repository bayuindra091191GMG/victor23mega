<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 17 May 2018 11:39:59 +0700.
 */

namespace App\Models;

use Carbon\Carbon;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ReturHeader
 * 
 * @property int $id
 * @property string $code
 * @property int $purchase_invoice_id
 * @property float $delivery_fee
 * @property float $total_discount
 * @property float $extra_discount
 * @property float $total_price
 * @property float $total_payment_before_tax
 * @property int $ppn_percent
 * @property float $ppn_amount
 * @property float $total_payment
 * @property \Carbon\Carbon $date
 * @property int $status_id
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property int $updated_by
 * @property \Carbon\Carbon $updated_at
 * 
 * @property \App\Models\PurchaseInvoiceHeader $purchase_invoice_header
 * @property \App\Models\Status $status
 * @property \App\Models\Auth\User\User $user
 * @property \Illuminate\Database\Eloquent\Collection $retur_details
 *
 * @package App\Models
 */
class ReturHeader extends Eloquent
{
	protected $casts = [
		'purchase_invoice_id' => 'int',
		'delivery_fee' => 'float',
		'total_discount' => 'float',
        'extra_discount' => 'float',
		'total_price' => 'float',
		'total_payment_before_tax' => 'float',
		'ppn_percent' => 'int',
		'ppn_amount' => 'float',
		'total_payment' => 'float',
		'status_id' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int'
	];

	protected $dates = [
		'date'
	];

	protected $fillable = [
		'code',
		'purchase_invoice_id',
		'delivery_fee',
		'total_discount',
        'extra_discount',
		'total_price',
		'total_payment_before_tax',
		'ppn_percent',
		'ppn_amount',
		'total_payment',
		'date',
		'status_id',
		'created_by',
		'updated_by'
	];

    protected $appends = [
        'total_price_string',
        'total_discount_string',
        'extra_discount_string',
        'all_discount_string',
        'ppn_string',
        'total_payment_string',
        'delivery_fee_string',
        'date_string',
    ];

    public function getDateStringAttribute(){
        return Carbon::parse($this->attributes['date'])->format('d M Y');
    }

    public function getDeliveryFeeStringAttribute(){
        return number_format($this->attributes['delivery_fee'], 0, ",", ".");
    }

    public function getTotalPriceStringAttribute(){
        return number_format($this->attributes['total_price'], 0, ",", ".");
    }

    public function getTotalDiscountStringAttribute(){
        return number_format($this->attributes['total_discount'], 0, ",", ".");
    }

    public function getExtraDiscountStringAttribute(){
        return number_format($this->attributes['extra_discount'], 0, ",", ".");
    }

    public function getAllDiscountStringAttribute(){
        $individualDiscount = $this->attributes['total_discount'] ?? 0;
        $extraDiscount = $this->attributes['extra_discount'] ?? 0;
        $allDiscount = $individualDiscount + $extraDiscount;
        if($allDiscount > 0){
            return number_format($allDiscount, 0, ",", ".");
        }
        else{
            return '-';
        }
    }

    public function getPpnStringAttribute(){
        return number_format($this->attributes['ppn_amount'], 0, ",", ".");
    }

    public function getTotalPaymentStringAttribute(){
        return number_format($this->attributes['total_payment'], 0, ",", ".");
    }

	public function purchase_invoice_header()
	{
		return $this->belongsTo(\App\Models\PurchaseInvoiceHeader::class, 'purchase_invoice_id');
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

	public function retur_details()
	{
		return $this->hasMany(\App\Models\ReturDetail::class, 'header_id');
	}
}
