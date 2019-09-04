<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 07/02/2018
 * Time: 10:22
 */

namespace App\Http\Controllers\Admin\Purchasing;


use App\Exports\PurchaseRequestExport;
use App\Http\Controllers\Controller;
use App\Libs\Utilities;
use App\Mail\ApprovalPurchaseRequestCreated;
use App\Mail\PurchaseRequestCreatedMailNotification;
use App\Models\ApprovalRule;
use App\Models\AssignmentMaterialRequest;
use App\Models\AssignmentPurchaseRequest;
use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use App\Models\Department;
use App\Models\Document;
use App\Models\MaterialRequestHeader;
use App\Models\NumberingSystem;
use App\Models\PermissionMenu;
use App\Models\PreferenceCompany;
use App\Models\PurchaseOrderHeader;
use App\Models\PurchaseRequestDetail;
use App\Models\PurchaseRequestHeader;
use App\Models\Status;
use App\Notifications\PurchaseRequestCreated;
use App\Transformer\Purchasing\PurchaseRequestHeaderTransformer;
use App\Transformer\Purchasing\PurchaseRequestWarningTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade as PDF;
use PDF3;

class PurchaseRequestHeaderController extends Controller
{
    public function index(Request $request){

        $filterStatus = '3';
        if($request->status != null){
            $filterStatus = $request->status;
        }

        return View('admin.purchasing.purchase_requests.index', compact('filterStatus'));
    }

    public function indexWarning(Request $request){

        return View('admin.purchasing.purchase_requests.index_warning');
    }

    public function beforeCreate(){
        return View('admin.purchasing.purchase_requests.before_create');
    }

    public function create(){

        if(empty(request()->mr)){
            return redirect()->route('admin.purchase_requests.before_create');
        }

        $mrId = request()->mr;
        $materialRequest = MaterialRequestHeader::find($mrId);

        // Validate MR approval
        if($materialRequest->is_approved === 0){
            return redirect()->route('admin.purchase_requests.before_create');
        }

        // Validate PR exists
        if(PurchaseRequestHeader::where('material_request_id', $mrId)->exists()){
            return redirect()->route('admin.purchase_requests.before_create');
        }

        $departments = Department::all();

        // Numbering System
        $user = Auth::user();
        $sysNo = NumberingSystem::where('doc_id', '3')->first();
        $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumber($docCode, $sysNo->next_no);

        $data = [
            'departments'       => $departments,
            'autoNumber'        => $autoNumber,
            'materialRequest'   => $materialRequest
        ];

        return View('admin.purchasing.purchase_requests.create')->with($data);
    }

    public function show(PurchaseRequestHeader $purchase_request){
        $user = \Auth::user();

        // Check menu permission
        $roleId = $user->roles->pluck('id')[0];
        if(!PermissionMenu::where('role_id', $roleId)->where('menu_id', 23)->first()){
            Session::flash('error', 'Level Akses anda tidak mencukupi!');
            return redirect()->back();
        }

        $header = $purchase_request;

        $date = Carbon::parse($purchase_request->date)->format('d M Y');
        $priorityLimitDate = Carbon::parse($purchase_request->priority_limit_date)->format('d M Y');

        //Check Approval & Permission to Print
//        $user = \Auth::user();
//        $permission = true;
//        $isUserMustApprove = false;
//        //Kondisi belum diapprove
//        $status = 0;
//        $arrData = array();
//
//        // Check Approval Feature
//        $preference = PreferenceCompany::find(1);
//        $approvals = null;
//
//        if($preference->approval_setting == 1) {
//            $tempApprove = ApprovalRule::where('document_id', 3)->where('user_id', $user->id)->get();
//            $approvals = ApprovalRule::where('document_id', 3)->get();
//            $approvalPr = ApprovalPurchaseRequest::where('purchase_request_id', $purchase_request->id)->get();
//
//            if ($tempApprove->count() > 0) {
//                $isUserMustApprove = true;
//            }
//
//            $approvalData = ApprovalPurchaseRequest::where('purchase_request_id', $header->id)->where('user_id', $user->id)->first();
//            if(!empty($approvalData)){
//                $isUserMustApprove = false;
//            }
//
//            //Kondisi Approve Sebagian
//            $approvalPrData = ApprovalPurchaseRequest::where('purchase_request_id', $header->id)->get();
//            if($approvalData != null || $approvalPrData != null){
//                $status = $approvalPrData->count();
//
//                //Kondisi Semua sudah Approve
//                if($approvalPrData->count() == $approvals->count()){
//                    $status = 99;
//                }
//            }
//
//            if ($approvals->count() != $approvalPr->count()) {
//                $permission = false;
//            }
//
//            foreach($approvals as $approval)
//            {
//                $flag = 0;
//                foreach($approvalPr as $data)
//                {
//                    if($data->user_id == $approval->user_id)
//                    {
//                        $flag = 1;
//                    }
//                }
//
//                if($flag == 1){
//                    $arrData[] = $approval->user->name . " - Sudah Approve";
//                }
//                else{
//                    $arrData[] = $approval->user->name . " - Belum Approve";
//                }
//            }
//        }

        // Check PO created
        // IF PO created = cannot process to PO & cannot edit
        $isPoCreated = $header->is_all_poed === 1 ? true : false;

//        $data = [
//            'header'            => $header,
//            'date'              => $date,
//            'priorityLimitDate' => $priorityLimitDate,
//            'permission'        => $permission,
//            'approveOrder'      => $isUserMustApprove,
//            'status'            => $status,
//            'approvalData'      => $arrData,
//            'setting'           => $preference->approval_setting,
//            'isPoCreated'       => $isPoCreated
//        ];

        // Get MR type
        $mrType = $header->material_request_header->type;

        // Get tracking
        $trackedPoHeaders = $header->purchase_order_headers;
        $trackedGrHeaders = new Collection();
        $trackedPiHeaders = new Collection();
        $trackedSjHeaders = new Collection();
        if($trackedPoHeaders->count() > 0){
            foreach ($trackedPoHeaders as $poHeader){
                foreach ($poHeader->item_receipt_headers as $grHeader){
                    $trackedGrHeaders->add($grHeader);
                }

                foreach ($poHeader->purchase_invoice_headers as $piHeader){
                    $trackedPiHeaders->add($piHeader);
                }
            }
        }

        if($trackedGrHeaders->count() > 0){
            foreach ($trackedGrHeaders as $grHeader){
                foreach ($grHeader->delivery_order_headers as $doHeader){
                    $trackedSjHeaders->add($doHeader);
                }
            }
        }

        // Check assigner role id
        $preference = PreferenceCompany::find(1);
        $assignerRoleId = intval($preference->assigner_role_id);
        $isAssignerRole = false;
        if($roleId === $assignerRoleId || $roleId === 1){
            $isAssignerRole = true;
        }

        // Check document assigned
        $assignmentPr = AssignmentPurchaseRequest::where('purchase_request_id', $header->id)
            ->first();

        $data = [
            'header'                    => $header,
            'date'                      => $date,
            'priorityLimitDate'         => $priorityLimitDate,
            'isPoCreated'               => $isPoCreated,
            'mrType'                    => $mrType,
            'trackedPoHeaders'          => $trackedPoHeaders,
            'trackedGrHeaders'          => $trackedGrHeaders,
            'trackedSjHeaders'          => $trackedSjHeaders,
            'trackedPiHeaders'          => $trackedPiHeaders,
            'isAssignerRole'            => $isAssignerRole,
            'assignmentPr'              => $assignmentPr
        ];

        return View('admin.purchasing.purchase_requests.show')->with($data);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'pr_code'       => 'required|max:30|regex:/^\S*$/u',
            'km'            => 'max:20',
            'hm'            => 'max:20',
            'date'          => 'required'
        ],[
            'pr_code.required'      => 'Nomor PR wajib diisi!',
            'pr_code.regex'         => 'Nomor PR harus tanpa spasi!'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate details
        $items = $request->input('item');

        if(count($items) == 0){
            return redirect()->back()->withErrors('Detail barang wajib diisi!', 'default')->withInput($request->all());
        }

        $qtys = $request->input('qty');
        $valid = true;
        $i = 0;
        foreach($items as $item){
            if(empty($item)) $valid = false;
            if(empty($qtys[$i]) || $qtys[$i] == '0') $valid = false;
            $i++;
        }

        if(!$valid){
            return redirect()->back()->withErrors('Detail barang dan jumlah wajib diisi!', 'default')->withInput($request->all());
        }

        // Get MR id
        $mrId = $request->input('mr_id');

        // Validate MR relationship
        $validItem = true;
        $validQty = true;
        $i = 0;
        $materialRequest = MaterialRequestHeader::find($mrId);
        foreach($items as $item){
            if(!empty($item)){
                $mrDetail = $materialRequest->material_request_details->where('item_id', $item)->first();
                if(empty($mrDetail)){
                    $validItem = false;
                    break;
                }
                else{
                    if($qtys[$i] > $mrDetail->quantity){
                        $validQty = false;
                        break;
                    }
                }
                $i++;
            }
        }

        if(!$validItem){
            return redirect()->back()->withErrors('Inventory tidak ada dalam MR!', 'default')->withInput($request->all());
        }
        if(!$validQty){
            return redirect()->back()->withErrors('Kuantitas inventory tidak boleh melebihi kuantitas MR!', 'default')->withInput($request->all());
        }

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $doc = Document::find(3);

        // Generate auto number
        if($request->filled('auto_number')){
    //        $sysNo = NumberingSystem::where('doc_id', '3')->first();
    //        $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
    //        $prCode = Utilities::GenerateNumber($docCode, $sysNo->next_no);

            $prPrepend = $doc->code. '/'. $now->year. '/'. $now->month;
            $sysNo = Utilities::GetNextAutoNumber($prPrepend);

            $docCode = $doc->code. '-'. $user->employee->site->code;
            $prCode = Utilities::GenerateNumber($docCode, $sysNo);

            // Check existing number
            if(PurchaseOrderHeader::where('code', $prCode)->exists()){
                return redirect()->back()->withErrors('Nomor PR sudah terdaftar!', 'default')->withInput($request->all());
            }

            //$sysNo->next_no++;
            //$sysNo->save();
        }
        else{
            $prCode = $request->input('pr_code');

            // Check existing number
            if(PurchaseOrderHeader::where('code', $prCode)->exists()){
                return redirect()->back()->withErrors('Nomor PR sudah terdaftar!', 'default')->withInput($request->all());
            }
        }

        $now = Carbon::now('Asia/Jakarta');
        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');
        $limitDate = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');

        $priority = $request->input('priority');
        if($priority == 'Part - P1' || $priority == 'Non-Part - P1'){
            $limitDate->addDays(8);
        }
        elseif($priority == 'Part - P2' || $priority == 'Non-Part - P2'){
            $limitDate->addDays(15);
        }
        else{
            $limitDate->addDays(30);
        }

        $mrHeader = MaterialRequestHeader::find($mrId);

        // Mark pr created
        $mrHeader->is_pr_created = 1;
        $mrHeader->save();

        $prHeader = PurchaseRequestHeader::create([
            'code'                  => $prCode,
            'site_id'               => $user->employee->site_id,
            'material_request_id'   => $mrId,
            'date'                  => $date->toDateTimeString(),
            'department_id'         => $mrHeader->department_id,
            'priority'              => $mrHeader->priority,
            'priority_limit_date'   => $limitDate->toDateTimeString(),
            'km'                    => $mrHeader->km,
            'hm'                    => $mrHeader->hm,
            'is_approved'           => 1,
            'approved_date'         => $now->toDateTimeString(),
            'is_al_poed'            => 0,
            'is_retur'              => 0,
            'status_id'             => 3,
            'created_by'            => $user->id,
            'created_at'            => $now->toDateTimeString(),
            'updated_by'            => $user->id,
            'updated_at'            => $now->toDateTimeString(),
    //        'warehouse_id'          => $materialRequest->warehouse_id,
            'is_reorder'            => $materialRequest->is_reorder
        ]);

        if($request->filled('machinery_id')){
            $prHeader->machinery_id = $request->input('machinery_id');
            $prHeader->save();
        }

        // Create purchase request detail
        $qty = $request->input('qty');
        $remark = $request->input('remark');
        $idx = 0;
        foreach($request->input('item') as $item){
            if(!empty($item)){
                $prDetail = PurchaseRequestDetail::create([
                    'header_id'         => $prHeader->id,
                    'item_id'           => $item,
                    'quantity'          => $qty[$idx],
                    'quantity_poed'     => 0,
                    'quantity_invoiced' => 0,
                    'quantity_retur'    => 0
                ]);

                if(!empty($remark[$idx])) $prDetail->remark = $remark[$idx];
                $prDetail->save();
            }
            $idx++;
        }

        // Increase autonumber
        if($request->filled('auto_number')){
            $prPrepend = $doc->code. '/'. $now->year. '/'. $now->month;
            Utilities::UpdateAutoNumber($prPrepend);
        }

        // Check Approval Feature
        $preference = PreferenceCompany::find(1);

        try{
            if($preference->approval_setting == 1) {
                $approvals = ApprovalRule::where('document_id', 3)->get();
                if($approvals->count() > 0){
                    foreach($approvals as $approval){
                        if(!empty($approval->user->email_address)){
                            Mail::to($approval->user->email_address)->send(new ApprovalPurchaseRequestCreated($prHeader, $approval->user));
                        }
                    }
                }
            }
        }
        catch (\Exception $ex){
            dd($ex);
        }

        try{
            // Send notification
            $mrCreator = $prHeader->material_request_header->createdBy;
            $mrCreator->notify(new PurchaseRequestCreated($prHeader, 'true'));

            $roleIds = [12,13];
            $roles = Role::whereIn('id', $roleIds)->get();
            foreach($roles as $role){
                $users =  $role->users()->get();
                if($users->count() > 0){
                    foreach ($users as $notifiedUser){
                        if($notifiedUser->id !== $mrCreator->id){
                            $notifiedUser->notify(new PurchaseRequestCreated($prHeader, 'false'));
                        }
                    }
                }
            }

            // Send email notification to purchasing users
            $environment = env('APP_ENV','local');
            if($environment === 'prod'){
                // Ginanjar
                $purchasingUser1 = User::find(16);
                Mail::to($purchasingUser1->email_address)->send(new PurchaseRequestCreatedMailNotification($prHeader, $purchasingUser1));

                // Petrus
                $purchasingUser2 = User::find(25);
                Mail::to($purchasingUser2->email_address)->send(new PurchaseRequestCreatedMailNotification($prHeader, $purchasingUser2));

                // Karina
                $purchasingUser3 = User::find(47);
                Mail::to($purchasingUser3->email_address)->send(new PurchaseRequestCreatedMailNotification($prHeader, $purchasingUser3));
            }
        }
        catch(\Exception $ex){
            error_log($ex);
        }

        // Check assignment
//        $assignmentMr = AssignmentMaterialRequest::where('material_request_id', $mrHeader->id)
//            ->where('status_id', 17)
//            ->first();
//
//        if(!empty($assignmentMr)){
//            $assignmentMr->status_id = 18;
//            $assignmentMr->processed_by = $user->id;
//            $assignmentMr->processed_date = $now->toDateTimeString();
//
//            if($user->id != $assignmentMr->assigned_user_id){
//                $assignmentMr->is_different_processor = 1;
//            }
//
//            $assignmentMr->save();
//
//            // Create PR assignment entry
//            AssignmentPurchaseRequest::create([
//                'purchase_request_id'       => $prHeader->id,
//                'assigned_user_id'          => $assignmentMr->assigned_user_id,
//                'assigner_user_id'          => $user->id,
//                'status_id'                 => 17,
//                'created_by'                => $user->id,
//                'created_at'                => $now,
//                'updated_by'                => $user->id,
//                'updated_at'                => $now
//            ]);
//
//            $prHeader->assigned_to = $assignmentMr->assigned_user_id;
//            $prHeader->save();
//        }

        // Update processed by for assignment
        $mrHeader->processed_by = $user->id;
        $mrHeader->save();

        Session::flash('message', 'Berhasil membuat purchase request!');

        return redirect()->route('admin.purchase_requests.show', ['purchase_request' => $prHeader]);
    }

    public function storeService(Request $request){
        $validator = Validator::make($request->all(),[
            'pr_code'       => 'required|max:30|regex:/^\S*$/u',
            'km'            => 'max:20',
            'hm'            => 'max:20',
            'date'          => 'required'
        ],[
            'pr_code.required'      => 'Nomor PR wajib diisi!',
            'pr_code.regex'         => 'Nomor PR harus tanpa spasi!'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate MR number
//        if(empty($request->input('mr_code')) && empty($request->input('mr_id'))){
//            return redirect()->back()->withErrors('Nomor MR wajib diisi!', 'default')->withInput($request->all());
//        }

        // Get MR id
        $mrId = $request->input('mr_id');

        $user = \Auth::user();

        // Generate auto number
        $prCode = 'default';
        if($request->filled('auto_number')){
            $sysNo = NumberingSystem::where('doc_id', '3')->first();
            $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
            $prCode = Utilities::GenerateNumber($docCode, $sysNo->next_no);

            // Check existing number
            if(PurchaseOrderHeader::where('code', $prCode)->exists()){
                return redirect()->back()->withErrors('Nomor PR sudah terdaftar!', 'default')->withInput($request->all());
            }

            $sysNo->next_no++;
            $sysNo->save();
        }
        else{
            $prCode = $request->input('pr_code');

            // Check existing number
            if(PurchaseOrderHeader::where('code', $prCode)->exists()){
                return redirect()->back()->withErrors('Nomor PR sudah terdaftar!', 'default')->withInput($request->all());
            }
        }

        $now = Carbon::now('Asia/Jakarta');
        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');
        $limitDate = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');

        $priority = $request->input('priority');
        if($priority == '1'){
            $limitDate->addDays(8);
        }
        elseif($priority == '2'){
            $limitDate->addDays(15);
        }
        else{
            $limitDate->addDays(22);
        }

        $mrHeader = MaterialRequestHeader::find($mrId);

        // Mark pr created
        $mrHeader->is_pr_created = 1;
        $mrHeader->save();

        $prHeader = PurchaseRequestHeader::create([
            'code'                  => $prCode,
            'site_id'               => $user->employee->site_id,
            'material_request_id'   => $mrId,
            'date'                  => $date->toDateTimeString(),
            'department_id'         => $mrHeader->department_id,
            'priority'              => $mrHeader->priority,
            'priority_limit_date'   => $limitDate->toDateTimeString(),
            'km'                    => $mrHeader->km,
            'hm'                    => $mrHeader->hm,
            'is_approved'           => 1,
            'approved_date'         => $now->toDateTimeString(),
            'is_al_poed'            => 0,
            'is_retur'              => 0,
            'status_id'             => 3,
            'created_by'            => $user->id,
            'created_at'            => $now->toDateTimeString(),
            'updated_by'            => $user->id,
            'updated_at'            => $now->toDateTimeString()
        ]);

        if($request->filled('machinery_id')){
            $prHeader->machinery_id = $request->input('machinery_id');
            $prHeader->save();
        }

        // Create purchase request detail
        $prDetail = PurchaseRequestDetail::create([
            'header_id'         => $prHeader->id,
            'item_id'           => 0,
            'quantity'          => 1,
            'quantity_invoiced' => 0,
            'remark'            => $mrHeader->material_request_details->first()->remark
        ]);

        // Check Approval Feature
//        $preference = PreferenceCompany::find(1);
//
//        try{
//            if($preference->approval_setting == 1) {
//                $approvals = ApprovalRule::where('document_id', 3)->get();
//                if($approvals->count() > 0){
//                    foreach($approvals as $approval){
//                        if(!empty($approval->user->email_address)){
//                            Mail::to($approval->user->email_address)->send(new ApprovalPurchaseRequestCreated($prHeader, $approval->user));
//                        }
//                    }
//                }
//            }
//        }
//        catch (\Exception $ex){
//            error_log($ex);
//        }

        try{
            // Send notification
            $mrCreator = $prHeader->material_request_header->createdBy;
            $mrCreator->notify(new PurchaseRequestCreated($prHeader, 'true'));

            $roleIds = [12,13];
            $roles = Role::whereIn('id', $roleIds)->get();
            foreach($roles as $role){
                $users =  $role->users()->get();
                if($users->count() > 0){
                    foreach ($users as $notifiedUser){
                        if($notifiedUser->id !== $mrCreator->id){
                            $notifiedUser->notify(new PurchaseRequestCreated($prHeader, 'false'));
                        }
                    }
                }
            }
        }
        catch(\Exception $ex){
            error_log($ex);
        }

        Session::flash('message', 'Berhasil membuat purchase request!');

        return redirect()->route('admin.purchase_requests.show', ['purchase_request' => $prHeader]);
    }

    public function edit(PurchaseRequestHeader $purchase_request){
        $header = $purchase_request;
        $departments = Department::all();
        $date = Carbon::parse($purchase_request->date)->format('d M Y');

        $data = [
            'header'        => $header,
            'departments'   => $departments,
            'date'          => $date
        ];

        return View('admin.purchasing.purchase_requests.edit')->with($data);
    }

    public function update(Request $request, PurchaseRequestHeader $purchase_request){
        $validator = Validator::make($request->all(),[
            'date'          => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = \Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');
        $limitDate = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');

        if($purchase_request->priority == '1'){
            $limitDate->addDays(8);
        }
        elseif($purchase_request->priority== '2'){
            $limitDate->addDays(15);
        }
        else{
            $limitDate->addDays(22);
        }

        $purchase_request->date = $date;
        $purchase_request->priority_limit_date = $limitDate->toDateTimeString();
        $purchase_request->updated_by = $user->id;
        $purchase_request->updated_at = $now->toDateTimeString();
        $purchase_request->save();

        // Check assignment
//        $assignmentPr = AssignmentPurchaseRequest::where('purchase_request_id', $prHeader->id)
//            ->where('status_id', 17)
//            ->first();
//
//        if(!empty($assignmentPr)){
//            $assignmentPr->status_id = 18;
//            $assignmentPr->processed_by = $user->id;
//            $assignmentPr->processed_date = $now->toDateTimeString();
//
//            if($user->id != $assignmentPr->assigned_user_id){
//                $assignmentPr->is_different_processor = 1;
//            }
//
//            $assignmentPr->save();
//        }

        // Update processed by for assignment
//        $prHeader->processed_by = $user->id;
//        $prHeader->save();

        Session::flash('message', 'Berhasil ubah purchase request!');

        return redirect()->route('admin.purchase_requests.show', ['purchase_request' => $purchase_request]);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function getIndex(Request $request){

        $mode = 'default';
        if($request->filled('mode')){
            $mode = $request->input('mode');
        }

        $purchaseRequests = null;
        $status = '0';
        if($request->filled('status')){
            $status = $request->input('status');
            if($status != '0'){
                $purchaseRequests = PurchaseRequestHeader::where('status_id', $status);
            }
            else{
                $purchaseRequests = PurchaseRequestHeader::whereIn('status_id', [3,4,11]);
            }
        }
        else{
            $purchaseRequests = PurchaseRequestHeader::whereIn('status_id', [3,4,11]);
        }

        if($mode === 'before_create_po'){
            $purchaseRequests = $purchaseRequests->where('status_id', 3)
                ->where('is_all_poed', 0);
        }

        return DataTables::of($purchaseRequests)
            ->setTransformer(new PurchaseRequestHeaderTransformer($mode))
            ->make(true);
    }

    public function getIndexWarning(Request $request){
        $prHeaders = new Collection();
        $tmpHeaders = PurchaseRequestHeader::where('status_id', 3)
            ->orderByDesc('date')
            ->get();
        foreach ($tmpHeaders as $header){
            if($header->material_request_header->status_id === 3){
                if($header->priority_expired){
                    $prHeaders->add($header);
                }
                else{
                    if($header->day_left <= 3){
                        $prHeaders->add($header);
                    }
                }
            }
        }

        return DataTables::of($prHeaders)
            ->setTransformer(new PurchaseRequestWarningTransformer)
            ->make(true);
    }

    public function getPurchaseRequests(Request $request){
        $term = trim($request->q);
        $purchase_requests = PurchaseRequestHeader::where('status_id', 3)
            ->where('code', 'LIKE', '%'. $term. '%')
            ->get();

        $formatted_tags = [];

        foreach ($purchase_requests as $purchase_request) {
            $formatted_tags[] = ['id' => $purchase_request->id, 'text' => $purchase_request->code];
        }

        return \Response::json($formatted_tags);
    }

    public function close(Request $request){
        try{
            $user = \Auth::user();
            $now = Carbon::now('Asia/Jakarta');

            $purchaseRequest = PurchaseRequestHeader::find($request->input('id'));
            $purchaseRequest->closed_by = $user->id;
            $purchaseRequest->closed_at = $now->toDateTimeString();
            $purchaseRequest->close_reason = $request->input('reason');
            $purchaseRequest->status_id = 11;
            $purchaseRequest->save();

            Session::flash('message', 'Berhasil tutup PR!');

            return Response::json(array('success' => 'VALID'));
        }
        catch(\Exception $ex){
            return Response::json(array('errors' => 'INVALID'));
        }
    }

    public function report(){
        $departments = Department::all();

        return View('admin.purchasing.purchase_requests.report', compact('departments'));
    }

    public function downloadReport(Request $request) {
        $validator = Validator::make($request->all(),[
            'start_date'        => 'required',
            'end_date'          => 'required',
        ],[
            'start_date.required'   => 'Dari Tanggal wajib diisi!',
            'end_date.required'     => 'Sampai Tanggal wajib diisi!',

        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $start = Carbon::createFromFormat('d M Y', $request->input('start_date'), 'Asia/Jakarta');
        $end = Carbon::createFromFormat('d M Y', $request->input('end_date'), 'Asia/Jakarta');

        // Validate date
        if($start->gt($end)){
            return redirect()->back()->withErrors('Dari Tanggal tidak boleh lebih besar dari Sampai Tanggal!', 'default')->withInput($request->all());
        }

        if($request->input('is_excel') === 'true'){
            $nowExcel = Carbon::now('Asia/Jakarta');
            $filenameExcel = 'PURCHASE_REQUEST_REPORT_' . $nowExcel->toDateTimeString(). '.xlsx';

            return (new PurchaseRequestExport(
                $start->toDateTimeString(),
                $end->toDateTimeString(),
                (int) $request->input('department'),
                (int) $request->input('status')
            ))->download($filenameExcel);
        }

        $start = $start->addDays(-1);
        $end = $end->addDays(1);

        $prHeaders = PurchaseRequestHeader::whereBetween('date', array($start->toDateTimeString(), $end->toDateTimeString()));

        // Filter departemen
        $filterDepartment = 'Semua';
        $department = $request->input('department');
        if($department != '0'){
            $prHeaders = $prHeaders->where('department_id', $department);
            $filterDepartment = Department::find($department)->name;
        }

        // Filter status
        $status = $request->input('status');
        $filterStatus = 'Semua';
        if($status != '0'){
            $prHeaders = $prHeaders->where('status_id', $status);
            $filterStatus = Status::find($status)->description;
        }

        $prHeaders = $prHeaders->orderByDesc('date')
                    ->get();

        // Validate Data
        if($prHeaders->count() == 0){
            return redirect()->back()->withErrors('Data tidak ditemukan!', 'default')->withInput($request->all());
        }

        if($request->input('is_preview') === 'false'){
            $data =[
                'prHeaders'         => $prHeaders,
                'start_date'        => $request->input('start_date'),
                'finish_date'       => $request->input('end_date'),
                'filterDepartment'  => $filterDepartment,
                'filterStatus'      => $filterStatus
            ];

            $now = Carbon::now('Asia/Jakarta');
            $filename = 'PURCHASE_REQUEST_REPORT_' . $now->toDateTimeString();

            $pdf = PDF3::loadView('documents.purchase_requests.purchase_requests_pdf', $data)
                ->setOption('footer-right', '[page] of [toPage]');

            return $pdf->download($filename.'.pdf');
        }
        else{
            $data =[
                'prHeaders'         => $prHeaders,
                'start_date'        => $request->input('start_date'),
                'finish_date'       => $request->input('end_date'),
                'filterDepartment'  => $filterDepartment,
                'filterStatus'      => $filterStatus,
                'status'            => $request->input('status'),
                'department'        => $request->input('department')
            ];

            return view('documents.purchase_requests.purchase_requests_pdf_preview')->with($data);
        }
    }

    public function download($id){
        $purchaseRequest = PurchaseRequestHeader::find($id);
        $purchaseRequestDetails = PurchaseRequestDetail::where('header_id', $purchaseRequest->id)->get();

        $pdf = PDF::loadView('documents.purchase_requests.purchase_requests_doc', ['purchaseRequest' => $purchaseRequest, 'purchaseRequestDetails' => $purchaseRequestDetails]);
        $now = Carbon::now('Asia/Jakarta');
        $filename = $purchaseRequest->code. '_' . $now->toDateTimeString();
        $pdf->set_option("isPhpEnabled", true);

        return $pdf->stream($filename.'.pdf');
    }

    public function printDocument($id){
        $purchaseRequest = PurchaseRequestHeader::find($id);
        $purchaseRequestDetails = PurchaseRequestDetail::where('header_id', $purchaseRequest->id)->get();
        $approvalUser = User::find(29);
//        $approvalUser = ApprovalRule::where('document_id', 3)->get();
//        $temp = PreferenceCompany::find(1);
//        $setting = $temp->approval_setting;

        $data = [
            'purchaseRequest'           => $purchaseRequest,
            'purchaseRequestDetails'    => $purchaseRequestDetails,
            'approvalUser'              => $approvalUser
        ];

        return view('documents.purchase_requests.purchase_requests_doc')->with($data);
    }
}