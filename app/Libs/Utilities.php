<?php

namespace App\Libs;

use App\Models\AutoNumber;
use Carbon\Carbon;

class Utilities
{
    public static function GenerateNumber($code, $nextNumber)
    {
        $modulus = "";
        $now = Carbon::now('Asia/Jakarta');

        $mod = strlen($nextNumber);
        switch ($mod){
            case 1:
                $modulus = "000";
                break;
            case 2:
                $modulus = "00";
                break;
            case 3:
                $modulus = "0";
                break;
        }

        $number = $code."/".$now->year."/".$now->month."/".$modulus.$nextNumber;
        return $number;
    }

    //  Get next incremental number of transaction number
    /**
     * @param $prepend
     * @return int
     * @throws \Exception
     */
    public static function GetNextAutoNumber($prepend){
        try{
            $nextNo = 1;
            $autoNumber = AutoNumber::find($prepend);
            if(empty($autoNumber)){
                AutoNumber::create([
                    'id'        => $prepend,
                    'next_no'   => 1
                ]);
            }
            else{
                $nextNo = $autoNumber->next_no;
            }

            return $nextNo;
        }
        catch (\Exception $ex){
            throw $ex;
        }
    }

    // Update incremental number of transaction number
    /**
     * @param $prepend
     * @throws \Exception
     */
    public static function UpdateAutoNumber($prepend){
        try{
            $autoNumber = AutoNumber::find($prepend);
            $autoNumber->next_no++;
            $autoNumber->save();
        }
        catch (\Exception $ex){
            throw $ex;
        }
    }

    public static function GenerateNumberPurchaseOrder($code, $nextNumber)
    {
        $modulus = "";
        $now = Carbon::now('Asia/Jakarta');

        $mod = strlen($nextNumber);
        switch ($mod){
            case 1:
                $modulus = "000000";
                break;
            case 2:
                $modulus = "00000";
                break;
            case 3:
                $modulus = "0000";
                break;
            case 4:
                $modulus = "000";
                break;
            case 5:
                $modulus = "00";
                break;
            case 6:
                $modulus = "0";
                break;
        }

        $number = $code."/".$now->year."/".$modulus.$nextNumber;
        return $number;
    }

    public static function arrayIsUnique($array){
        return array_unique($array) == $array;
    }

    public static function toFloat($raw){
        $valueStr1 = str_replace('.','', $raw);
        $valueStr2 = str_replace(',', '.', $valueStr1);

        return (double) $valueStr2;
    }
}