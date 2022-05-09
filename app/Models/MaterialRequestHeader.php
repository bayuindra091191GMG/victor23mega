<?php

/**
 * Created by Reliese Model.
 * Date: Fri, 23 Mar 2018 15:42:43 +0700.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class MaterialRequestHeader
 * 
 * @property int $id
 * @property int $site_id
 * @property string $code
 * @property int $type
 * @property string $purpose
 * @property int $department_id
 * @property int $machinery_id
 * @property string $priority
 * @property string $km
 * @property string $hm
 * @property int $status_id
 * @property \Carbon\Carbon $date
 * @property string $close_reason
 * @property int $closed_by
 * @property \Carbon\Carbon $closed_at
 * @property int $is_issued
 * @property int $is_retur
 * @property int $is_approved
 * @property \Carbon\Carbon $approved_date
 * @property string $requested_by
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property int $updated_by
 * @property \Carbon\Carbon $updated_at
 * @property string $reject_reason
 * @property \Carbon\Carbon $rejected_date
 * @property string $pdf_path
 * @property int $is_pr_created
 * @property string $feedback
 * @property string $warehouse_id
 * @property int $processed_by
 * @property int $assigned_to
 * @property bool $is_synced
 * @property string $created_on
 *
 * @property \App\Models\Site $site
 * @property \App\Models\Department $department
 * @property \App\Models\Warehouse $warehouse
 * @property \App\Models\Machinery $machinery
 * @property \App\Models\Status $status
 * @property \Illuminate\Database\Eloquent\Collection $issued_docket_headers
 * @property \Illuminate\Database\Eloquent\Collection $material_request_details
 * @property \Illuminate\Database\Eloquent\Collection $purchase_request_headers
 * @property \Illuminate\Database\Eloquent\Collection $assignment_material_requests
 * @property \App\Models\Auth\User\User $processedBy
 * @property \App\Models\Auth\User\User $assignedTo
 *
 * @package App\Models
 */
class MaterialRequestHeader extends Eloquent
{
	protected $casts = [
		'type' => 'int',
        'site_id' => 'int',
		'department_id' => 'int',
		'machinery_id' => 'int',
        'warehouse_id' => 'int',
		'status_id' => 'int',
		'closed_by' => 'int',
        'is_issued' => 'int',
        'is_retur' => 'int',
        'is_approved' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int',
        'is_pr_created' => 'int',
        'is_reorder' => 'int',
        'processed_by' => 'int',
        'assigned_to' => 'int',
        'is_synced' => 'bool'
	];

	protected $dates = [
		'date',
		'closed_at',
        'approved_date',
        'rejected_date'
	];

    protected $appends = [
        'date_string',
        'created_at_string'
    ];

	protected $fillable = [
		'code',
        'site_id',
		'type',
        'purpose',
		'department_id',
		'machinery_id',
		'priority',
		'km',
		'hm',
		'status_id',
		'date',
		'close_reason',
		'closed_by',
        'is_issued',
        'is_retur',
        'is_approved',
        'approved_date',
		'closed_at',
        'requested_by',
		'created_by',
		'updated_by',
        'reject_reason',
        'rejected_date',
        'pdf_path',
        'is_pr_created',
        'feedback',
        'warehouse_id',
        'is_reorder',
        'processed_by',
        'assigned_to',
        'is_synced',
        'created_on'
	];

    public function scopeDateDescending(Builder $query){
        return $query->orderBy('date','DESC');
    }

    public function getDateStringAttribute(){
        return Carbon::parse($this->attributes['date'])->format('d M Y');
    }

    public function getCreatedAtStringAttribute(){
        return Carbon::parse($this->attributes['created_at'])->format('d M Y');
    }

    public function site()
    {
        return $this->belongsTo(\App\Models\Site::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(\App\Models\Warehouse::class);
    }

	public function department()
	{
		return $this->belongsTo(\App\Models\Department::class);
	}

	public function machinery()
	{
		return $this->belongsTo(\App\Models\Machinery::class);
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

	public function issued_docket_headers()
	{
		return $this->hasMany(\App\Models\IssuedDocketHeader::class);
	}

	public function material_request_details()
	{
		return $this->hasMany(\App\Models\MaterialRequestDetail::class, 'header_id');
	}

	public function purchase_request_headers()
	{
		return $this->hasMany(\App\Models\PurchaseRequestHeader::class, 'material_request_id');
	}

    public function assignment_material_requests()
    {
        return $this->hasMany(\App\Models\AssignmentMaterialRequest::class, 'material_request_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'processed_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'assigned_to');
    }
}
