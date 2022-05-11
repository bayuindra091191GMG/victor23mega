<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 11 May 2022 10:33:03 +0700.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class MachineryType
 * 
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $description
 * @property bool $is_synced
 * @property string $created_on
 *
 * @package App\Models
 */
class MachineryType extends Eloquent
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
}
