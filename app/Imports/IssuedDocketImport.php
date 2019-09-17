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
    public $data;

    /**
     * @param Collection $rows
     * @return Collection
     */
    public function collection(Collection $rows)
    {
        $docketDetails = collect();
        foreach ($rows as $row){
            $itemCode = strtolower(trim($row[0]));
            $item = Item::whereRaw("LOWER(`code`) = ?", [$itemCode])->first();
            $itemId = -1;
            $itemText = '';
            $itemUom = '';
            if(!empty($item)){
                $itemId = $item->id;
                $itemText = $item->code;
                $itemUom = $item->uom;
            }

            $accountCode = trim($row[1]);
            $account = Account::where('code', $accountCode)->first();
            $accountId = -1;
            $accountText = '';
            if(!empty($account)){
                $accountId = $account->id;
                $accountText = $account->code. ' - '. $account->description. ' - '. $account->location;
            }

            $machinery = Machinery::whereRaw("LOWER(`code`) = ?", [strtolower($row[2])])->first();
            $machineryId = -1;
            $machineryText = '';
            if(!empty($machinery)){
                $machineryId = $machinery->id;
                $machineryText = $machinery->code;
            }

            $qtyFloat = Utilities::toFloat($row[3]);
            //$time = strval($row[5]);

            $docketDetail = collect([
                'item_id'           => $itemId,
                'item_text'         => $itemText,
                'account_id'        => $accountId,
                'account_text'      => $accountText,
                'machinery_id'      => $machineryId,
                'machinery_text'    => $machineryText,
                'qty'               => $qtyFloat,
                'uom'               => $itemUom,
                'shift'             => strtoupper(trim($row[4])),
                'time'              => trim($row[5]),
                'hm'                => trim($row[6]),
                'km'                => trim($row[7]),
                'fuelman'           => trim($row[8]),
                'operator'          => trim($row[9]),
                'remark'            => trim($row[10])
            ]);

            $docketDetails->push($docketDetail);
        }

        $this->data = $docketDetails;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 5;
    }
}