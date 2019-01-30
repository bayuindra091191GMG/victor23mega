<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 23 Mar 2018 16:56:21 +0700.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PurchaseOrderHeader
 * 
 * @property int $id
 * @property string $code
 * @property int $site_id
 * @property int $purchase_request_id
 * @property int $quotation_id
 * @property int $supplier_id
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
 * @property int $is_all_received
 * @property int $is_all_invoiced
 * @property int $is_retur
 * @property int $status_id
 * @property int $is_approved
 * @property \Carbon\Carbon $approved_date
 * @property \Carbon\Carbon $date
 * @property int $closed_by
 * @property string $close_reason
 * @property \Carbon\Carbon $closing_date
 * @property int $payment_term
 * @property string $special_note
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property int $updated_by
 * @property \Carbon\Carbon $updated_at
 * @property string $quotation_pdf_path_1
 * @property string $quotation_pdf_path_2
 * @property string $quotation_pdf_path_3
 * @property int $warehouse_id
 * 
 * @property \App\Models\PurchaseRequestHeader $purchase_request_header
 * @property \App\Models\QuotationHeader $quotation_header
 * @property \App\Models\Status $status
 * @property \App\Models\Supplier $supplier
 * @property \App\Models\Auth\User\User $user
 * @property \Illuminate\Database\Eloquent\Collection $item_receipt_headers
 * @property \Illuminate\Database\Eloquent\Collection $payment_requests_po_details
 * @property \Illuminate\Database\Eloquent\Collection $purchase_invoice_headers
 * @property \Illuminate\Database\Eloquent\Collection $purchase_order_details
 * @property \App\Models\Department $warehouse
 *
 * @package App\Models
 */
class PurchaseOrderHeader extends Eloquent
{
	protected $casts = [
        'site_id' => 'int',
		'purchase_request_id' => 'int',
		'quotation_id' => 'int',
		'supplier_id' => 'int',
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
        'is_all_received' => 'int',
        'is_all_invoiced' => 'int',
        'is_retur' => 'int',
        'is_approved' => 'int',
        'payment_term' => 'int',
		'status_id' => 'int',
		'closed_by' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int',
        'warehouse_id' => 'int'
	];

    protected $appends = [
        'total_price_string',
        'total_discount_string',
        'extra_discount_string',
        'all_discount_string',
        'total_payment_before_tax_string',
        'ppn_string',
        'pph_string',
        'total_payment_string',
        'delivery_fee_string',
        'date_string',
        'closing_date_string',
        'show_url',
        'show_url_pr',
        'supplier_name'
    ];

	protected $dates = [
		'date',
		'closing_date'
	];

	protected $fillable = [
		'code',
        'site_id',
        'date',
		'purchase_request_id',
		'quotation_id',
		'supplier_id',
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
        'is_all_received',
        'is_all_invoiced',
        'is_retur',
        'is_approved',
        'approved_date',
		'closed_by',
		'close_reason',
		'closing_date',
        'payment_term',
        'special_note',
        'status_id',
		'created_by',
		'updated_by',
        'quotation_pdf_path_1',
        'quotation_pdf_path_2',
        'quotation_pdf_path_3',
        'warehouse_id'
	];

    public function getClosingDateStringAttribute(){
        return Carbon::parse($this->attributes['closing_date'])->format('d M Y');
    }

    public function getDateStringAttribute(){
        return Carbon::parse($this->attributes['date'])->format('d M Y');
    }

    public function getTotalPriceStringAttribute(){
        return number_format($this->attributes['total_price'], 2, ",", ".");
    }

    public function getTotalDiscountStringAttribute(){
        if(!empty($this->attributes['total_discount']) && $this->attributes['total_discount'] != 0){
            return number_format($this->attributes['total_discount'], 2, ",", ".");
        }
        else{
            return '-';
        }
    }

    public function getExtraDiscountStringAttribute(){
        return number_format($this->attributes['extra_discount'], 2, ",", ".");
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

    public function getTotalPaymentBeforeTaxStringAttribute(){
        return number_format($this->attributes['total_payment_before_tax'], 2, ",", ".");
    }

    public function getTotalPaymentStringAttribute(){
        return number_format($this->attributes['total_payment'], 2, ",", ".");
    }

    public function getDeliveryFeeStringAttribute(){
        return number_format($this->attributes['delivery_fee'], 2, ",", ".");
    }

    public function getShowUrlAttribute(){
        return "<a style='text-decoration: underline;' href='". route('admin.purchase_orders.show', ['purchase_order' => $this->attributes['id']]). "'>". $this->attributes['code']. "</a>";
    }

    public function getShowUrlPrAttribute(){
        $prCode = $this->purchase_request_header->code;
        return "<a style='text-decoration: underline;' href='". route('admin.purchase_requests.show', ['purchase_request' => $this->attributes['purchase_request_id']]). "'>". $prCode. "</a>";
    }

    public function getSupplierNameAttribute(){
        return $this->supplier->name;
    }

    public function scopeDateDescending(Builder $query){
        return $query->orderBy('date','DESC');
    }

	public function purchase_request_header()
	{
		return $this->belongsTo(\App\Models\PurchaseRequestHeader::class, 'purchase_request_id');
	}

	public function quotation_header()
	{
		return $this->belongsTo(\App\Models\QuotationHeader::class, 'quotation_id');
	}

	public function status()
	{
		return $this->belongsTo(\App\Models\Status::class);
	}

	public function supplier()
	{
		return $this->belongsTo(\App\Models\Supplier::class);
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

    public function closedBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'closed_by');
    }

    public function item_receipt_headers()
	{
		return $this->hasMany(\App\Models\ItemReceiptHeader::class, 'purchase_order_id');
	}

	public function payment_requests_po_details()
	{
		return $this->hasMany(\App\Models\PaymentRequestsPoDetail::class, 'purchase_order_id');
	}

	public function purchase_invoice_headers()
	{
		return $this->hasMany(\App\Models\PurchaseInvoiceHeader::class, 'purchase_order_id');
	}

	public function purchase_order_details()
	{
		return $this->hasMany(\App\Models\PurchaseOrderDetail::class, 'header_id');
	}
}
