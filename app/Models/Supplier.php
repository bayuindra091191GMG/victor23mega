<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 18 Jun 2018 12:53:59 +0700.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Supplier
 * 
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $type
 * @property string $category
 * @property string $email1
 * @property string $email2
 * @property string $phone1
 * @property string $phone2
 * @property string $fax
 * @property string $cellphone
 * @property string $contact_person
 * @property string $address
 * @property string $city
 * @property string $remark
 * @property string $npwp
 * @property string $bank_name
 * @property string $bank_account_number
 * @property string $bank_account_name
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property int $updated_by
 * @property \Carbon\Carbon $updated_at
 * @property int $status_id
 * 
 * @property \App\Models\Auth\User\User $user
 * @property \Illuminate\Database\Eloquent\Collection $payment_requests
 * @property \Illuminate\Database\Eloquent\Collection $purchase_order_headers
 * @property \Illuminate\Database\Eloquent\Collection $quotation_headers
 *
 * @package App\Models
 */
class Supplier extends Eloquent
{
	protected $casts = [
		'created_by' => 'int',
		'updated_by' => 'int',
		'status_id' => 'int'
	];

	protected $fillable = [
		'code',
		'name',
        'type',
        'category',
		'email1',
		'email2',
		'phone1',
		'phone2',
		'fax',
		'cellphone',
		'contact_person',
		'address',
		'city',
		'remark',
		'npwp',
		'bank_name',
		'bank_account_number',
		'bank_account_name',
		'created_by',
		'updated_by',
        'status_id'
	];

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\Auth\User\User::class, 'updated_by');
    }

	public function payment_requests()
	{
		return $this->hasMany(\App\Models\PaymentRequest::class);
	}

	public function purchase_order_headers()
	{
		return $this->hasMany(\App\Models\PurchaseOrderHeader::class);
	}

	public function quotation_headers()
	{
		return $this->hasMany(\App\Models\QuotationHeader::class);
	}

    public function status()
    {
        return $this->belongsTo(\App\Models\Status::class);
    }
}
