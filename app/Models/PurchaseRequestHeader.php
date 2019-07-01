<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 23 Mar 2018 15:54:48 +0700.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PurchaseRequestHeader
 * 
 * @property int $id
 * @property string $code
 * @property int $site_id
 * @property int $material_request_id
 * @property int $department_id
 * @property int $machinery_id
 * @property string $priority
 * @property \Carbon\Carbon $priority_limit_date
 * @property string $km
 * @property string $hm
 * @property int $status_id
 * @property \Carbon\Carbon $date
 * @property int $is_approved
 * @property \Carbon\Carbon $approved_date
 * @property int $is_all_poed
 * @property int $is_retur
 * @property string $close_reason
 * @property int $closed_by
 * @property \Carbon\Carbon $closed_at
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property int $updated_by
 * @property \Carbon\Carbon $updated_at
 * @property int $warehouse_id
 * 
 * @property \App\Models\Department $department
 * @property \App\Models\Department $warehouse
 * @property \App\Models\Machinery $machinery
 * @property \App\Models\MaterialRequestHeader $material_request_header
 * @property \App\Models\Status $status
 * @property \Illuminate\Database\Eloquent\Collection $approval_purchase_requests
 * @property \Illuminate\Database\Eloquent\Collection $delivery_order_headers
 * @property \Illuminate\Database\Eloquent\Collection $purchase_order_headers
 * @property \Illuminate\Database\Eloquent\Collection $purchase_request_details
 * @property \Illuminate\Database\Eloquent\Collection $quotation_headers
 * @property \Illuminate\Database\Eloquent\Collection $assignment_purchase_requests
 * @property \App\Models\Auth\User\User $processedBy
 *
 * @package App\Models
 */
class PurchaseRequestHeader extends Eloquent
{
	protected $casts = [
        'site_id' => 'int',
		'material_request_id' => 'int',
		'department_id' => 'int',
		'machinery_id' => 'int',
        'is_approved' => 'int',
		'status_id' => 'int',
        'is_all_poed' => 'int',
        'is_retur' => 'int',
		'closed_by' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int',
        'warehouse_id' => 'int',
        'is_reorder' => 'int'
	];

    protected $appends = [
        'date_string',
        'priority_expired',
        'day_left'];

	protected $dates = [
		'date',
        'approved_date',
		'closed_at',
        'priority_limit_date'
	];

	protected $fillable = [
		'code',
        'site_id',
		'material_request_id',
		'department_id',
		'machinery_id',
		'priority',
        'priority_limit_date',
		'km',
		'hm',
		'status_id',
		'date',
        'is_approved',
        'approved_date',
        'is_all_poed',
        'is_retur',
		'close_reason',
		'closed_by',
		'closed_at',
		'created_by',
        'created_at',
		'updated_by',
		'updated_at',
        'warehouse_id',
        'is_reorder',
        'processed_by'
	];

    public function scopeDateDescending(Builder $query){
        return $query->orderBy('date','DESC');
    }

    public function getDateStringAttribute(){
        return Carbon::parse($this->attributes['created_at'])->format('d M Y');
    }

    public function getDayLeftAttribute(){
        $limitDate = Carbon::parse($this->attributes['priority_limit_date']);
        return $limitDate->diffInDays();
    }

    public function getPriorityExpiredAttribute(){
        $now = Carbon::now('Asia/Jakarta')->startOfDay();
        $limitDate = Carbon::parse($this->attributes['priority_limit_date']);
        return $now->gt($limitDate->startOfDay());
    }

    public function site()
    {
        return $this->belongsTo(\App\Models\Site::class);
    }

	public function department()
	{
		return $this->belongsTo(\App\Models\Department::class);
	}

    public function warehouse()
    {
        return $this->belongsTo(\App\Models\Warehouse::class);
    }

	public function machinery()
	{
		return $this->belongsTo(\App\Models\Machinery::class);
	}

	public function material_request_header()
	{
		return $this->belongsTo(\App\Models\MaterialRequestHeader::class, 'material_request_id');
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

    public function closedBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'closed_by');
    }

	public function approval_purchase_requests()
	{
		return $this->hasMany(\App\Models\ApprovalPurchaseRequest::class, 'purchase_request_id');
	}

	public function delivery_order_headers()
	{
		return $this->hasMany(\App\Models\DeliveryOrderHeader::class, 'purchase_request_id');
	}

	public function purchase_order_headers()
	{
		return $this->hasMany(\App\Models\PurchaseOrderHeader::class, 'purchase_request_id');
	}

	public function purchase_request_details()
	{
		return $this->hasMany(\App\Models\PurchaseRequestDetail::class, 'header_id');
	}

	public function quotation_headers()
	{
		return $this->hasMany(\App\Models\QuotationHeader::class, 'purchase_request_id');
	}

    public function assignment_purchase_requests()
    {
        return $this->hasMany(\App\Models\AssignmentPurchaseRequest::class, 'purchase_request_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'processed_by');
    }
}
