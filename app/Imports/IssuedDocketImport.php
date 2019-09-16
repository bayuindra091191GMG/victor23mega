<?php


namespace App\Imports;


use App\Libs\Utilities;
use App\Models\Account;
use App\Models\Item;
use App\Models\Machinery;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class IssuedDocketImport implements ToCollection, WithStartRow
{

    /**
     * @param Collection $rows
     * @return Collection
     */
    public function collection(Collection $rows)
    {
        $docketDetails = collect();
        foreach ($rows as $row){
            $item = Item::whereRaw('LOWER(code) = '. strtolower($row[0]))->first();
            $itemId = -1;
            $itemText = '';
            if(!empty($item)){
                $itemId = $item->id;
                $itemText = $item->code;
            }

            $account = Account::whereRaw('LOWER(code) = '. strtolower($row[1]))->first();
            $accountId = -1;
            $accountText = '';
            if(!empty($account)){
                $accountId = $account->id;
                $accountText = $account->code. ' - '. $account->description. ' - '. $account->location;
            }

            $machinery = Machinery::whereRaw('LOWER(code) = '. strtolower($row[2]))->first();
            $machineryId = -1;
            $machineryText = '';
            if(!empty($machinery)){
                $machineryId = $machinery->id;
                $machineryText = $machinery->code;
            }

            $qtyFloat = Utilities::toFloat($row[3]);

            $docketDetail = collect([
                'item_id'           => $itemId,
                'item_code'         => $itemText,
                'account_id'        => $accountId,
                'account_text'      => $accountText,
                'machinery_id'      => $machineryId,
                'machinery_text'    => $machineryText,
                'qty'               => $qtyFloat,
                'shift'             => strtoupper($row[4]),
                'time'              => $row[5],
                'hm'                => $row[6],
                'km'                => $row[7],
                'fuelman'           => $row[8],
                'operator'          => $row[9],
                'remark'            => $row[10]
            ]);

            $docketDetails->push($docketDetail);
        }

        return $docketDetails;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 5;
    }
}