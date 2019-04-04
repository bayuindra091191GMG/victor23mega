<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PurchaseOrderMailToCreator;
use App\Mail\PurchaseOrderApprovedMailNotification;
use App\Models\ApprovalMaterialRequest;
use App\Models\ApprovalPaymentRequest;
use App\Models\ApprovalPurchaseOrder;
use App\Models\ApprovalPurchaseRequest;
use App\Models\ApprovalRule;
use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use App\Models\Document;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\MaterialRequestHeader;
use App\Models\PurchaseOrderHeader;
use App\Models\PurchaseRequestHeader;
use App\Notifications\MaterialRequestCreated;
use App\Notifications\PurchaseOrderCreated;
use App\Transformer\MasterData\ApprovalRuleTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;

class ApprovalRuleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('admin.approval_rules.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $users = User::where('status_id', 1)->orderBy('name')->get();
        $documents = Document::all();

        return view('admin.approval_rules.create', compact('users', 'documents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'user' => 'required',
            'document' => 'required',
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());

        //Checking
        if(Input::get('user') === '-1'){
            return redirect()->back()->withErrors('Pilih user!', 'default')->withInput($request->all());
        }
        if(Input::get('document') === '-1'){
            return redirect()->back()->withErrors('Pilih dokumen!', 'default')->withInput($request->all());
        }

        $user_id = $request->get('user');
        $document_id = $request->get('document');

        $rule = ApprovalRule::where('user_id', $user_id)->where('document_id', $document_id)->first();
        if($rule != null){
            Session::flash('error', 'Pengaturan Approval Sudah Dibuat!');
            return redirect(route('admin.approval_rules.create'));
        }

        $dateTimeNow = Carbon::now('Asia/Jakarta');
        $user = Auth::user();

        ApprovalRule::create([
            'user_id'           => $user_id,
            'document_id'       => $document_id,
            'created_by'        => $user->id,
            'created_at'        => $dateTimeNow->toDateTimeString(),
            'updated_by'        => $user->id,
            'updated_at'        => $dateTimeNow->toDateTimeString()
        ]);

        Session::flash('message', 'Berhasil membuat data pengaturan approval baru!');

        return redirect(route('admin.approval_rules'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ApprovalRule $approvalRule
     * @return \Illuminate\Http\Response
     */
    public function edit(ApprovalRule $approvalRule)
    {
        $users = User::where('status_id', 1)->orderBy('name')->get();
        $documents = Document::orderBy('description')->get();

        return view('admin.approval_rules.edit', compact('approvalRule', 'users', 'documents'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $validator = Validator::make($request->all(), [
            'user' => 'required',
            'document' => 'required',
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());
        $dateTimeNow = Carbon::now('Asia/Jakarta');
        $user = Auth::user();
        $user_id = $request->get('user');
        $document_id = $request->get('document');

        //Check
        $check = ApprovalRule::where('user_id', $user_id)->where('document_id', $document_id)->first();
        if($check != null){
            Session::flash('error', 'Pengaturan Approval Sudah Dibuat!');
            return redirect(route('admin.approval_rules.create'));
        }

        $rule = ApprovalRule::find($id);
        $rule->user_id = $user_id;
        $rule->document_id = $document_id;
        $rule->updated_by = $user->id;
        $rule->updated_at = $dateTimeNow->toDateTimeString();
        $rule->save();

        Session::flash('message', 'Sukses mengubah data Pengaturan Approval!');

        return redirect(route('admin.approval_rules.edit', ['approval_rules' => $rule->id]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy(Request $request)
    {
        try{
            $approvalRule = ApprovalRule::find($request->input('id'));
            $approvalRule->delete();

            Session::flash('message', 'Berhasil menghapus Approval Rule '. $approvalRule->user->name . ' - ' . $approvalRule->document->description);
            return Response::json(array('success' => 'VALID'));
        }
        catch(\Exception $ex){
            return Response::json(array('errors' => 'INVALID'));
        }
    }

    // Material Request Approval
    public function mrApproval(MaterialRequestHeader $material_request){
        $user = Auth::user();
        $header = $material_request;
        $date = Carbon::parse($header->date)->format('d M Y');

        $type = $header->type;
        if($type === 1){
            $routeUrl = route('admin.material_requests.other.show', ['material_request' => $header->id]);

            // Custom approval rule for Mr. Christ
            if($header->site_id ===  3  && ($header->department_id === 7 || $header->department_id === 4)){
                $docId = 18;
            }
            else{
                if($header->priority == 'Part - P1' || $header->priority == 'Part - P2' || $header->priority == 'Part - P3'){
                    $docId = 9;
                }
                else{
                    $docId = 14;
                }
            }
        }
        elseif($type === 2){
            $routeUrl = route('admin.material_requests.fuel.show', ['material_request' => $header->id]);

            // Custom approval rule for Mr. Christ
            if($header->site_id ===  3  && ($header->department_id === 7 || $header->department_id === 4)){
                $docId = 18;
            }
            else{
                if($header->priority == 'Part - P1' || $header->priority == 'Part - P2' || $header->priority == 'Part - P3'){
                    $docId = 10;
                }
                else{
                    $docId = 15;
                }
            }

        }
        elseif($type === 3){
            $routeUrl = route('admin.material_requests.oil.show', ['material_request' => $header->id]);

            // Custom approval rule for Mr. Christ
            if($header->site_id ===  3  && ($header->department_id === 7 || $header->department_id === 4)){
                $docId = 18;
            }
            else{
                if($header->priority == 'Part - P1' || $header->priority == 'Part - P2' || $header->priority == 'Part - P3'){
                    $docId = 11;
                }
                else{
                    $docId = 16;
                }
            }
        }
        else{
            $routeUrl = route('admin.material_requests.service.show', ['material_request' => $header->id]);

            // Custom approval rule for Mr. Christ
            if($header->site_id ===  3  && ($header->department_id === 7 || $header->department_id === 4)){
                $docId = 18;
            }
            else{
                if($header->priority == 'Part - P1' || $header->priority == 'Part - P2' || $header->priority == 'Part - P3'){
                    $docId = 12;
                }
                else{
                    $docId = 17;
                }
            }
        }

        // Get priority type
        if($header->priority === 'Part - P1' || $header->priority === 'Part - P2' || $header->priority === 'Part - P3'){
            $priorityApproval = 'PART';
        }
        else{
            $priorityApproval = 'NON-PART';
        }

        $approvalRules = ApprovalRule::where('document_id', $docId)->get();
        $approvalData = ApprovalMaterialRequest::where('material_request_id', $header->id)
            ->where('priority', $priorityApproval)
            ->where('user_id', $user->id)
            ->first();

        $status = 0;
        $approvalMrData = ApprovalMaterialRequest::where('material_request_id', $header->id)
            ->where('priority', $priorityApproval)
            ->get();

        if(!empty($approvalData) || $approvalMrData->count() > 0){
            $status = $approvalMrData->count();

            // Kondisi Semua sudah Approve
            if($approvalMrData->count() === $approvalRules->count()){
                $status = 99;
            }
        }

        $data = [
            'header'            => $header,
            'date'              => $date,
            'status'            => $status,
            'approvalData'      => $approvalMrData,
            'routeUrl'          => $routeUrl
        ];

        return View('admin.approval_rules.approval_mr')->with($data);
    }

    public function approveMr(MaterialRequestHeader $material_request){
        $header = $material_request;
        $type = $header->type;

        // Custom approval rule for Mr. Christ
        if($header->site_id ===  3  && ($header->department_id === 7 || $header->department_id === 4)){
            $docId = 18;
        }
        else{
            if($type === 1){
                if($header->priority == 'Part - P1' || $header->priority == 'Part - P2' || $header->priority == 'Part - P3'){
                    $docId = 9;
                }
                else{
                    $docId = 14;
                }
            }
            elseif($type === 2){
                if($header->priority == 'Part - P1' || $header->priority == 'Part - P2' || $header->priority == 'Part - P3'){
                    $docId = 10;
                }
                else{
                    $docId = 15;
                }
            }
            elseif($type === 3){
                if($header->priority == 'Part - P1' || $header->priority == 'Part - P2' || $header->priority == 'Part - P3'){
                    $docId = 11;
                }
                else{
                    $docId = 16;
                }
            }
            else{
                if($header->priority == 'Part - P1' || $header->priority == 'Part - P2' || $header->priority == 'Part - P3'){
                    $docId = 12;
                }
                else{
                    $docId = 17;
                }
            }
        }

        $approvalRules = ApprovalRule::where('document_id', $docId)->get();
        $count = $approvalRules->count();

        // Create Approval
        $dateTimeNow = Carbon::now('Asia/Jakarta');
        $user = Auth::user();

        // Get priority type
        if($header->priority === 'Part - P1' || $header->priority === 'Part - P2' || $header->priority === 'Part - P3'){
            $priorityApproval = 'PART';
        }
        else{
            $priorityApproval = 'NON-PART';
        }

        $isExist = false;
        $approvalData = ApprovalMaterialRequest::where('material_request_id', $header->id)
            ->where('priority', $priorityApproval)
            ->where('user_id', $user->id)
            ->first();

        if(!empty($approvalData)){
            $isExist = true;
        }

        $isValid = false;
        foreach ($approvalRules as $rule){
            if($user->id === $rule->user_id){
                $isValid = true;
            }
        }

        if(!$isValid){
            return redirect()->back()->withErrors('Anda tidak berhak melakukan approval dokumen ini!', 'default');
        }

        if($isExist){
            return redirect()->back()->withErrors('Anda sudah melakukan approval dokumen ini!', 'default');
        }

        ApprovalMaterialRequest::create([
            'material_request_id'   => $header->id,
            'user_id'               => $user->id,
            'status_id'             => 12,
            'created_at'            => $dateTimeNow->toDateTimeString(),
            'updated_at'            => $dateTimeNow->toDateTimeString(),
            'created_by'            => $user->id,
            'updated_by'            => $user->id,
            'priority'              => $priorityApproval
        ]);

        // Update Document Status
        $approvalCount = ApprovalMaterialRequest::where('material_request_id', $header->id)->get()->count();
        if($approvalCount === $count){
            $header->is_approved = 1;
            $header->approved_date = $dateTimeNow->toDateTimeString();
            $header->save();

            // Check stock
            $isInStock = true;
            foreach($header->material_request_details as $detail){
                $item = Item::find($detail->item_id);
                if($item->stock < $detail->quantity){
                    $isInStock = false;
                }
            }

            // Send notification
            $mrCreator = $header->createdBy;
            $mrCreator->notify(new MaterialRequestCreated($header, false, 'true'));

            $roleIds = [4,5,12];

            $roles = Role::whereIn('id', $roleIds)->get();
            foreach($roles as $role){
                $users =  $role->users()->get();
                if($users->count() > 0){
                    foreach ($users as $notifiedUser){
                        $notifiedUser->notify(new MaterialRequestCreated($header, $isInStock, 'false'));
                    }
                }
            }
        }

        Session::flash('message', 'Berhasil Approve Dokumen ini!');

        return redirect()->route('admin.approval_rules.mr_approval', ['material_request' => $header]);
    }

    public function rejectMr(Request $request, MaterialRequestHeader $material_request){
        $header = $material_request;
        $type = $header->type;

        // Custom approval rule for Mr. Christ
        if($header->site_id ===  3  && ($header->department_id === 7 || $header->department_id === 4)){
            $docId = 18;
        }
        else{
            if($type === 1){
                if($header->priority == 'Part - P1' || $header->priority == 'Part - P2' || $header->priority == 'Part - P3'){
                    $docId = 9;
                }
                else{
                    $docId = 14;
                }
            }
            elseif($type === 2){
                if($header->priority == 'Part - P1' || $header->priority == 'Part - P2' || $header->priority == 'Part - P3'){
                    $docId = 10;
                }
                else{
                    $docId = 15;
                }
            }
            elseif($type === 3){
                if($header->priority == 'Part - P1' || $header->priority == 'Part - P2' || $header->priority == 'Part - P3'){
                    $docId = 11;
                }
                else{
                    $docId = 16;
                }
            }
            else{
                if($header->priority == 'Part - P1' || $header->priority == 'Part - P2' || $header->priority == 'Part - P3'){
                    $docId = 12;
                }
                else{
                    $docId = 17;
                }
            }
        }

        $datas = ApprovalRule::where('document_id', $docId)->get();
        $count = $datas->count();

        $dateTimeNow = Carbon::now('Asia/Jakarta');
        $user = Auth::user();

        // Get priority type
        if($header->priority === 'Part - P1' || $header->priority === 'Part - P2' || $header->priority === 'Part - P3'){
            $priorityApproval = 'PART';
        }
        else{
            $priorityApproval = 'NON-PART';
        }

        $isExist = false;
        $approvalData = ApprovalMaterialRequest::where('material_request_id', $header->id)
            ->where('priority', $priorityApproval)
            ->where('user_id', $user->id)
            ->first();

        if(!empty($approvalData)){
            $isExist = true;
        }

        foreach ($datas as $data){
            if($user->id === $data->user_id){
                $isValid = true;
            }
        }

        if(!$isValid){
            return redirect()->back()->withErrors('Anda tidak berhak melakukan approval dokumen ini!', 'default');
        }

        if($isExist){
            return redirect()->back()->withErrors('Anda sudah melakukan approval dokumen ini!', 'default');
        }

        ApprovalMaterialRequest::create([
            'material_request_id'   => $header->id,
            'user_id'               => $user->id,
            'status_id'             => 13,
            'created_at'            => $dateTimeNow->toDateTimeString(),
            'updated_at'            => $dateTimeNow->toDateTimeString(),
            'created_by'            => $user->id,
            'updated_by'            => $user->id
        ]);

        // Update MR
        $header->status_id = 13;
        $header->reject_reason = $request->input('reject_reason');
        $header->rejected_date = $dateTimeNow->toDateTimeString();
        $header->save();

        // Send notification
        $mrCreator = $header->createdBy;
        $mrCreator->notify(new MaterialRequestCreated($header, false, 'true'));

        $roleIds = [4,5,12];

        $roles = Role::whereIn('id', $roleIds)->get();
        foreach($roles as $role){
            $users =  $role->users()->get();
            if($users->count() > 0){
                foreach ($users as $notifiedUser){
                    $notifiedUser->notify(new MaterialRequestCreated($header, false, 'false'));
                }
            }
        }

        Session::flash('message', 'Berhasil Menolak Dokumen ini!');

        return redirect()->route('admin.approval_rules.mr_approval', ['material_request' => $header]);
    }

    // Purchase Request Approval
    public function prApproval($approval_rule){
        $user = Auth::user();
        $header = PurchaseRequestHeader::find($approval_rule);
        $date = Carbon::parse($header->date)->format('d M Y');
        $priorityLimitDate = Carbon::parse($header->priority_limit_date)->format('d M Y');

        // Check kondisi Approval
        $approvals = ApprovalRule::where('document_id', 3)->get();
        $approvalData = ApprovalPurchaseRequest::where('purchase_request_id', $header->id)->where('user_id', $user->id)->first();
        $status = 0;
        $approvalPrData = ApprovalPurchaseRequest::where('purchase_request_id', $header->id)->get();
        if($approvalData != null || $approvalPrData != null){
            $status = $approvalPrData->count();

            //Kondisi Semua sudah Approve
            if($approvalPrData->count() == $approvals->count()){
                $status = 99;
            }
        }

        $approvalRule = ApprovalRule::where('document_id', '3')->get();

        $data = [
            'header'            => $header,
            'date'              => $date,
            'priorityLimitDate' => $priorityLimitDate,
            'status'            => $status,
            'approvalData'      => $approvalRule
        ];

        return View('admin.approval_rules.approval_pr')->with($data);
    }

    public function approvePr($id){
        $datas = ApprovalRule::where('document_id', 3)->get();
        $count = $datas->count();

        //Create Approval
        $dateTimeNow = Carbon::now('Asia/Jakarta');
        $user = Auth::user();

        $valid = false;
        $exist = true;
        $approvalData = ApprovalPurchaseRequest::where('purchase_request_id', $id)->where('user_id', $user->id)->first();
        if($approvalData != null){
            $exist = false;
        }

        foreach ($datas as $data){
            if($user->id == $data->user_id){
                $valid = true;
            }
        }

        if(!$valid){
            return redirect()->back()->withErrors('Anda tidak berhak melakukan approval dokumen ini!', 'default');
        }
        if(!$exist){
            return redirect()->back()->withErrors('Anda sudah melakukan approval dokumen ini!', 'default');
        }

        ApprovalPurchaseRequest::create([
            'purchase_request_id'   => $id,
            'user_id'               => $user->id,
            'status_id'             => 12,
            'created_at'            => $dateTimeNow->toDateTimeString(),
            'updated_at'            => $dateTimeNow->toDateTimeString(),
            'created_by'            => $user->id,
            'updated_by'            => $user->id
        ]);

        // Update Document Status
        $approvalCount = ApprovalPurchaseRequest::where('purchase_request_id', $id)->get()->count();
        if($approvalCount == $count){
            $purchaseRequest = PurchaseRequestHeader::find($id);
            $purchaseRequest->is_approved = 1;
            $purchaseRequest->save();
        }

        Session::flash('message', 'Berhasil Approve Dokumen ini!');

        return redirect()->route('admin.approval_rules.pr_approval', ['approval_rule' => $id]);
    }

    // Purchase Order Approval
    public function poApproval($approval_rule){
        $user = Auth::user();
        $header = PurchaseOrderHeader::find($approval_rule);
        $date = Carbon::parse($header->date)->format('d M Y');
        $priorityLimitDate = Carbon::parse($header->priority_limit_date)->format('d M Y');

        // Check kondisi Approval
        $approvals = ApprovalRule::where('document_id', 4)->get();
        $approvalData = ApprovalPurchaseOrder::where('purchase_order_id', $header->id)->where('user_id', $user->id)->first();
        $status = 0;
        $approvalPoData = ApprovalPurchaseOrder::where('purchase_order_id', $header->id)->get();
        if($approvalData != null || $approvalPoData != null){
            $status = $approvalPoData->count();

            // Kondisi Semua sudah Approve
            if($approvalPoData->count() == $approvals->count()){
                $status = 99;
            }
        }

//        $approvalRule = ApprovalRule::where('document_id', '4')->get();

        $data = [
            'header'            => $header,
            'date'              => $date,
            'priorityLimitDate' => $priorityLimitDate,
            'status'            => $status,
            'approvalData'      => $approvalPoData
        ];

        return View('admin.approval_rules.approval_po')->with($data);
    }

    public function approvePo($id){
        $dateTimeNow = Carbon::now('Asia/Jakarta');
        $user = Auth::user();

        $isAlreadyApproved = false;
        $approvalData = ApprovalPurchaseOrder::where('purchase_order_id', $id)->where('user_id', $user->id)->first();
        if(!empty($approvalData)){
            $isAlreadyApproved = true;
        }

        if($isAlreadyApproved){
            return redirect()->back()->withErrors('Anda sudah melakukan approval dokumen ini!', 'default');
        }

        $isValid = false;
        $approvalRules = ApprovalRule::where('document_id', 4)->get();
        foreach ($approvalRules as $rule){
            if($user->id === $rule->user_id){
                $isValid = true;
            }
        }

        if(!$isValid){
            return redirect()->back()->withErrors('Anda tidak berhak melakukan approval dokumen ini!', 'default');
        }

        $approvalPoUser = ApprovalPurchaseOrder::where('user_id', $user->id)
                            ->where('purchase_order_id', $id)
                            ->where('status_id', 12)
                            ->first();

        if(empty($approvalPoUser)){
            ApprovalPurchaseOrder::create([
                'purchase_order_id'   => $id,
                'user_id'             => $user->id,
                'status_id'           => 12,
                'created_at'          => $dateTimeNow->toDateTimeString(),
                'updated_at'          => $dateTimeNow->toDateTimeString(),
                'created_by'          => $user->id,
                'updated_by'          => $user->id
            ]);
        }

        // Check kondisi Approval
        $environment = env('APP_ENV','local');
        $approvalPoData = ApprovalPurchaseOrder::where('purchase_order_id', $id)->get();
        if(!empty($approvalData) || $approvalPoData->count() > 0){

            // Kondisi Semua sudah Approve
            if($approvalPoData->count() === $approvalRules->count()){

                $header = PurchaseOrderHeader::find($id);

                // Update PO approval status
                if($approvalPoData->count() === $approvalRules->count()){
                    $header->is_approved = 1;
                    $header->approved_date = $dateTimeNow->toDateTimeString();
                    $header->save();

                    // Update Item_Stock
                    foreach ($header->purchase_order_details as $detail){
                        $itemDb = Item::find($detail->item_id);
                        $itemDb->stock_on_order += $detail->quantity;
                        $itemDb->save();

                        $itemStock = ItemStock::where('item_id', $detail->item_id)->where('warehouse_id', $header->warehouse_id)->first();
                        $itemStock->stock_on_order += $detail->quantity;
                        $itemStock->save();
                    }
                }

                try{
                    Mail::to($header->createdBy->email_address)->send(new PurchaseOrderMailToCreator($header, $header->createdBy));

                    if($environment === 'prod'){
                        $logisticUser1 = User::find(26);
                        $logisticUser2 = User::find(28);
                        Mail::to($logisticUser1->email_address)->send(new PurchaseOrderApprovedMailNotification($header, $header->createdBy));
                        Mail::to($logisticUser2->email_address)->send(new PurchaseOrderApprovedMailNotification($header, $header->createdBy));

                        $purchasingUser1 = User::find(27);
                        Mail::to($purchasingUser1->email_address)->send(new PurchaseOrderApprovedMailNotification($header, $header->createdBy));

                        $purchasingUser2 = User::find(25);
                        Mail::to($purchasingUser2->email_address)->send(new PurchaseOrderApprovedMailNotification($header, $header->createdBy));

                        $purchasingUser3 = User::find(47);
                        Mail::to($purchasingUser3->email_address)->send(new PurchaseOrderApprovedMailNotification($header, $header->createdBy));

                        $purchasingUser4 = User::find(16);
                        Mail::to($purchasingUser4->email_address)->send(new PurchaseOrderApprovedMailNotification($header, $header->createdBy));

                        $purchasingUser5 = User::find(40);
                        Mail::to($purchasingUser5->email_address)->send(new PurchaseOrderApprovedMailNotification($header, $header->createdBy));
                    }
                }
                catch(\Exception $ex){
                    Log::error('ApprovalRuleController - approvePo : '. $ex);
                }

                // Send web notification to PO creator
                $poCreator = $header->createdBy;
                $poCreator->notify(new PurchaseOrderCreated($header, 'false', 'true'));

                // Send web notification to related MR creator
                $mrCreator = $header->purchase_request_header->material_request_header->createdBy;
                $mrCreator->notify(new PurchaseOrderCreated($header, 'true', 'false'));

                $roles = Role::where('id', 13)->get();
                foreach($roles as $role){
                    $users =  $role->users()->get();
                    if($users->count() > 0){
                        foreach ($users as $notifiedUser){
                            if($notifiedUser->id !== $mrCreator->id){
                                $notifiedUser->notify(new PurchaseOrderCreated($header, 'false', 'false'));
                            }
                        }
                    }
                }
            }
        }

        Session::flash('message', 'Berhasil Approve Dokumen ini!');

        return redirect()->route('admin.approval_rules.po_approval', ['approval_rule' => $id]);
    }

    public function rejectPo($id){
        $dateTimeNow = Carbon::now('Asia/Jakarta');
        $user = Auth::user();
        $purchaseOrder = PurchaseOrderHeader::find($id);

        if($purchaseOrder->status_id !== 3){
            return redirect()->back()->withErrors('Dokumen PO sudah di-approve atau reject!', 'default');
        }

        $isExist = false;
        $approvalData = ApprovalPurchaseOrder::where('purchase_order_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if(!empty($approvalData)){
            $isExist = true;
        }

        $approvalRule = ApprovalRule::where('document_id', 4)
            ->where('user_id', $user->id)
            ->first();

        $isApprover = false;
        if(!empty($approvalRule)){
            $isApprover = true;
        }

        if(!$isApprover){
            return redirect()->back()->withErrors('Anda tidak berhak melakukan approval dokumen ini!', 'default');
        }

        if($isExist){
            return redirect()->back()->withErrors('Anda sudah melakukan approval dokumen ini!', 'default');
        }

        ApprovalPurchaseOrder::create([
            'purchase_order_id'   => $id,
            'user_id'             => $user->id,
            'status_id'           => 13,
            'created_at'          => $dateTimeNow->toDateTimeString(),
            'updated_at'          => $dateTimeNow->toDateTimeString(),
            'created_by'          => $user->id,
            'updated_by'          => $user->id
        ]);

        // Update status
        $purchaseOrder->status_id = 13;
        $purchaseOrder->save();

        // Undo reorder process
        if($purchaseOrder->is_reorder === 1){
            foreach ($purchaseOrder->purchase_order_details as $detail){
                $itemStock = ItemStock::where('warehouse_id', $purchaseOrder->warehouse_id)
                    ->where('item_id', $detail->item_id)
                    ->first();
                $itemStock->stock_on_reorder -= $detail->quantity;
                $itemStock->save();
            }
        }

        Session::flash('message', 'Berhasil Tolak Dokumen ini!');

        return redirect()->route('admin.approval_rules.po_approval', ['approval_rule' => $id]);
    }

    public function getIndex()
    {
        $approvalRules = ApprovalRule::all();
        return DataTables::of($approvalRules)
            ->setTransformer(new ApprovalRuleTransformer())
            ->addIndexColumn()
            ->make(true);
    }
}

