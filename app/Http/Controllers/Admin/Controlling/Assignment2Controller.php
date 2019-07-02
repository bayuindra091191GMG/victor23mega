<?php


namespace App\Http\Controllers\Admin\Controlling;

use App\Exports\MonitoringMRExport;
use App\Http\Controllers\Controller;
use App\Models\AssignmentPurchaseRequest;
use App\Models\Department;
use App\Models\MaterialRequestHeader;
use App\Models\Site;
use App\Transformer\Controlling\AssignmentPrTransformer;
use App\Transformer\Controlling\MonitoringMRTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use PDF3;

class Assignment2Controller extends Controller
{
    public function assigmentMRIndex(){
        return view('admin.assignment.staff_index_mr');
    }

    public function assigmentPRIndex(){
        return view('admin.assignment.staff_index_pr');
    }
    public function getIndexPr()
    {
        try{
            $histories = AssignmentPurchaseRequest::query();
            return DataTables::of($histories)
                ->setTransformer(new AssignmentPrTransformer())
                ->addIndexColumn()
                ->make(true);
        }
        catch (\Exception $ex){
            error_log($ex);
        }
    }

}