<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 19 Sep 2018 10:36:34 +0700.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class IssuedDocketDetail
 * 
 * @property int $id
 * @property int $header_id
 * @property int $item_id
 * @property int $machinery_id
 * @property int $quantity
 * @property int $quantity_retur
 * @property string $remarks
 * @property string $remark_retur
 * @property string $time
 * @property string $hm
 * @property string $km
 * @property string $fuelman
 * @property string $operator
 * @property string $shift
 * 
 * @property \App\Models\IssuedDocketHeader $issued_docket_header
 * @property \App\Models\Item $item
 * @property \App\Models\Machinery $machinery
 *
 * @package App\Models
 */
class IssuedDocketDetail extends Eloquent
{
	public $timestamps = false;

	protected $casts = [
		'header_id' => 'int',
		'item_id' => 'int',
		'machinery_id' => 'int',
		'quantity' => 'int',
		'quantity_retur' => 'int'
	];

	protected $fillable = [
		'header_id',
		'item_id',
		'machinery_id',
		'quantity',
		'quantity_retur',
		'remarks',
		'remark_retur',
		'time',
		'hm',
		'km',
		'fuelman',
		'operator',
		'shift'
	];

    protected $appends = [
        'item_value_str',
        'subtotal_value_str'
    ];

    public function getItemValueStrAttribute(){
        if(!empty($this->item->value) && $this->item->value != 0){
            $value = $this->item->value;
            return number_format($value, 2, ",", ".");
        }
        else{
            return '0';
        }
    }

    public function getSubtotalValueStrAttribute(){
        if(!empty($this->item->value) && $this->item->value != 0){
            $qtyResult = $this->attributes['quantity'] - $this->attributes['quantity_retur'];
            if($qtyResult == 0) return '0';
            $value = $qtyResult * $this->item->value;
            return number_format($value, 2, ",", ".");
        }
        else{
            return '0';
        }
    }

	public function issued_docket_header()
	{
		return $this->belongsTo(\App\Models\IssuedDocketHeader::class, 'header_id');
	}

	public function item()
	{
		return $this->belongsTo(\App\Models\Item::class);
	}

	public function machinery()
	{
		return $this->belongsTo(\App\Models\Machinery::class);
	}
}
