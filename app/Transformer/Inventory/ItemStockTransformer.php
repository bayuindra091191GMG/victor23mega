<?php
/**
 * Created by PhpStorm.
 * User: hellb
 * Date: 7/16/2018
 * Time: 11:26 AM
 */

namespace App\Transformer\Inventory;

use App\Models\ItemStock;
use League\Fractal\TransformerAbstract;

class ItemStockTransformer extends TransformerAbstract
{
    public function transform(ItemStock $itemStock){

        $itemShowUrl = route('admin.items.show', ['item' => $itemStock->item_id]);

        $code = "<a name='". $itemStock->item->code. "' href='". $itemShowUrl. "' style='text-decoration: underline;' target='_blank'>". $itemStock->item->code. "</a>";

        $action = "<a class='btn btn-xs btn-primary' href='". $itemShowUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-eye'></i></a>";

        return[
            'code'                  => $code,
            'name'                  => $itemStock->item->name,
            'part_number'           => $itemStock->item->part_number ?? '-',
            'warehouse'             => $itemStock->warehouse->name,
            'location'              => $itemStock->location,
            'uom'                   => $itemStock->item->uom,
            'stock'                 => $itemStock->stock ?? '0',
            'stock_min'             => $itemStock->stock_min,
            'stock_max'             => $itemStock->stock_max,
            'stock_on_order'        => $itemStock->stock_on_order,
            'qty_issued_12_months'  => $itemStock->qty_issued_12_months ?? '0',
            'is_stock_warning'      => $itemStock->is_stock_warning ? 'AKTIF' : 'NON-AKTIF',
            'group'                 => $itemStock->item->group->name ?? '-',
            'machinery_type'        => $itemStock->item->machinery_type ?? '-',
            'movement_status'       => $itemStock->movement_status,
            'action'                => $action
        ];
    }
}