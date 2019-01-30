<?php
/**
 * Created by PhpStorm.
 * User: hellb
 * Date: 1/2/2019
 * Time: 12:09 PM
 */

namespace App\Imports;


use App\Models\Item;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class InventoryImport implements ToCollection
{

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        set_time_limit(0);

        $totalRecord = 0;
        $matchFound = 0;
        foreach ($rows as $row){
            $totalRecord++;

            $item = Item::where('code', $row[0])->first();
            if(!empty($item)){
                $matchFound++;

                $value = str_replace(',','', $row[11]);
                $item->value = $value;
                $item->name = $row[1];
                $item->save();
            }
        }

        error_log('Total Record: '. $totalRecord);
        error_log('Match Found: '. $matchFound);
    }
}