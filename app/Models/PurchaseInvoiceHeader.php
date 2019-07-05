<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 14 Mar 2018 13:46:27 +0700.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PurchaseInvoiceHeader
 *
 * @property int $id
 * @property string $code
 * @property int $purchase_order_id
 * @property float $delivery_fee
 * @property float $total_discount
 * @property float $extra_discount
 * @property float $total_price
 * @property float $total_payment_before_tax
 * @property int $pph_percent
 * @property int $ppn_percent
 * @property float $pph_amount
 * @property float $ppn_amount
 * @property float $total_payment
 * @property int $is_retur
 * @property int $status_id
 * @property \Carbon\Carbon $date
 * @property float $repayment_amount
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property int $updated_by
 * @property \Carbon\Carbon $updated_at
 * @property int $payment_term
 * @property int $site_id
 * @property int $mr_type
 *
 * @property \App\Models\PurchaseOrderHeader $purchase_order_header
 * @property \App\Models\Status $status
 * @property \App\Models\Auth\User\User $user
 * @property \App\Models\Site $site
 * @property \Illuminate\Database\Eloquent\Collection $purchase_invoice_details
 * @property \Illuminate\Database\Eloquent\Collection $payment_requests_pi_details
 *
 * @package App\Models
 */
class PurchaseInvoiceHeader extends Eloquent
{
	protected $casts = [
		'purchase_order_id' => 'int',
		'delivery_fee' => 'float',
		'total_discount' => 'float',
        'extra_discount' => 'float',
		'total_price' => 'float',
		'total_payment_before_tax' => 'float',
		'pph_percent' => 'int',
		'ppn_percent' => 'int',
		'pph_amount' => 'float',
		'ppn_amount' => 'float',
		'total_payment' => 'float',
        'is_retur' => 'int',
		'status_id' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int',
		'payment_term' => 'int',
		'site_id' => 'int',
        'mr_type' => 'int'
	];

    protected $appends = [
        'total_price_string',
        'total_discount_string',
        'all_discount_string',
        'ppn_string',
        'pph_string',
        'total_payment_string',
        'repayment_amount_string',
        'delivery_fee_string',
        'date_string',
        'show_url',
        'show_url_po',
        'po_supplier_name',
        'mr_type_string'
    ];

	protected $fillable = [
		'code',
		'purchase_order_id',
		'delivery_fee',
		'total_discount',
        'extra_discount',
		'total_price',
		'total_payment_before_tax',
		'pph_percent',
		'ppn_percent',
		'pph_amount',
		'ppn_amount',
		'total_payment',
        'date',
        'repayment_amount',
        'is_retur',
		'status_id',
		'created_by',
		'updated_by',
        'payment_term',
        'site_id',
        'mr_type'
	];

    public function getDateStringAttribute(){
        return Carbon::parse($this->attributes['created_at'])->format('d M Y');
    }

    public function getRepaymentAmountStringAttribute(){
        return number_format($this->attributes['repayment_amount'], 2, ",", ".");
    }

    public function getTotalPriceStringAttribute(){
        return number_format($this->attributes['total_price'], 2, ",", ".");
    }

    public function getTotalDiscountStringAttribute(){
        return number_format($this->attributes['total_discount'], 2, ",", ".");
    }

    public function getAllDiscountStringAttribute(){
        $individualDiscount = $this->attributes['total_discount'] ?? 0;
        $extraDiscount = $this->attributes['extra_discount'] ?? 0;
        $allDiscount = $individualDiscount + $extraDiscount;
        if($allDiscount > 0){
            return number_format($allDiscount, 2, ",", ".");
        }
        else{
            return '-';
        }
    }

    public function getPpnStringAttribute(){
        return number_format($this->attributes['ppn_amount'], 2, ",", ".");
    }

    public function getPphStringAttribute(){
        return number_format($this->attributes['pph_amount'], 2, ",", ".");
    }

    public function getTotalPaymentStringAttribute(){
        return number_format($this->attributes['total_payment'], 2, ",", ".");
    }

    public function getDeliveryFeeStringAttribute(){
        return number_format($this->attributes['delivery_fee'], 2, ",", ".");
    }

    public function getShowUrlAttribute(){
        return "<a style='text-decoration: underline;' href='". route('admin.purchase_invoices.show', ['purchase_invoice' => $this->attributes['id']]). "'>". $this->attributes['code']. "</a>";
    }

    public function getShowUrlPoAttribute(){
        $poCode = $this->purchase_order_header->code;
        return "<a style='text-decoration: underline;' href='". route('admin.purchase_orders.show', ['purchase_order' => $this->attributes['purchase_order_id']]). "'>". $poCode. "</a>";
    }

    public function getPoSupplierNameAttribute(){
        return $this->purchase_order_header->supplier->name;
    }

    public function getMrTypeStringAttribute(){
        if($this->attributes['mr_type'] === 1){
            return 'PART/NON-PART';
        }
        elseif($this->attributes['mr_type'] === 2){
            return 'BBM';
        }
        elseif($this->attributes['mr_type'] === 3){
            return 'OLI';
        }
        else{
            return 'SERVIS';
        }
    }

    public function scopeDateDescending(Builder $query){
        return $query->orderBy('date','DESC');
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

	public function purchase_invoice_details()
	{
		return $this->hasMany(\App\Models\PurchaseInvoiceDetail::class, 'header_id');
	}

    public function purchase_invoice_repayments()
    {
        return $this->hasMany(\App\Models\PurchaseInvoiceRepayment::class, 'purchase_invoice_header_id');
    }

    public function retur_headers()
    {
        return $this->hasMany(\App\Models\ReturHeader::class, 'purchase_invoice_id');
    }

    public function site()
    {
        return $this->belongsTo(\App\Models\Site::class);
    }

    public function payment_requests_pi_details()
    {
        return $this->hasMany(\App\Models\PaymentRequestsPiDetail::class, 'purchase_invoice_header_id');
    }
}
