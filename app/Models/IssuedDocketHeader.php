<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 15 Feb 2018 04:00:49 +0000.
 */

namespace App\Models;

use Carbon\Carbon;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class IssuedDocketHeader
 * 
 * @property int $id
 * @property int $type
 * @property string $code
 * @property int $site_id
 * @property \Carbon\Carbon $date
 * @property int $unit_id
 * @property int $purchase_request_id
 * @property int $department_id
 * @property string $division
 * @property int $is_retur
 * @property int $status_id
 * @property string $receiver_name
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property int $updated_by
 * @property \Carbon\Carbon $updated_at
 * @property int $account_id
 * @property bool $is_synced
 * @property string $created_on
 * @property string $km
 * @property string $hm
 *
 * @property \App\Models\Department $department
 * @property \App\Models\Machinery $machinery
 * @property \App\Models\MaterialRequestHeader $material_request_header_id
 * @property \App\Models\Warehouse $warehouse_id
 * @property \App\Models\Status $status
 * @property \App\Models\Auth\User\ $createdBy
 * @property \App\Models\Auth\User\ $updatedBy
 * @property \Illuminate\Database\Eloquent\Collection $issued_docket_details
 *
 * @package App\Models
 */
class IssuedDocketHeader extends Eloquent
{
    protected $appends = [
        'date_string',
        'total_value',
        'total_value_str'];

	protected $casts = [
        'type' => 'int',
        'site_id' => 'int',
		'unit_id' => 'int',
		'material_request_header_id' => 'int',
		'department_id' => 'int',
        'warehouse_id' => 'int',
        'is_retur' => 'int',
		'status_id' => 'int',
		'created_by' => 'int',
		'updated_by' => 'int',
        'is_synced' => 'bool'
	];

	protected $dates = [
		'date',
        'total_value_str'
	];

	protected $fillable = [
		'code',
        'site_id',
        'account_id',
        'type',
		'date',
        'km',
        'hm',
		'unit_id',
		'material_request_header_id',
        'warehouse_id',
		'department_id',
		'division',
        'is_retur',
        'receiver_name',
		'status_id',
		'created_by',
		'updated_by',
        'is_synced',
        'created_on'
	];

	public function getDateStringAttribute(){
	    return Carbon::parse($this->attributes['date'])->format('d M Y');
    }

    public function getTotalValueAttribute(){
        $totalValue = 0;
        foreach($this->issued_docket_details as $detail){
            $value = $detail->quantity * $detail->item->value;
            $totalValue += $value;
        }
        return $totalValue;
    }

    public function getTotalValueStrAttribute(){
	    $totalValue = 0;
        foreach($this->issued_docket_details as $detail){
            $value = $detail->quantity * $detail->item->value;
            $totalValue += $value;
        }
        return number_format($totalValue, 2, ",", ".");
    }

	public function department()
	{
		return $this->belongsTo(\App\Models\Department::class);
	}

    public function site()
    {
        return $this->belongsTo(\App\Models\Site::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(\App\Models\Warehouse::class, 'warehouse_id');
    }

	public function machinery()
	{
		return $this->belongsTo(\App\Models\Machinery::class, 'unit_id');
	}

	public function material_request_header()
	{
		return $this->belongsTo(\App\Models\MaterialRequestHeader::class, 'material_request_header_id');
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

	public function issued_docket_details()
	{
		return $this->hasMany(\App\Models\IssuedDocketDetail::class, 'header_id');
	}

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class, 'account_id');
    }
}
