<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 23/01/2018
 * Time: 10:45
 */

namespace App\Transformer\Inventory;

use App\Models\StockCard;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class StockCardTransformer extends TransformerAbstract
{
    public function transform(StockCard $stockCard){
        try{
            $itemShowUrl = route('admin.items.show', ['item' => $stockCard->item_id]);

            $itemUrl = "<a data-sort='". $stockCard->item->code."' style='text-decoration: underline;' href='" . $itemShowUrl. "' target='_blank'>". $stockCard->item->code. " (". $stockCard->item->name. ")</a>";

            return[
                'reference'     => $stockCard->reference ?? '-',
                'item'          => $itemUrl ?? '-',
                'warehouse'     => $stockCard->warehouse->name ?? '-',
                'in_qty'        => $stockCard->in_qty ?? '0',
                'out_qty'       => $stockCard->out_qty ?? '0',
                'result_qty'    => $stockCard->result_qty,
                'created_at'    => Carbon::parse($stockCard->created_at)->toIso8601String(),
                'created_by'    => $stockCard->createdBy->name ?? '-'
            ];
        }
        catch(\Exception $ex){
            error_log($stockCard->id);
        }
    }
}