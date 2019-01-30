<?php
/**
 * Created by PhpStorm.
 * User: hellb
 * Date: 6/9/2018
 * Time: 7:01 PM
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Imports\AccountsImport;
use App\Imports\InventoryImport;
use App\Models\Account;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\StockCard;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function uploadSupplier(){
        return view('imports.upload-vendor');
    }

    public function uploadInventory(){
        return view('imports.upload-inventory');
    }

    public function importSupplier(Request $request){
        $dateTimeNow = Carbon::now('Asia/Jakarta');
        $user = Auth::user();

        try{
            $data = Excel::load(Input::file('file'), function($reader) {})->get();
            foreach ($data->toArray() as $row) {
                if(!empty($row['nama'])){
                    $email =  $row['email'];
                    if(strpos($email, ',')){
                        $emailArr = explode(',', $email);
                        $email1 = trim($emailArr[0]);
                        $email2 = trim($emailArr[1]);
                    }
                    else{
                        $email1 = $email;
                        $email2 = null;
                    }

                    $phone =  $row['telp'];
                    if(strpos($phone, ',')){
                        $phoneArr = explode(',', $phone);
                        $phone1 = trim($phoneArr[0]);
                        $phone2 = trim($phoneArr[1]);
                    }
                    else{
                        $phone1 = $phone;
                        $phone2 = null;
                    }

                    Supplier::create([
                        'name'                  => $row['nama'],
                        'category'              => $row['kategori'],
                        'email1'                => $email1 ?? null,
                        'email2'                => $email2 ?? null,
                        'phone1'                => $phone1 ?? null,
                        'phone2'                => $phone2 ?? null,
                        'contact_person'        => $row['cp'] ?? null,
                        'city'                  => 'Muara Teweh',
                        'address'               => $row['alamat'] ?? null,
                        'bank_name'             => $row['bank'] ?? 'BANK',
                        'bank_account_number'   => $row['norek'] ?? '123456',
                        'bank_account_name'     => 'PEMILIK REKENING',
                        'created_by'            => $user->id,
                        'created_at'            => $dateTimeNow->toDateTimeString(),
                        'updated_by'            => $user->id,
                        'updated_at'            => $dateTimeNow->toDateTimeString(),
                    ]);
                }
            }

//            Excel::load(Input::file('file'), function ($reader) use($user, $dateTimeNow) {
//                foreach ($reader->toArray() as $key => $value) {
//                    if($row['nama'] != null){
//
//                        Supplier::create([
//                            'name'                  => $row['nama'],
//                            'email'                 => $row['email'],
//                            'phone'                 => $row['telp'],
//                            'contact_person'        => $row['cp'],
//                            'address'               => $row['alamat'],
//                            'city'                  => $row['lokasi'],
//                            'bank_name'             => 'BANK',
//                            'bank_account_number'   => '123456',
//                            'bank_account_name'     => 'PEMILIK REKENING',
//                            'created_by'            => $user->id,
//                            'created_at'            => $dateTimeNow->toDateTimeString(),
//                            'updated_by'            => $user->id,
//                            'updated_at'            => $dateTimeNow->toDateTimeString(),
//                        ]);
//                    }
//                }
//            });

            Session::flash('message', 'Berhasil Import data vendor!');
             return redirect(route('admin.import.suppliers.upload'));
        }
        catch (\Exception $exception){
            return $exception;
        }
    }

    public function importUser(Request $request){
        $dateTimeNow = Carbon::now('Asia/Jakarta');
        $user = Auth::user();

        try{
            Excel::load(Input::file('file'), function ($reader) use($user, $dateTimeNow) {
                foreach ($reader->toArray() as $row) {
                    if($row['nama'] != null){

                    }
                }
            });

            Session::flash('message', 'Berhasil Import data vendor!');

            return redirect(route('admin.import.suppliers.upload'));
        }
        catch (\Exception $exception){
            return $exception;
        }
    }

//    public function importInventory(Request $request){
//        $dateTimeNow = Carbon::now('Asia/Jakarta');
//        $user = Auth::user();
//
//        set_time_limit(0);
//
//        try{
//            $data = Excel::load(Input::file('file'), function($reader) {})->get();
//            foreach ($data->toArray() as $row) {
//                if(!empty($row['kode'])){
//                    $tmp = trim($row['unit']);
//                    $tmp = strtoupper($tmp);
//                    if($tmp !== 'CONSUMABLE' &&
//                        $tmp !== 'GENERAL' &&
//                        $tmp !== 'ELEKTRIK' &&
//                        $tmp !== 'FABRIKASI' &&
//                        $tmp !== 'FORM' &&
//                        $tmp !== 'MATERIAL' &&
//                        $tmp !== 'OFFICE' &&
//                        $tmp !== 'TOOLS MEKANIK' &&
//                        $tmp !== 'SURVEY' &&
//                        $tmp !== 'ENVIRONMENTAL' &&
//                        $tmp !== 'ALAT KERJA' &&
//                        $tmp !== 'TOOLS' &&
//                        $tmp !== 'ATK OFFICE' &&
//                        $tmp !== 'KANTIN' &&
//                        $tmp !== 'ATK' &&
//                        $tmp !== 'KOMUNIKASI'){
//                        $unitType = $tmp;
//                    }
//
//                    $stock = trim($row['stok']);
//
//                    if($stock !== '0'){
//                        $item = Item::create([
//                            'code'                  => $row['kode'] ?? 'KOSONG',
//                            'name'                  => $row['nama'] ?? 'KOSONG',
//                            'part_number'           => $row['part'] ?? 'KOSONG',
//                            'uom'                   => strtoupper($row['uom']) ?? 'PCS',
//                            'group_id'              => 1,
//                            'machinery_type'        => $unitType ?? null,
//                            'stock'                 => $stock ?? 0,
//                            'value'                 => 0,
//                            'created_by'            => $user->id,
//                            'created_at'            => $dateTimeNow->toDateTimeString(),
//                            'updated_by'            => $user->id,
//                            'updated_at'            => $dateTimeNow->toDateTimeString()
//                        ]);
//
//                        if(!empty($stock)){
//                            $itemStock = ItemStock::create([
//                                'item_id'       => $item->id,
//                                'warehouse_id'  => 2,
//                                'location'      => $row['rak'] ?? null,
//                                'stock'         => $stock,
//                                'created_by'    => $user->id,
//                                'created_at'    => $dateTimeNow->toDateTimeString()
//                            ]);
//                        }
//
//                        if(!empty($stock)){
//                            // Create Stock Card
//                            StockCard::create([
//                                'item_id'       => $item->id,
//                                'reference'     => 'Balance Awal',
//                                'in_qty'        => $stock,
//                                'out_qty'       => 0,
//                                'result_qty'    => $stock,
//                                'warehouse_id'  => 3,
//                                'created_by'    => $user->id,
//                                'created_at'    => $dateTimeNow->toDateTimeString(),
//                                'updated_by'    => $user->id,
//                                'updated_at'    => $dateTimeNow->toDateTimeString()
//                            ]);
//                        }
//                    }
//                }
//            }
//
//            Session::flash('message', 'Berhasil Import data inventory!');
//            return redirect(route('admin.import.items.upload'));
//        }
//        catch (\Exception $exception){
//            return $exception;
//        }
//    }

    public function importCostCode(Request $request){
        try{
            Excel::import(new AccountsImport(), request()->file('file'));

            Session::flash('message', 'Berhasil Import data Chart of Accounts!');
            return redirect(route('admin.import.items.upload'));
        }
        catch (\Exception $exception){
            return $exception;
        }
    }

    public function importInventory(Request $request){
        try{
            Excel::import(new InventoryImport(), request()->file('file'));

            Session::flash('message', 'Berhasil Import data Inventory!');
            return redirect(route('admin.import.items.upload'));
        }
        catch (\Exception $exception){
            return $exception;
        }
    }
}