<?php


namespace App\Http\Controllers\Admin\Controlling;


use App\Http\Controllers\Controller;
use App\Models\AssignmentMaterialRequest;
use App\Models\AssignmentPurchaseRequest;
use App\Models\Auth\User\User;
use App\Models\MaterialRequestHeader;
use App\Models\PurchaseRequestHeader;
use App\Transformer\Controlling\AssignmentMrTransformer;
use App\Transformer\Controlling\AssignmentPrTransformer;
use App\Transformer\Controlling\AssignmentTrackingTransformer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AssignmentController extends Controller
{
    public function indexHistoryAssignmentMr(Request $request){
        $filterDateStart = Carbon::today()->subMonths(1)->format('d M Y');
        $filterDateEnd = Carbon::today()->format('d M Y');

        if($request->date_start != null && $request->date_end != null){

            $dateStartDecoded = rawurldecode($request->date_start);
            $dateEndDecoded = rawurldecode($request->date_end);
            $start = Carbon::createFromFormat('d M Y', $dateStartDecoded, 'Asia/Jakarta');
            $end = Carbon::createFromFormat('d M Y', $dateEndDecoded, 'Asia/Jakarta');

            if($end->greaterThanOrEqualTo($start)){
                $filterDateStart = $dateStartDecoded;
                $filterDateEnd = $dateEndDecoded;
            }
        }

        //dd($filterDateStart. ' - '. $filterDateEnd);

        $data = [
            'filterDateStart'   => $filterDateStart,
            'filterDateEnd'     => $filterDateEnd
        ];

        return view('admin.assignment.index_mr')->with($data);
    }

    public function indexHistoryAssignmentPr(Request $request){
        $filterDateStart = Carbon::today()->subMonths(1)->format('d M Y');
        $filterDateEnd = Carbon::today()->format('d M Y');

        if($request->date_start != null && $request->date_end != null){

            $dateStartDecoded = rawurldecode($request->date_start);
            $dateEndDecoded = rawurldecode($request->date_end);
            $start = Carbon::createFromFormat('d M Y', $dateStartDecoded, 'Asia/Jakarta');
            $end = Carbon::createFromFormat('d M Y', $dateEndDecoded, 'Asia/Jakarta');

            if($end->gt($start)){
                $filterDateStart = $dateStartDecoded;
                $filterDateEnd = $dateEndDecoded;
            }
        }

        $data = [
            'filterDateStart'   => $filterDateStart,
            'filterDateEnd'     => $filterDateEnd
        ];

        return view('admin.assignment.index_pr')->with($data);
    }

    public function getIndexMr(Request $request)
    {
        try{
            $start = Carbon::createFromFormat('d M Y', $request->input('date_start'), 'Asia/Jakarta');
            $end = Carbon::createFromFormat('d M Y', $request->input('date_end'), 'Asia/Jakarta');
            $start->subDays(1);
            $end->addDays(1);

            $histories = AssignmentMaterialRequest::whereBetween('created_at', array($start->toDateTimeString(), $end->toDateTimeString()))
                ->get();

            return DataTables::of($histories)
                ->setTransformer(new AssignmentMrTransformer)
                ->addIndexColumn()
                ->make(true);
        }
        catch (\Exception $ex){
            error_log($ex);
        }
    }

    public function getIndexStaffMr(Request $request)
    {
        try{
//            $start = Carbon::createFromFormat('d M Y', $request->input('date_start'), 'Asia/Jakarta');
//            $end = Carbon::createFromFormat('d M Y', $request->input('date_end'), 'Asia/Jakarta');

            $user = Auth::user();

            $assignmentMrRawList = AssignmentMaterialRequest::where('assigned_user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $assignmentMrList = collect();
            foreach ($assignmentMrRawList as $mrToAssign){
                if($mrToAssign->material_request_header->is_pr_created  === 1){
                    $prHeader = $mrToAssign->material_request_header->purchase_request_headers->first();
                    if($prHeader->is_all_poed === 0 || $prHeader->is_all_poed === 2){
                        $assignmentMrList->push($mrToAssign);
                    }
                }
                else{
                    $assignmentMrList->push($mrToAssign);
                }
            }

            return DataTables::of($assignmentMrList)
                ->setTransformer(new AssignmentMrTransformer)
                ->addIndexColumn()
                ->make(true);
        }
        catch (\Exception $ex){
            error_log($ex);
        }
    }

    public function getIndexPr(Request $request)
    {
        try{
            $start = Carbon::createFromFormat('d M Y', $request->input('date_start'), 'Asia/Jakarta');
            $end = Carbon::createFromFormat('d M Y', $request->input('date_end'), 'Asia/Jakarta');

            $histories = AssignmentPurchaseRequest::whereBetween('created_at', array($start->toDateTimeString(), $end->toDateTimeString()))
                ->get();

            return DataTables::of($histories)
                ->setTransformer(new AssignmentPrTransformer())
                ->addIndexColumn()
                ->make(true);
        }
        catch (\Exception $ex){
            error_log($ex);
        }
    }

    public function createAssignmentMr(){
        $mrHeaders = MaterialRequestHeader::with(['department', 'machinery', 'createdBy'])
            ->where('status_id', 3)
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
            ->where('is_all_poed', 0)
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

            // Check existing assignment
            $existAssignment = AssignmentMaterialRequest::where('material_request_id', $mrId)->first();
            if(!empty($existAssignment)){
                $response = collect([
                    'error'                 => 'MR sudah di-assign, mohon refresh halaman!',
                    'assigned_user_id'      => $assignedUserId,
                    'assigned_name'         => '',
                    'mr_id'                 => $mrId
                ]);

                return new JsonResponse($response);
            }

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
                'error'                 => 'none',
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

            // Check existing assignment
            $existAssignment = AssignmentPurchaseRequest::where('purchase_request_id', $prId)->first();
            if(!empty($existAssignment)){
                $response = collect([
                    'error'                 => 'PR sudah di-assign, mohon refresh halaman!',
                    'assigned_user_id'      => $assignedUserId,
                    'assigned_name'         => '',
                    'pr_id'                 => $prId
                ]);

                return new JsonResponse($response);
            }

            // Create assignment entry
            AssignmentPurchaseRequest::create([
                'purchase_request_id'       => $prId,
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

    public function track(Request $request){
        try{

            // Filter selected user
            $selectedUserId = -1;
            $selectedUser = null;
            if($request->user_id != null){
                $selectedUserId = intval($request->user_id);
                if($selectedUserId > 0){
                    $selectedUser = User::find($selectedUserId);
                    if(empty($selectedUser)){
                        $selectedUserId = -1;
                    }
                }
            }

            // Filter "Staff Purchasing" role
            $users = User::orderBy('name')->get();
            $purchasingUsers = collect();
            foreach ($users as $user){
                if(!empty($user->roles->pluck('id')[0])){
                    $roleId = $user->roles->pluck('id')[0];
                    if($roleId === 5){
                        $purchasingUsers->push($user);
                    }
                }
            }

            // Date filter
            $filterDateStart = Carbon::today()->subMonths(1)->format('d M Y');
            $filterDateEnd = Carbon::today()->format('d M Y');

            if($request->date_start != null && $request->date_end != null){

                $dateStartDecoded = rawurldecode($request->date_start);
                $dateEndDecoded = rawurldecode($request->date_end);
                $start = Carbon::createFromFormat('d M Y', $dateStartDecoded, 'Asia/Jakarta');
                $end = Carbon::createFromFormat('d M Y', $dateEndDecoded, 'Asia/Jakarta');

                if($end->greaterThanOrEqualTo($start)){
                    $filterDateStart = $dateStartDecoded;
                    $filterDateEnd = $dateEndDecoded;
                }
            }

            // Document type filter
            $docType = 'mr';
//            if($request->doc_type != null){
//                $docType = $request->doc_type;
//            }

            $data = [
                'selectedUserId'        => $selectedUserId,
                'selectedUser'          => $selectedUser,
                'purchasingUsers'       => $purchasingUsers,
                'filterDateStart'       => $filterDateStart,
                'filterDateEnd'         => $filterDateEnd,
                'docType'               => $docType
            ];

            return view('admin.assignment.track')->with($data);
        }
        catch (\Exception $ex){
            dd($ex);
            error_log('AssignmentController - track error ex: '. $ex);
        }
    }

    public function getTrack(Request $request)
    {
        try{
//            $docType = $request->input('doc_type');
            $docType = 'mr';
            $userId = intval($request->input('user_id'));
            $start = Carbon::createFromFormat('d M Y', $request->input('date_start'), 'Asia/Jakarta');
            $end = Carbon::createFromFormat('d M Y', $request->input('date_end'), 'Asia/Jakarta');
            $start->subDays(1);
            $end->addDays(1);

            if($docType === 'mr'){
                $assignments = AssignmentMaterialRequest::whereBetween('created_at', array($start->toDateTimeString(), $end->toDateTimeString()));
                if($userId > 0){
                    $assignments = $assignments->where('assigned_user_id', $userId)->get();
                }
                else{
                    $assignments = $assignments->get();
                }

                // Sort by staff name using closure
                $assignments = $assignments->sortBy(function($assignment) {
                    return $assignment->assignedUser->name;
                });

                $assignments = $assignments->sortByDesc('created_at');

                return DataTables::of($assignments)
                    ->setTransformer(new AssignmentTrackingTransformer)
                    ->addIndexColumn()
                    ->make(true);
            }
            else{
                $assignments = AssignmentPurchaseRequest::whereBetween('created_at', array($start->toDateTimeString(), $end->toDateTimeString()));
                if($userId > 0){
                    $assignments = $assignments->where('assigned_user_id', $userId)->get();
                }
                else{
                    $assignments = $assignments->get();
                }

                // Sort by staff name using closure
                $assignments = $assignments->sortBy(function($assignment) {
                    return $assignment->assignedUser->name;
                });

                $assignments = $assignments->sortByDesc('created_at');

                return DataTables::of($assignments)
                    ->setTransformer(new AssignmentPrTransformer)
                    ->addIndexColumn()
                    ->make(true);
            }
        }
        catch (\Exception $ex){
            error_log($ex);
        }
    }
}