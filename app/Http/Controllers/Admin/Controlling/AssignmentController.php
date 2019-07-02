<?php


namespace App\Http\Controllers\Admin\Controlling;


use App\Http\Controllers\Controller;
use App\Models\AssignmentMaterialRequest;
use App\Models\Auth\User\User;
use App\Models\MaterialRequestHeader;
use App\Models\PurchaseRequestHeader;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    public function createAssignmentMr(){
        $mrHeaders = MaterialRequestHeader::where('status_id', 3)
            ->where('is_approved', 1)
            ->where('is_pr_created', 0)
            ->whereNull('assigned_to')
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'mrHeaders'     => $mrHeaders
        ];

        return view('admin.assignment.create_mr')->with($data);
    }

    public function createAssignmentPr(){
        $prHeaders = PurchaseRequestHeader::where('status_id', 3)
            ->where('is_approved', 1)
            ->where('is_pr_created', 0)
            ->whereNull('assigned_to')
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'prHeaders'     => $prHeaders
        ];

        return view('admin.assignment.create_pr')->with($data);
    }

    public function storeAssignmentMr(Request $request){
        try{
            $assignedUserId = $request->input('assigned_user');
            $mrId = $request->input('mr_id');
            $user = Auth::user();
            $now = Carbon::now('Asia/Jakarta')->toDateTimeString();

            // Create assignment entry
            AssignmentMaterialRequest::create([
                'material_request_id'       => $mrId,
                'assigned_user_id'          => $assignedUserId,
                'assigner_user_id'          => $user->id,
                'status_id'                 => 17,
                'created_by'                => $user->id,
                'created_at'                => $now,
                'updated_by'                => $user->id,
                'updated_at'                => $now
            ]);

            // Update MR header
            DB::table('material_request_headers')
                ->where('id', $mrId)
                ->update(['assigned_to' => $assignedUserId]);

            $assignedUser = User::find($assignedUserId);
            $response = collect([
                'assigned_user_id'      => $assignedUserId,
                'assigned_name'         => $assignedUser->name,
                'mr_id'                 => $mrId
            ]);

            return new JsonResponse($response);
        }
        catch (\Exception $ex){
            error_log('storeAssignmentMr error ex: '. $ex);
        }
    }

    public function storeAssignmentPr(Request $request){
        try{
            $assignedUserId = $request->input('assigned_user');
            $prId = $request->input('pr_id');
            $user = Auth::user();
            $now = Carbon::now('Asia/Jakarta')->toDateTimeString();

            // Create assignment entry
            AssignmentMaterialRequest::create([
                'material_request_id'       => $prId,
                'assigned_user_id'          => $assignedUserId,
                'assigner_user_id'          => $user->id,
                'status_id'                 => 17,
                'created_by'                => $user->id,
                'created_at'                => $now,
                'updated_by'                => $user->id,
                'updated_at'                => $now
            ]);

            // Update PR header
            DB::table('purchase_request_headers')
                ->where('id', $prId)
                ->update(['assigned_to' => $assignedUserId]);

            $assignedUser = User::find($assignedUserId);
            $response = collect([
                'assigned_user_id'      => $assignedUserId,
                'assigned_name'         => $assignedUser->name,
                'pr_id'                 => $prId
            ]);

            return new JsonResponse($response);
        }
        catch (\Exception $ex){
            error_log('storeAssignmentPr error ex: '. $ex);
        }
    }
}