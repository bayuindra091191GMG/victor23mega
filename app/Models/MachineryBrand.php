<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 11 May 2022 10:31:14 +0700.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class MachineryBrand
 * 
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $description
 * @property bool $is_synced
 * @property string $created_on
 * 
 * @property \Illuminate\Database\Eloquent\Collection $machineries
 *
 * @package App\Models
 */
class MachineryBrand extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'is_synced' => 'bool'
	];

	protected $fillable = [
		'code',
		'name',
		'description',
		'is_synced',
		'created_on'
	];

	public function machineries()
	{
		return $this->hasMany(\App\Models\Machinery::class, 'brand_id');
	}
}
