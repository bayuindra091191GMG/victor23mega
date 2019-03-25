<?php

/**
 * Created by Reliese Model.
 * Date: Mon, 25 Mar 2019 09:52:20 +0700.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class AutoNumber
 * 
 * @property string $id
 * @property int $next_no
 *
 * @package App\Models
 */
class AutoNumber extends Eloquent
{
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'next_no' => 'int'
	];

	protected $fillable = [
	    'id',
		'next_no'
	];
}
