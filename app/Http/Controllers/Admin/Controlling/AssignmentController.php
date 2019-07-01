<?php


namespace App\Http\Controllers\Admin\Controlling;


use App\Http\Controllers\Controller;
use App\Models\MaterialRequestHeader;

class AssignmentController extends Controller
{
    public function createAssignmentMr(){
        $mrHeaders = MaterialRequestHeader::where('status_id', 3)
            ->where('is_approved', 1)
            ->orderBy('created_at', 'desc')
            ->get();


    }
}