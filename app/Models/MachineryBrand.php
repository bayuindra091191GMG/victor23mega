<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 01 Feb 2018 02:38:52 +0000.
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
        'is_synced'
	];

	public function machineries()
	{
		return $this->hasMany(\App\Models\Machinery::class, 'brand_id');
	}
}
