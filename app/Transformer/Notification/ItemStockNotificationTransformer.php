<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 02/05/2018
 * Time: 11:29
 */

namespace App\Transformer\Notification;


use App\Models\ItemStockNotification;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ItemStockNotificationTransformer extends TransformerAbstract
{
    public function transform(ItemStockNotification $itemStockNotif){

        $createdDate = Carbon::parse($itemStockNotif->created_at)->format('d M Y');

        $itemShowUrl = route('admin.items.show', ['item' => $itemStockNotif->item_id]);

        $code = "<a style='text-decoration: underline;' href='". $itemShowUrl. "'>". $itemStockNotif->item->code. "</a>";

        return[
            'code'                  => $code,
            'name'                  => $itemStockNotif->item->name,
            'uom'                   => $itemStockNotif->item->uom,
            'stock'                 => $itemStockNotif->item_stock->stock ?? '0',
            'stock_min'             => $itemStockNotif->item_stock->stock_min ?? '0',
            'stock_max'             => $itemStockNotif->item_stock->stock_max ?? '0',
            'warehouse'             => $itemStockNotif->item_stock->warehouse->name
        ];
    }
}