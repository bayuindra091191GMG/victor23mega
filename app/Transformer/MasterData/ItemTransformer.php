<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 25/01/2018
 * Time: 10:41
 */

namespace App\Transformer\MasterData;

use App\Models\Item;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ItemTransformer extends TransformerAbstract
{
    public function transform(Item $item){

//        $createdDate = Carbon::parse($item->created_at)->format('d M Y');
        $createdDate = Carbon::parse($item->created_at)->toIso8601String();

        $itemShowUrl = route('admin.items.show', ['item' => $item->id]);
        $itemEditUrl = route('admin.items.edit', ['item' => $item->id]);

        $code = "<a name='". $item->code. "' href='". $itemShowUrl. "' style='text-decoration: underline;' target='_blank'>". $item->code. "</a>";

        $action = "<a class='btn btn-xs btn-primary' href='". $itemShowUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-eye'></i></a>";
        $action .= "<a class='btn btn-xs btn-info' href='". $itemEditUrl. "' data-toggle='tooltip' data-placement='top'><i class='fa fa-pencil'></i></a>";
        $action .= "<a class='delete-modal btn btn-xs btn-danger' data-id='". $item->id ."' ><i class='fa fa-trash'></i></a>";

        return[
            'code'                  => $code,
            'name'                  => $item->name,
            'part_number'           => $item->part_number ?? '-',
            'uom'                   => $item->uom,
            'value'                 => $item->value_str,
            'stock'                 => $item->stock ?? '0',
            'stock_on_order'        => $item->stock_on_order ?? '0',
            'group'                 => $item->group->name,
            'machinery_type'        => $item->machinery_type,
            'description'           => $item->description ?? '-',
            'created_at'            => $createdDate,
            'action'                => $action
        ];
    }
}