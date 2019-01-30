<?php
/**
 * Created by PhpStorm.
 * User: hellb
 * Date: 10/2/2018
 * Time: 9:15 AM
 */

namespace App\Transformer;


use App\Models\Account;
use App\Models\ItemStock;
use Illuminate\Support\Carbon;
use League\Fractal\TransformerAbstract;

class ReorderTransformer extends TransformerAbstract
{
    public function transform(ItemStock $itemStock){
        $location = $itemStock->location ?? '-';
        $stockOnOrder = $itemStock->stock_on_order ?? 0;
        $action = "<a class='add-table btn btn-xs btn-success' 
                    data-id='". $itemStock->id ."' 
                    data-itemCode='". $itemStock->item->code ."' 
                    data-partNumber='". $itemStock->item->part_number ."' 
                    data-itemName='". $itemStock->item->name ."' 
                    data-warehouseName='". $itemStock->warehouse->name."'
                    data-location='". $location ."'
                    data-stock='". $itemStock->stock ."'
                    data-stockMin='". $itemStock->stock_min ."'
                    data-stockOnOrder='". $stockOnOrder ."'><i class='fa fa-plus'></i></a>";

        return[
            'item_id'            => $itemStock->item->code,
            'part_number'  => $itemStock->item->part_number,
            'name'         => $itemStock->item->name,
            'warehouse_id'  => $itemStock->warehouse->name,
            'location'      => $itemStock->location ?? '-',
            'stock'         => $itemStock->stock,
            'stock_min'     => $itemStock->stock_min,
            'stock_on_order'=> $stockOnOrder,
            'action'        => $action
        ];
    }
}