<?php

namespace App\Http\Controllers;

use Dompdf\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{

    public function index(){
        return view('excel');
    }

   public function readData(Request $request){
       $result = "";

       try{
           Excel::load(Input::file('file'), function ($reader) use($result) {

               foreach ($reader->toArray() as $row) {
                   dd($row);
               }
           });

           return $result;
       }
       catch (Exception $exception){
           return $exception;
       }
   }
}
