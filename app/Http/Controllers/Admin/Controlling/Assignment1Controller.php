<?php


namespace App\Http\Controllers\Admin\Controlling;


use App\Http\Controllers\Controller;
use App\Models\AssignmentMaterialRequest;
use App\Models\MaterialRequestHeader;
use App\Transformer\MasterData\AssigmentMrTransformer;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class Assignment1Controller extends Controller
{
    public function historyAssigmentIndex(){
        return view('admin.assigment.index_mr');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getIndexMr()
    {
        try{
            $histories = AssignmentMaterialRequest::query();
            return DataTables::of($histories)
                ->setTransformer(new AssigmentMrTransformer)
                ->addIndexColumn()
                ->make(true);
        }
        catch (\Exception $ex){
            error_log($ex);
        }
    }
}