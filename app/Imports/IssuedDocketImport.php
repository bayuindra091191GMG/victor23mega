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

            $accountId = -1;
            $accountText = '';
            if(!empty($row[1])){
                $accountCode = trim($row[1]);
                $account = Account::where('code', $accountCode)->first();
                if(!empty($account)){
                    $accountId = $account->id;
                    $accountText = $account->code. ' - '. $account->description. ' - '. $account->location;
                }
            }

            $machineryId = -1;
            $machineryText = '';
            if(!empty($row[2])){
                $machinery = Machinery::whereRaw("LOWER(`code`) = ?", [strtolower($row[2])])->first();
                if(!empty($machinery)){
                    $machineryId = $machinery->id;
                    $machineryText = $machinery->code;
                }
            }

            $qtyFloat = 0;
            if(!empty($row[3])){
                $qtyFloat = Utilities::toFloat($row[3]);
            }

            $docketDetail = collect([
                'item_id'           => $itemId,
                'item_text'         => $itemText,
                'account_id'        => $accountId,
                'account_text'      => $accountText,
                'machinery_id'      => $machineryId,
                'machinery_text'    => $machineryText,
                'qty'               => $qtyFloat,
                'uom'               => $itemUom,
                'shift'             => !empty($row[4]) ? strtoupper(trim($row[4])) : '',
                'time'              => !empty($row[5]) ? trim($row[5]) : '',
                'hm'                => !empty($row[6]) ? trim($row[6]) : '',
                'km'                => !empty($row[7]) ? trim($row[7]) : '',
                'fuelman'           => !empty($row[8]) ? trim($row[8]) : '',
                'operator'          => !empty($row[9]) ? trim($row[9]) : '',
                'remark'            => !empty($row[10]) ? trim($row[10]) : '',
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