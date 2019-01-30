<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 05/10/2018
 * Time: 13:27
 */

namespace App\Imports;


use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class AccountsImport implements ToCollection
{

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $dateTimeNow = Carbon::now('Asia/Jakarta');
        $user = Auth::user();

        $totalDuplicate = 0;

        foreach ($rows as $row){
            if(!Account::where('code', $row[0])->exists())
            {
                Account::create([
                    'code'              => $row[0],
                    'location'          => $row[1],
                    'department'        => $row[2],
                    'division'          => $row[3],
                    'description'       => $row[4],
                    'remark'            => $row[5],
                    'brand'             => $row[6] ?? null,
                    'created_by'        => $user->id,
                    'created_at'        => $dateTimeNow->toDateTimeString(),
                    'updated_by'        => $user->id,
                    'updated_at'        => $dateTimeNow->toDateTimeString()
                ]);
            }
            else{
                $totalDuplicate++;
                error_log("DUPLICATE");
            }
        }

        error_log($totalDuplicate);
    }
}