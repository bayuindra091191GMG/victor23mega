<?php
/**
 * Created by PhpStorm.
 * User: hellb
 * Date: 3/20/2018
 * Time: 11:22 AM
 */

namespace App\Http\Controllers\Admin\Inventory;


use App\Http\Controllers\Controller;
use App\Libs\Utilities;
use App\Mail\ApprovalMaterialRequestCreated;
use App\Models\ApprovalMaterialRequest;
use App\Models\ApprovalRule;
use App\Models\Auth\Role\Role;
use App\Models\AutoNumber;
use App\Models\Department;
use App\Models\Document;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\Machinery;
use App\Models\MaterialRequestDetail;
use App\Models\MaterialRequestHeader;
use App\Models\NumberingSystem;
use App\Models\PermissionMenu;
use App\Models\PreferenceCompany;
use App\Models\PurchaseRequestHeader;
use App\Models\Status;
use App\Models\Warehouse;
use App\Notifications\MaterialRequestCreated;
use App\Transformer\Inventory\MaterialRequestHeaderTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade as PDF;
use PDF3;

class MaterialRequestHeaderController extends Controller
{
    public function indexOther(Request $request){

        $filterStatus = '3';
        if($request->status != null){
            $filterStatus = $request->status;
        }

        return View('admin.inventory.material_requests.other.index', compact('filterStatus'));
    }

    public function indexFuel(Request $request){
        $filterStatus = '3';
        if($request->status != null){
            $filterStatus = $request->status;
        }

        return View('admin.inventory.material_requests.fuel.index', compact('filterStatus'));
    }

    public function indexOil(Request $request){
        $filterStatus = '3';
        if($request->status != null){
            $filterStatus = $request->status;
        }

        return View('admin.inventory.material_requests.oil.index', compact('filterStatus'));
    }

    public function indexService(Request $request){
        $filterStatus = '3';
        if($request->status != null){
            $filterStatus = $request->status;
        }

        return View('admin.inventory.material_requests.service.index', compact('filterStatus'));
    }

    public function createOther(){
        $departments = Department::all();

        // Numbering System
        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        //$sysNo = NumberingSystem::where('doc_id', '9')->first();
        $mrPrepend = 'MR/'. $now->year. '/'. $now->month;
        $sysNo = Utilities::GetNextAutoNumber($mrPrepend);

        $docCode = 'MR-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumber($docCode, $sysNo);

        $warehouses = Warehouse::where('id', '!=', 0)->orderBy('name')->get();
        $userWarehouseId = $user->employee->site->warehouses->first()->id;

        $data = [
            'departments'           => $departments,
            'autoNumber'            => $autoNumber,
            'warehouses'            => $warehouses,
            'userWarehouseId'       => $userWarehouseId
        ];

        return View('admin.inventory.material_requests.other.create')->with($data);
    }

    public function createFuel(){
        $departments = Department::all();

        // Numbering System
        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        //$sysNo = NumberingSystem::where('doc_id', '10')->first();
        $mrPrepend = 'MR-F/'. $now->year. '/'. $now->month;
        $sysNo = Utilities::GetNextAutoNumber($mrPrepend);

        $docCode = 'MR-F-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumber($docCode, $sysNo);

        $warehouses = Warehouse::where('id', '!=', 0)->orderBy('name')->get();
        $userWarehouseId = $user->employee->site->warehouses->first()->id;

        $data = [
            'departments'           => $departments,
            'autoNumber'            => $autoNumber,
            'warehouses'            => $warehouses,
            'userWarehouseId'       => $userWarehouseId
        ];

        return View('admin.inventory.material_requests.fuel.create')->with($data);
    }

    public function createOil(){
        $departments = Department::all();

        // Numbering System
        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        //$sysNo = NumberingSystem::where('doc_id', '11')->first();
        $mrPrepend = 'MR-O/'. $now->year. '/'. $now->month;
        $sysNo = Utilities::GetNextAutoNumber($mrPrepend);

        $docCode = 'MR-O-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumber($docCode, $sysNo);

        $warehouses = Warehouse::where('id', '!=', 0)->orderBy('name')->get();
        $userWarehouseId = $user->employee->site->warehouses->first()->id;

        $data = [
            'departments'           => $departments,
            'autoNumber'            => $autoNumber,
            'warehouses'            => $warehouses,
            'userWarehouseId'       => $userWarehouseId
        ];

        return View('admin.inventory.material_requests.oil.create')->with($data);
    }

    public function createService(){
        $departments = Department::all();

        // Numbering System
        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        //$sysNo = NumberingSystem::where('doc_id', '12')->first();
        $mrPrepend = 'MR-S/'. $now->year. '/'. $now->month;
        $sysNo = Utilities::GetNextAutoNumber($mrPrepend);

        $docCode = 'MR-S-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumber($docCode, $sysNo);

        $data = [
            'departments'   => $departments,
            'autoNumber'    => $autoNumber
        ];

        return View('admin.inventory.material_requests.service.create')->with($data);
    }

    public function showOther(MaterialRequestHeader $material_request){
        try
        {
            $header = $material_request;
            $date = Carbon::parse($material_request->date)->format('d M Y');

            $itemStocks = new Collection();

            if($header->status_id == 3 && $header->type === 'non-stock'){
                // Check stock
                $isInStock = true;
                foreach($header->material_request_details as $detail){
                    if($detail->item->stock < $detail->quantity){
                        $isInStock = false;
                    }
                }

                // Get stock
                if($isInStock){
                    foreach($header->material_request_details as $detail){
                        $stocks = ItemStock::where('item_id', $detail->item_id)
                            ->where('stock', '>', 0)
                            ->get();
                        foreach($stocks as $stock){
                            $itemStocks->add($stock);
                        }
                    }
                }
            }

            $isPrCreated = false;
            if(PurchaseRequestHeader::where('material_request_id', $header->id)->exists()){
                $isPrCreated = true;
            }

            // Check Approval & Permission to Print
            $user = \Auth::user();
            $roleId = $user->roles->pluck('id')[0];
            $isApproved = $header->is_approved === 1 ? true: false;
            $approveMr = false;
            // Kondisi belum diapprove
            $status = 0;

            // Check feedback authorized
            $isFeedbackAuthorized = false;
            if($user->id === $header->created_by || $roleId === 1 || $roleId === 3 ||
                $roleId === 4 || $roleId === 9 || $roleId || $roleId === 14 ||
                $roleId === 15 || $roleId === 18 || $roleId === 20){
                $isFeedbackAuthorized = true;
            }

            // All Approval Settings checked if On Or Not
            $setting = PreferenceCompany::find(1);
            $approvalRules = null;
            $arrData = array();

            // Custom approval rule for Mr. Christ
            if($header->site_id ===  3  && ($header->department_id === 7 || $header->department_id === 4)){
                $docId = 18;
            }
            else{
                // Get priority type
                if($header->priority === 'Part - P1' || $header->priority === 'Part - P2' || $header->priority === 'Part - P3'){
                    $docId = 9;
                }
                else{
                    $docId = 14;
                }
            }

            if($setting->approval_setting == 1) {
                $tempApprove = ApprovalRule::where('document_id', $docId)->where('user_id', $user->id)->get();
                $approvalRules = ApprovalRule::where('document_id', $docId)->get();

                if ($tempApprove->count() != 0) {
                    $approveMr = true;
                }

                $approverData = ApprovalMaterialRequest::where('material_request_id', $header->id)
                    ->where('user_id', $user->id)
                    ->first();

                if(!empty($approverData) && $header->is_approved === 1){
                    $approveMr = false;
                }

                // Kondisi Approve Sebagian
                $approverDatas = ApprovalMaterialRequest::where('material_request_id', $header->id)
                    ->get();

                if(!empty($approverData) || $approverDatas->count() > 0){
                    $status = $approverDatas->count();

                    // Kondisi Semua Sudah Approve
                    if($header->is_approved === 1){
                        $status = 99;
                    }
                }

                if($header->is_approved === 1){
                    foreach($approverDatas as $approver)
                    {
                        if($approver->status_id === 12){
                            $arrData[] = "<span style='color: green;'>". $approver->user->name . " - Approve</span>";
                        }
                        else{
                            $arrData[] = "<span style='color: red;'>". $approver->user->name . " - Tolak</span>";

                            // Kondisi ada yang reject
                            $status = 101;
                        }
                    }
                }
                else{
                    foreach($approvalRules as $rule)
                    {
                        $flag = 0;
                        foreach($approverDatas as $approver)
                        {
                            if($approver->user_id == $rule->user_id)
                            {
                                if($approver->status_id === 12){
                                    $flag = 1;
                                }
                                else{
                                    $flag = 2;
                                    // Kondisi ada yang reject
                                    $status = 101;
                                }
                            }
                        }

                        if($flag == 1){
                            $arrData[] = "<span style='color: green;'>". $rule->user->name . " - Approve</span>";
                        }
                        elseif($flag == 2){
                            $arrData[] = "<span style='color: red;'>". $rule->user->name . " - Tolak</span>";
                        }
                        else{
                            $arrData[] = "<span style='color: #f4bf42;'>". $rule->user->name . " - Belum Approve</span>";
                        }
                    }
                }
            }

            // MR Tracking
            $isTrackingAvailable = true;
            if($header->purchase_request_headers->count() === 0){
                $isTrackingAvailable = false;
            }

            if($isTrackingAvailable){
                $trackedPrHeader = $header->purchase_request_headers->first();
                if(!empty($trackedPrHeader)){
                    $trackedPoHeaders = $trackedPrHeader->purchase_order_headers;
                    $trackedGrHeaders = new Collection();
                    foreach($trackedPoHeaders as $poHeader){
                        foreach($poHeader->item_receipt_headers as $grHeader){
                            $trackedGrHeaders->add($grHeader);
                        }
                    }

                    $trackedSjHeaders = new Collection();
                    foreach($trackedGrHeaders as $trackedGrHeader){
                        foreach ($trackedGrHeader->delivery_order_headers as $sjHeader){
                            $trackedSjHeaders->add($sjHeader);
                        }
                    }

                    $trackedPiHeaders = new Collection();
                    foreach($trackedPoHeaders as $poHeader){
                        foreach($poHeader->purchase_invoice_headers as $piHeader){
                            $trackedPiHeaders->add($piHeader);
                        }
                    }
                }

            }

//            $trackedIdHeaders = $header->issued_docket_headers;

            // Check menu permission
            $isAuthorized = false;
            $roleId = $user->roles->pluck('id')[0];
            if(PermissionMenu::where('role_id', $roleId)->where('menu_id', 23)->first()){
                $isAuthorized = true;
            }

            $data = [
                'header'                    => $header,
                'date'                      => $date,
                'itemStocks'                => $itemStocks,
                'isPrCreated'               => $isPrCreated,
                'isApproved'                => $isApproved,
                'approveMr'                 => $approveMr,
                'status'                    => $status,
                'approvalData'              => $arrData,
                'setting'                   => $setting->approval_setting,
                'isTrackingAvailable'       => $isTrackingAvailable,
                'trackedPrHeader'           => $trackedPrHeader ?? null,
                'trackedPoHeaders'          => $trackedPoHeaders ?? null,
                'trackedGrHeaders'          => $trackedGrHeaders ?? null,
                'trackedSjHeaders'          => $trackedSjHeaders ?? null,
                'trackedPiHeaders'          => $trackedPiHeaders ?? null,
//                'trackedIdHeaders'          => $trackedIdHeaders ?? null,
                'isAuthorized'              => $isAuthorized,
                'isFeedbackAuthorized'      => $isFeedbackAuthorized
            ];

            return View('admin.inventory.material_requests.other.show')->with($data);
        }catch (\Exception $ex){
            Log::error("MaterialRequestHeaderController showOther ". $header->code. " EX: ". $ex);
            dd("SYSTEM ERROR! PLEASE CONTACT ADMINISTRATOR!");
        }
    }

    public function showFuel(MaterialRequestHeader $material_request){
        try{
            $header = $material_request;
            $date = Carbon::parse($material_request->date)->format('d M Y');

            $itemStocks = new Collection();

            if($header->status_id === 3 && $header->type === 'non-stock'){
                // Check stock
                $isInStock = true;
                foreach($header->material_request_details as $detail){
                    if($detail->item->stock < $detail->quantity){
                        $isInStock = false;
                    }
                }

                // Get stock
                if($isInStock){
                    foreach($header->material_request_details as $detail){
                        $stocks = ItemStock::where('item_id', $detail->item_id)
                            ->where('stock', '>', 0)
                            ->get();
                        foreach($stocks as $stock){
                            $itemStocks->add($stock);
                        }
                    }
                }
            }

            $isPrCreated = false;
            if(PurchaseRequestHeader::where('material_request_id', $header->id)->exists()){
                $isPrCreated = true;
            }

            // Check Approval & Permission to Print
            $user = \Auth::user();
            $roleId = $user->roles->pluck('id')[0];
            $isApproved = $header->is_approved === 1 ? true: false;
            $approveMr = false;
            // Kondisi belum diapprove
            $status = 0;

            // Check feedback authorized
            $isFeedbackAuthorized = false;
            if($user->id === $header->created_by || $roleId === 1 || $roleId === 3 ||
                $roleId === 4 || $roleId === 9 || $roleId || $roleId === 14 ||
                $roleId === 15 || $roleId === 18 || $roleId === 20){
                $isFeedbackAuthorized = true;
            }

            // All Approval Settings checked if On Or Not
            $setting = PreferenceCompany::find(1);
            $approvals = null;
            $arrData = array();

            // Custom approval rule for Mr. Christ
            if($header->site_id ===  3  && ($header->department_id === 7 || $header->department_id === 4)){
                $docId = 18;
            }
            else{
                // Get priority type
                if($header->priority === 'Part - P1' || $header->priority === 'Part - P2' || $header->priority === 'Part - P3'){
                    $docId = 10;
                }
                else{
                    $docId = 15;
                }
            }

            if($setting->approval_setting == 1) {
                $tempApprove = ApprovalRule::where('document_id', $docId)->where('user_id', $user->id)->get();
                $approvalRules = ApprovalRule::where('document_id', $docId)->get();

                if ($tempApprove->count() != 0) {
                    $approveMr = true;
                }

                $approverData = ApprovalMaterialRequest::where('material_request_id', $header->id)
                    ->where('user_id', $user->id)
                    ->first();

                if(!empty($approverData) && $header->is_approved === 1){
                    $approveMr = false;
                }

                // Kondisi Approve Sebagian
                $approverDatas = ApprovalMaterialRequest::where('material_request_id', $header->id)
                    ->get();

                if(!empty($approverData) || $approverDatas->count() > 0){
                    $status = $approverDatas->count();

                    // Kondisi Semua Sudah Approve
                    if($header->is_approved === 1){
                        $status = 99;
                    }
                }

                if($header->is_approved === 1){
                    foreach($approverDatas as $approver)
                    {
                        if($approver->status_id === 12){
                            $arrData[] = "<span style='color: green;'>". $approver->user->name . " - Approve</span>";
                        }
                        else{
                            $arrData[] = "<span style='color: red;'>". $approver->user->name . " - Tolak</span>";

                            // Kondisi ada yang reject
                            $status = 101;
                        }
                    }
                }
                else{
                    foreach($approvalRules as $rule)
                    {
                        $flag = 0;
                        foreach($approverDatas as $approver)
                        {
                            if($approver->user_id == $rule->user_id)
                            {
                                if($approver->status_id === 12){
                                    $flag = 1;
                                }
                                else{
                                    $flag = 2;
                                    // Kondisi ada yang reject
                                    $status = 101;
                                }
                            }
                        }

                        if($flag == 1){
                            $arrData[] = "<span style='color: green;'>". $rule->user->name . " - Approve</span>";
                        }
                        elseif($flag == 2){
                            $arrData[] = "<span style='color: red;'>". $rule->user->name . " - Tolak</span>";
                        }
                        else{
                            $arrData[] = "<span style='color: #f4bf42;'>". $rule->user->name . " - Belum Approve</span>";
                        }
                    }
                }
            }

            // MR Tracking
            $isTrackingAvailable = true;
            if($header->purchase_request_headers->count() === 0){
                $isTrackingAvailable = false;
            }

            if($isTrackingAvailable){
                $trackedPrHeader = $header->purchase_request_headers->first();
                $trackedPoHeaders = $trackedPrHeader->purchase_order_headers;
                $trackedGrHeaders = new Collection();
                foreach($trackedPoHeaders as $poHeader){
                    foreach($poHeader->item_receipt_headers as $grHeader){
                        $trackedGrHeaders->add($grHeader);
                    }
                }

                $trackedSjHeaders = new Collection();
                foreach($trackedGrHeaders as $trackedGrHeader){
                    foreach ($trackedGrHeader->delivery_order_headers as $sjHeader){
                        $trackedSjHeaders->add($sjHeader);
                    }
                }

                $trackedPiHeaders = new Collection();
                foreach($trackedPoHeaders as $poHeader){
                    foreach($poHeader->purchase_invoice_headers as $piHeader){
                        $trackedPiHeaders->add($piHeader);
                    }
                }
            }

//            $trackedIdHeaders = $header->issued_docket_headers;

            // Check menu permission
            $isAuthorized = false;
            $roleId = $user->roles->pluck('id')[0];
            if(PermissionMenu::where('role_id', $roleId)->where('menu_id', 23)->first()){
                $isAuthorized = true;
            }

            $data = [
                'header'            => $header,
                'date'              => $date,
                'itemStocks'        => $itemStocks,
                'isPrCreated'       => $isPrCreated,
                'isApproved'        => $isApproved,
                'approveMr'         => $approveMr,
                'status'            => $status,
                'approvalData'      => $arrData,
                'setting'           => $setting->approval_setting,
                'isTrackingAvailable'       => $isTrackingAvailable,
                'trackedPrHeader'           => $trackedPrHeader ?? null,
                'trackedPoHeaders'          => $trackedPoHeaders ?? null,
                'trackedGrHeaders'          => $trackedGrHeaders ?? null,
                'trackedSjHeaders'          => $trackedSjHeaders ?? null,
                'trackedPiHeaders'          => $trackedPiHeaders ?? null,
                'isAuthorized'              => $isAuthorized,
                'isFeedbackAuthorized'      => $isFeedbackAuthorized
            ];

            return View('admin.inventory.material_requests.fuel.show')->with($data);
        }
        catch(\Exception $ex){
            Log::error("MaterialRequestHeaderController showFuel ". $header->code. " EX: ". $ex);
            dd("SYSTEM ERROR! PLEASE CONTACT ADMINISTRATOR!");
        }
    }

    public function showOil(MaterialRequestHeader $material_request){
        try
        {
            $header = $material_request;
            $date = Carbon::parse($material_request->date)->format('d M Y');

            $itemStocks = new Collection();

            if($header->status_id == 3 && $header->type === 'non-stock'){
                // Check stock
                $isInStock = true;
                foreach($header->material_request_details as $detail){
                    if($detail->item->stock < $detail->quantity){
                        $isInStock = false;
                    }
                }

                // Get stock
                if($isInStock){
                    foreach($header->material_request_details as $detail){
                        $stocks = ItemStock::where('item_id', $detail->item_id)
                            ->where('stock', '>', 0)
                            ->get();
                        foreach($stocks as $stock){
                            $itemStocks->add($stock);
                        }
                    }
                }
            }

            $isPrCreated = false;
            if(PurchaseRequestHeader::where('material_request_id', $header->id)->exists()){
                $isPrCreated = true;
            }

            // Check Approval & Permission to Print
            $user = \Auth::user();
            $roleId = $user->roles->pluck('id')[0];
            $isApproved = $header->is_approved === 1 ? true: false;
            $approveMr = false;
            // Kondisi belum diapprove
            $status = 0;

            // Check feedback authorized
            $isFeedbackAuthorized = false;
            if($user->id === $header->created_by || $roleId === 1 || $roleId === 3 ||
                $roleId === 4 || $roleId === 9 || $roleId || $roleId === 14 ||
                $roleId === 15 || $roleId === 18 || $roleId === 20){
                $isFeedbackAuthorized = true;
            }

            // All Approval Settings checked if On Or Not
            $setting = PreferenceCompany::find(1);
            $approvals = null;
            $arrData = array();

            // Custom approval rule for Mr. Christ
            if($header->site_id ===  3  && ($header->department_id === 7 || $header->department_id === 4)){
                $docId = 18;
            }
            else{
                // Get priority type
                if($header->priority === 'Part - P1' || $header->priority === 'Part - P2' || $header->priority === 'Part - P3'){
                    $docId = 11;
                }
                else{
                    $docId = 16;
                }
            }

            if($setting->approval_setting == 1) {
                $tempApprove = ApprovalRule::where('document_id', $docId)->where('user_id', $user->id)->get();
                $approvalRules = ApprovalRule::where('document_id', $docId)->get();

                if ($tempApprove->count() != 0) {
                    $approveMr = true;
                }

                $approverData = ApprovalMaterialRequest::where('material_request_id', $header->id)
                    ->where('user_id', $user->id)
                    ->first();

                if(!empty($approverData) && $header->is_approved === 1){
                    $approveMr = false;
                }

                // Kondisi Approve Sebagian
                $approverDatas = ApprovalMaterialRequest::where('material_request_id', $header->id)
                    ->get();

                if(!empty($approverData) || $approverDatas->count() > 0){
                    $status = $approverDatas->count();

                    // Kondisi Semua Sudah Approve
                    if($header->is_approved === 1){
                        $status = 99;
                    }
                }

                if($header->is_approved === 1){
                    foreach($approverDatas as $approver)
                    {
                        if($approver->status_id === 12){
                            $arrData[] = "<span style='color: green;'>". $approver->user->name . " - Approve</span>";
                        }
                        else{
                            $arrData[] = "<span style='color: red;'>". $approver->user->name . " - Tolak</span>";

                            // Kondisi ada yang reject
                            $status = 101;
                        }
                    }
                }
                else{
                    foreach($approvalRules as $rule)
                    {
                        $flag = 0;
                        foreach($approverDatas as $approver)
                        {
                            if($approver->user_id == $rule->user_id)
                            {
                                if($approver->status_id === 12){
                                    $flag = 1;
                                }
                                else{
                                    $flag = 2;
                                    // Kondisi ada yang reject
                                    $status = 101;
                                }
                            }
                        }

                        if($flag == 1){
                            $arrData[] = "<span style='color: green;'>". $rule->user->name . " - Approve</span>";
                        }
                        elseif($flag == 2){
                            $arrData[] = "<span style='color: red;'>". $rule->user->name . " - Tolak</span>";
                        }
                        else{
                            $arrData[] = "<span style='color: #f4bf42;'>". $rule->user->name . " - Belum Approve</span>";
                        }
                    }
                }
            }

            // MR Tracking
            $isTrackingAvailable = true;
            if($header->purchase_request_headers->count() === 0){
                $isTrackingAvailable = false;
            }

            if($isTrackingAvailable){
                $trackedPrHeader = $header->purchase_request_headers->first();
                $trackedPoHeaders = $trackedPrHeader->purchase_order_headers;
                $trackedGrHeaders = new Collection();
                foreach($trackedPoHeaders as $poHeader){
                    foreach($poHeader->item_receipt_headers as $grHeader){
                        $trackedGrHeaders->add($grHeader);
                    }
                }

                $trackedSjHeaders = new Collection();
                foreach($trackedGrHeaders as $trackedGrHeader){
                    foreach ($trackedGrHeader->delivery_order_headers as $sjHeader){
                        $trackedSjHeaders->add($sjHeader);
                    }
                }

                $trackedPiHeaders = new Collection();
                foreach($trackedPoHeaders as $poHeader){
                    foreach($poHeader->purchase_invoice_headers as $piHeader){
                        $trackedPiHeaders->add($piHeader);
                    }
                }

            }

//            $trackedIdHeaders = $header->issued_docket_headers;

            // Check menu permission
            $isAuthorized = false;
            if(PermissionMenu::where('role_id', $roleId)->where('menu_id', 23)->first()){
                $isAuthorized = true;
            }

            $data = [
                'header'            => $header,
                'date'              => $date,
                'itemStocks'        => $itemStocks,
                'isPrCreated'       => $isPrCreated,
                'isApproved'        => $isApproved,
                'approveMr'         => $approveMr,
                'status'            => $status,
                'approvalData'      => $arrData,
                'setting'           => $setting->approval_setting,
                'isTrackingAvailable'       => $isTrackingAvailable,
                'trackedPrHeader'           => $trackedPrHeader ?? null,
                'trackedPoHeaders'          => $trackedPoHeaders ?? null,
                'trackedGrHeaders'          => $trackedGrHeaders ?? null,
                'trackedSjHeaders'          => $trackedSjHeaders ?? null,
                'trackedPiHeaders'          => $trackedPiHeaders ?? null,
                'isAuthorized'              => $isAuthorized,
                'isFeedbackAuthorized'      => $isFeedbackAuthorized
            ];

            return View('admin.inventory.material_requests.oil.show')->with($data);
        }
        catch (\Exception $ex){
            Log::error("MaterialRequestHeaderController showOil ". $header->code. " EX: ". $ex);
            dd("SYSTEM ERROR! PLEASE CONTACT ADMINISTRATOR!");
        }
    }

    public function showService(MaterialRequestHeader $material_request){
        try
        {
            $header = $material_request;
            $date = Carbon::parse($material_request->date)->format('d M Y');

            $isPrCreated = false;
            if(PurchaseRequestHeader::where('material_request_id', $header->id)->exists()){
                $isPrCreated = true;
            }

            // Check Approval & Permission to Print
            $user = \Auth::user();
            $roleId = $user->roles->pluck('id')[0];
            $isApproved = $header->is_approved === 1 ? true: false;
            $approveMr = false;
            // Kondisi belum diapprove
            $status = 0;

            // Check feedback authorized
            $isFeedbackAuthorized = false;
            if($user->id === $header->created_by || $roleId === 1 || $roleId === 3 ||
                $roleId === 4 || $roleId === 9 || $roleId || $roleId === 14 ||
                $roleId === 15 || $roleId === 18 || $roleId === 20){
                $isFeedbackAuthorized = true;
            }

            // All Approval Settings checked if On Or Not
            $setting = PreferenceCompany::find(1);
            $approvals = null;
            $arrData = array();

            // Custom approval rule for Mr. Christ
            if($header->site_id ===  3  && ($header->department_id === 7 || $header->department_id === 4)){
                $docId = 18;
            }
            else{
                // Get priority type
                if($header->priority === 'Part - P1' || $header->priority === 'Part - P2' || $header->priority === 'Part - P3'){
                    $docId = 12;
                }
                else{
                    $docId = 17;
                }
            }


            if($setting->approval_setting == 1) {
                $tempApprove = ApprovalRule::where('document_id', $docId)->where('user_id', $user->id)->get();
                $approvalRules = ApprovalRule::where('document_id', $docId)->get();

                if ($tempApprove->count() != 0) {
                    $approveMr = true;
                }

                $approverData = ApprovalMaterialRequest::where('material_request_id', $header->id)
                    ->where('user_id', $user->id)
                    ->first();

                if(!empty($approverData) && $header->is_approved === 1){
                    $approveMr = false;
                }

                // Kondisi Approve Sebagian
                $approverDatas = ApprovalMaterialRequest::where('material_request_id', $header->id)
                    ->get();

                if(!empty($approverData) || $approverDatas->count() > 0){
                    $status = $approverDatas->count();

                    // Kondisi Semua Sudah Approve
                    if($header->is_approved === 1){
                        $status = 99;
                    }
                }

                if($header->is_approved === 1){
                    foreach($approverDatas as $approver)
                    {
                        if($approver->status_id === 12){
                            $arrData[] = "<span style='color: green;'>". $approver->user->name . " - Approve</span>";
                        }
                        else{
                            $arrData[] = "<span style='color: red;'>". $approver->user->name . " - Tolak</span>";

                            // Kondisi ada yang reject
                            $status = 101;
                        }
                    }
                }
                else{
                    foreach($approvalRules as $rule)
                    {
                        $flag = 0;
                        foreach($approverDatas as $approver)
                        {
                            if($approver->user_id == $rule->user_id)
                            {
                                if($approver->status_id === 12){
                                    $flag = 1;
                                }
                                else{
                                    $flag = 2;
                                    // Kondisi ada yang reject
                                    $status = 101;
                                }
                            }
                        }

                        if($flag == 1){
                            $arrData[] = "<span style='color: green;'>". $rule->user->name . " - Approve</span>";
                        }
                        elseif($flag == 2){
                            $arrData[] = "<span style='color: red;'>". $rule->user->name . " - Tolak</span>";
                        }
                        else{
                            $arrData[] = "<span style='color: #f4bf42;'>". $rule->user->name . " - Belum Approve</span>";
                        }
                    }
                }
            }

            // Check menu permission
            $isAuthorized = false;
            $roleId = $user->roles->pluck('id')[0];
            if(PermissionMenu::where('role_id', $roleId)->where('menu_id', 23)->first()){
                $isAuthorized = true;
            }

            // MR Tracking
            $isTrackingAvailable = true;
            if($header->purchase_request_headers->count() === 0){
                $isTrackingAvailable = false;
            }

            if($isTrackingAvailable){
                $trackedPrHeader = $header->purchase_request_headers->first();
                $trackedPoHeaders = $trackedPrHeader->purchase_order_headers;

                $trackedPiHeaders = new Collection();
                foreach($trackedPoHeaders as $poHeader){
                    foreach($poHeader->purchase_invoice_headers as $piHeader){
                        $trackedPiHeaders->add($piHeader);
                    }
                }
            }

            $data = [
                'header'            => $header,
                'date'              => $date,
                'isPrCreated'       => $isPrCreated,
                'isApproved'        => $isApproved,
                'approveMr'         => $approveMr,
                'status'            => $status,
                'approvalData'      => $arrData,
                'setting'           => $setting->approval_setting,
                'isAuthorized'      => $isAuthorized,
                'isTrackingAvailable'       => $isTrackingAvailable,
                'trackedPrHeader'           => $trackedPrHeader ?? null,
                'trackedPoHeaders'          => $trackedPoHeaders ?? null,
                'trackedPiHeaders'          => $trackedPiHeaders ?? null,
                'isFeedbackAuthorized'      => $isFeedbackAuthorized
            ];

            return View('admin.inventory.material_requests.service.show')->with($data);
        }
        catch (\Exception $ex){
            Log::error("MaterialRequestHeaderController showService ". $header->code. " EX: ". $ex);
            dd("SYSTEM ERROR! PLEASE CONTACT ADMINISTRATOR!");
        }
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'mr_code'       => 'required|max:30|regex:/^\S*$/u',
            'km'            => 'max:20',
            'hm'            => 'max:20',
            'date'          => 'required'
        ],[
            'mr_code.required'      => 'Nomor MR wajib diisi!',
            'mr_code.regex'         => 'Nomor MR harus tanpa spasi!'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate department
        if($request->input('department') === '-1'){
            return redirect()->back()->withErrors('Pilih departemen!', 'default')->withInput($request->all());
        }

        // Validate warehouse
        if($request->input('warehouse') === '-1'){
            return redirect()->back()->withErrors('Pilih gudang!', 'default')->withInput($request->all());
        }

        // Validate priority
        if($request->input('priority') === '-1'){
            return redirect()->back()->withErrors('Pilih prioritas!', 'default')->withInput($request->all());
        }

        $priority = $request->input('priority');
        if(empty($request->input('is_reorder'))){

            // Validate priority and machinery if no reorder type
            if($priority == 'Part - P1' || $priority == 'Part - P2' || $priority == 'Part - P3'){
                if(!$request->filled('machinery') || !$request->filled('km') || !$request->filled('km')){
                    return redirect()->back()->withErrors('Unit Alat Berat, KM dan HM wajib diisi!', 'default')->withInput($request->all());
                }
            }

            // Validate uploaded document based on priority if no reorder type
            if($priority == 'Part - P1' || $priority == 'Part - P2'){
                if($request->file('document') == null){
                    return redirect()->back()->withErrors('Wajib unggah berita acara untuk prioritas Part P1 dan Part P2!', 'default')->withInput($request->all());
                }
            }
        }

        // Generate auto number
        $type = $request->input('type');
        $docId = 0;
        if($type == '1'){
            $docId = 9;
        }
        else if($type == '2'){
            $docId = 10;
        }
        else if($type == '3'){
            $docId = 11;
        }
        else{
            $docId = 13;
        }

        // Validate details
        $items = $request->input('item');

        if(count($items) == 0){
            return redirect()->back()->withErrors('Detail barang wajib diisi!', 'default')->withInput($request->all());
        }

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');

        //$sysNo = NumberingSystem::where('doc_id', $docId)->first();
        if($request->filled('auto_number')){
            // = $sysNo->document->code. '-'. $user->employee->site->code;
            //$mrCode = Utilities::GenerateNumber($docCode, $sysNo->next_no);

            //$sysNo = NumberingSystem::where('doc_id', '9')->first();
            $doc = Document::find($docId);
            $mrPrepend = $doc->code. '/'. $now->year. '/'. $now->month;
            $sysNo = Utilities::GetNextAutoNumber($mrPrepend);

            $docCode = $doc->code. '-'. $user->employee->site->code;
            $mrCode = Utilities::GenerateNumber($docCode, $sysNo);

            // Check existing number
            if(MaterialRequestHeader::where('code', $mrCode)->exists()){
                return redirect()->back()->withErrors('Nomor MR sudah terdaftar!', 'default')->withInput($request->all());
            }
        }
        else{
            $mrCode = $request->input('mr_code');

            // Check existing number
            if(MaterialRequestHeader::where('code', $mrCode)->exists()){
                return redirect()->back()->withErrors('Nomor MR sudah terdaftar!', 'default')->withInput($request->all());
            }
        }

         //Get requester site id
        $siteId = $user->employee->site_id;
        // Get department id
        $departmentId = $request->input('department');

        // Approval custom rules for Mr. Christ
        if($siteId === 3  && ($departmentId === "7" || $departmentId === "4")){
            $docId = 18;
        }
        else{
            if($type == '1'){
                if($priority == 'Part - P1' || $priority == 'Part - P2' || $priority == 'Part - P3'){
                    $docId = 9;
                }
                else{
                    $docId = 14;
                }
            }
            else if($type == '2'){
                if($priority == 'Part - P1' || $priority == 'Part - P2' || $priority == 'Part - P3'){
                    $docId = 10;
                }
                else{
                    $docId = 15;
                }
            }
            else if($type == '3'){
                if($priority == 'Part - P1' || $priority == 'Part - P2' || $priority == 'Part - P3'){
                    $docId = 11;
                }
                else{
                    $docId = 16;
                }
            }
            else{
                if($priority == 'Part - P1' || $priority == 'Part - P2' || $priority == 'Part - P3'){
                    $docId = 13;
                }
                else{
                    $docId = 17;
                }
            }
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
            return redirect()->back()->withErrors('Detail inventory dan kuantitas wajib diisi!', 'default')->withInput($request->all());
        }

        // Check duplicate inventory
        $validUnique = Utilities::arrayIsUnique($items);
        if(!$validUnique){
            return redirect()->back()->withErrors('Detail inventory tidak boleh kembar!', 'default')->withInput($request->all());
        }

        $warehouseId = $request->input('warehouse');
        $mrHeader = MaterialRequestHeader::create([
            'code'              => $mrCode,
            'site_id'           => $siteId,
            'type'              => $type,
            'purpose'           => 'stock',
            'department_id'     => $departmentId,
            'priority'          => $request->input('priority'),
            'km'                => $request->input('km'),
            'hm'                => $request->input('hm'),
            'is_retur'          => 0,
            'is_issued'         => 0,
            'is_approved'       => 0,
            'status_id'         => 3,
            'created_by'        => $user->id,
            'created_at'        => $now->toDateTimeString(),
            'updated_by'        => $user->id,
            'warehouse_id'      => $warehouseId
        ]);

        if($request->filled('machinery')){
            $arrMachinery = explode('#', $request->input('machinery'));
            $mrHeader->machinery_id = $arrMachinery[0];
        }

        // Check is reorder
        $isReorder = false;
        if(!empty($request->input('is_reorder'))){
            $mrHeader->is_reorder = $request->input('is_reorder');
            $isReorder = true;
        }

        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');
        $mrHeader->date = $date->toDateTimeString();

        // Check uploaded pdf document
        if($request->file('document') != null){
            $folderPath = 'assets/documents/mr';
            $mrCodeFiltered = str_replace('/', '_', $mrHeader->code);
            $name = 'BERITA_ACARA_'. $mrCodeFiltered. '_'. $now->format('Ymdhms'). '.pdf';

            $path = $request->file('document')->storeAs(
                $folderPath, $name, 'public_uploads'
            );

            $mrHeader->pdf_path = $folderPath. '/'. $name;
        }

        $mrHeader->save();

        // Increase autonumber
        if($request->filled('auto_number')){
            $mrPrepend = 'MR/'. $now->year. '/'. $now->month;
            Utilities::UpdateAutoNumber($mrPrepend);
//            $sysNo->next_no++;
//            $sysNo->save();
        }

        // Create material request detail
        $qty = $request->input('qty');
        $remark = $request->input('remark');
        $idx = 0;
        foreach($request->input('item') as $item){
            if(!empty($item)){
                $splitted = explode('#', $item);
                $mrDetail = MaterialRequestDetail::create([
                    'header_id'         => $mrHeader->id,
                    'item_id'           => $splitted[0],
                    'quantity'          => $qty[$idx],
                    'quantity_received' => 0,
                    'quantity_issued'   => 0,
                    'quantity_retur'    => 0
                ]);

                if(!empty($remark[$idx])) $mrDetail->remark = $remark[$idx];
                $mrDetail->save();
            }
            $idx++;
        }

        // Reorder process
        if($isReorder){
            $idx = 0;
            foreach($request->input('item') as $item){
                if(!empty($item)){
                    $splitted = explode('#', $item);
                    $itemId = intval($splitted[0]);
                    $qtyInt = intval($qty[$idx]);
                    $itemStock = ItemStock::where('warehouse_id', $warehouseId)
                        ->where('item_id', $itemId)
                        ->first();
                    $itemStock->stock_on_reorder += $qtyInt;
                    $itemStock->save();
                }
                $idx++;
            }
        }

        // Check Approval Feature
        $environment = env('APP_ENV','local');
        $preference = PreferenceCompany::find(1);
        $isApproval = true;
        $approvals = ApprovalRule::where('document_id', $docId)->get();

        // Get priority type for MR approval
        if($priority === 'Part - P1' || $priority === 'Part - P2' || $priority === 'Part - P3'){
            $priorityApproval = 'PART';
        }
        else{
            $priorityApproval = 'NON-PART';
        }

        $approvalSetting = $preference->approval_setting;
        if($user->id === 39){
            $approvalSetting = 0;
        }

        if($approvalSetting === 1) {
            if($approvals->count() > 0){
                foreach($approvals as $approval){
                    if(!empty($approval->user->email_address)){
                        if($environment === 'prod'){
                            for ($try = 0; $try < 3; $try++){
                                try{
                                    Mail::to($approval->user->email_address)->send(new ApprovalMaterialRequestCreated($mrHeader, $approval->user));
                                    Log::info($approval->user->email_address. ' Send Approval Material Request '. $mrHeader->code);
                                    break 1;
                                }
                                catch (\Exception $ex){
                                    Log::error('MaterialRequestHeaderController - store : '. $ex);
                                }
                            }
                        }
                        else{
                            try{
                                if($docId === 18){
                                    Mail::to('mudagigih@gmail.com')->send(new ApprovalMaterialRequestCreated($mrHeader, $approval->user));
                                }
                                else{
                                    if($priorityApproval === 'PART'){
                                        Mail::to('hellbardx2@gmail.com')->send(new ApprovalMaterialRequestCreated($mrHeader, $approval->user));
                                    }
                                    else{
                                        Mail::to('bayuindra091191@gmail.com')->send(new ApprovalMaterialRequestCreated($mrHeader, $approval->user));
                                    }
                                }
                            }
                            catch (\Exception $ex){
                                error_log($ex);
                            }
                        }
                    }
                }
            }
            else{
                $isApproval = false;
            }
        }
        else{
            $isApproval = false;
        }

        if(!$isApproval){
            // Auto approved MR
            foreach($approvals as $approval){

                ApprovalMaterialRequest::create([
                    'material_request_id'   => $mrHeader->id,
                    'user_id'               => $approval->user_id,
                    'status_id'             => 12,
                    'created_at'            => $now->toDateTimeString(),
                    'updated_at'            => $now->toDateTimeString(),
                    'created_by'            => $user->id,
                    'updated_by'            => $user->id,
                    'priority'              => $priorityApproval
                ]);
            }

            $mrHeader->is_approved = 1;
            $mrHeader->approved_date = $now->toDateTimeString();
            $mrHeader->save();

            // Check stock
            $isInStock = true;
            $idx = 0;
            foreach($request->input('item') as $item){
                if(!empty($item)){
                    $splitted = explode('#', $item);
                    $qtyInt = (int) $qty[$idx];
                    $item = Item::find($splitted[0]);
                    if($item->stock < $qtyInt){
                        $isInStock = false;
                    }
                }
                $idx++;
            }

            // Send notification
            $mrCreator = $mrHeader->createdBy;
            $mrCreator->notify(new MaterialRequestCreated($mrHeader, false, 'true'));

            $roleIds = [4,5,12];

            $roles = Role::whereIn('id', $roleIds)->get();
            foreach($roles as $role){
                $users =  $role->users()->get();
                if($users->count() > 0){
                    foreach ($users as $notifiedUser){
                        $notifiedUser->notify(new MaterialRequestCreated($mrHeader, $isInStock, 'false'));
                    }
                }
            }
        }

        Session::flash('message', 'Berhasil membuat material request!');

        Session::forget('reorderItem');
        Session::forget('warehouseId');

        if($type === '1'){
            return redirect()->route('admin.material_requests.other.show', ['material_request' => $mrHeader]);
        }
        else if($type === '2'){
            return redirect()->route('admin.material_requests.fuel.show', ['material_request' => $mrHeader]);
        }
        else if($type === '3'){
            return redirect()->route('admin.material_requests.oil.show', ['material_request' => $mrHeader]);
        }
        else{
            return redirect()->route('admin.material_requests.service.show', ['material_request' => $mrHeader]);
        }
    }

    public function storeService(Request $request){
        $validator = Validator::make($request->all(),[
            'mr_code'       => 'required|max:30|regex:/^\S*$/u',
            'km'            => 'max:20',
            'hm'            => 'max:20',
            'date'          => 'required',
            'note'          => 'required|max:300'
        ],[
            'mr_code.required'      => 'Nomor MR wajib diisi!',
            'mr_code.regex'         => 'Nomor MR harus tanpa spasi!',
            'note.max'              => 'Keterangan servis tidak boleh lebih dari 300 karakter!'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate department
        if($request->input('department') === '-1'){
            return redirect()->back()->withErrors('Pilih departemen!', 'default')->withInput($request->all());
        }

        // Validate priority and machinery
        $priority = $request->input('priority');
        if($priority === 'Part - P1' || $priority === 'Part - P2' || $priority === 'Part - P3'){
            if(!$request->filled('machinery') || !$request->filled('km') || !$request->filled('km')){
                return redirect()->back()->withErrors('Unit Alat Berat, KM dan HM wajib diisi!', 'default')->withInput($request->all());
            }
        }

        // Validate uploaded document based on priority
        if($priority === 'Part - P1' || $priority === 'Part - P2'){
            if($request->file('document') == null){
                return redirect()->back()->withErrors('Wajib unggah berita acara untuk prioritas Part P1 dan Part P2!', 'default')->withInput($request->all());
            }
        }

        // Validate priority
        $priority = $request->input('priority');
        if($priority === '-1'){
            return redirect()->back()->withErrors('Pilih prioritas!', 'default')->withInput($request->all());
        }

        // Generate auto number
        $type = $request->input('type');

        $docId = 12;

        $user = Auth::user();

        $now = Carbon::now('Asia/Jakarta');
        if($request->filled('auto_number')){
//            $sysNo = NumberingSystem::where('doc_id', $docId)->first();
//            $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
//            $mrCode = Utilities::GenerateNumber($docCode, $sysNo->next_no);

            $doc = Document::find($docId);
            $mrPrepend = $doc->code. '/'. $now->year. '/'. $now->month;
            $sysNo = Utilities::GetNextAutoNumber($mrPrepend);

            $docCode = $doc->code. '-'. $user->employee->site->code;
            $mrCode = Utilities::GenerateNumber($docCode, $sysNo);

            // Check existing number
            if(MaterialRequestHeader::where('code', $mrCode)->exists()){
                return redirect()->back()->withErrors('Nomor MR sudah terdaftar!', 'default')->withInput($request->all());
            }

            $sysNo->next_no++;
            $sysNo->save();
        }
        else{
            $mrCode = $request->input('mr_code');

            // Check existing number
            if(MaterialRequestHeader::where('code', $mrCode)->exists()){
                return redirect()->back()->withErrors('Nomor MR sudah terdaftar!', 'default')->withInput($request->all());
            }
        }

        // Get requester site id
        $siteId = $user->employee->site_id;
        // Get department id
        $departmentId = $request->input('department');

        // Approval custom rules for Mr. Christ
        if($siteId === 3  && ($departmentId === "7" || $departmentId === "4")){
            $docId = 18;
        }
        else{
            if($type == '1'){
                if($priority == 'Part - P1' || $priority == 'Part - P2' || $priority == 'Part - P3'){
                    $docId = 9;
                }
                else{
                    $docId = 14;
                }
            }
            else if($type == '2'){
                if($priority == 'Part - P1' || $priority == 'Part - P2' || $priority == 'Part - P3'){
                    $docId = 10;
                }
                else{
                    $docId = 15;
                }
            }
            else if($type == '3'){
                if($priority == 'Part - P1' || $priority == 'Part - P2' || $priority == 'Part - P3'){
                    $docId = 11;
                }
                else{
                    $docId = 16;
                }
            }
            else{
                if($priority == 'Part - P1' || $priority == 'Part - P2' || $priority == 'Part - P3'){
                    $docId = 13;
                }
                else{
                    $docId = 17;
                }
            }
        }

        $mrHeader = MaterialRequestHeader::create([
            'code'              => $mrCode,
            'type'              => $type,
            'site_id'           => $siteId,
            'department_id'     => $departmentId,
            'warehouse_id'      => 1,
            'priority'          => $request->input('priority'),
            'km'                => $request->input('km'),
            'hm'                => $request->input('hm'),
            'is_retur'          => 0,
            'is_issued'         => 0,
            'is_approved'       => 0,
            'status_id'         => 3,
            'created_by'        => $user->id,
            'created_at'        => $now->toDateTimeString(),
            'updated_by'        => $user->id
        ]);

        if($request->filled('machinery')){
            $arrMachinery = explode('#', $request->input('machinery'));
            $mrHeader->machinery_id = $arrMachinery[0];
            $mrHeader->save();
        }

        // Check uploaded pdf document
        if($request->file('document') != null){
            $folderPath = 'assets/documents/mr';
            $mrCodeFiltered = str_replace('/', '_', $mrHeader->code);
            $name = 'BERITA_ACARA_'. $mrCodeFiltered. '_'. $now->format('Ymdhms'). '.pdf';

            $path = $request->file('document')->storeAs(
                $folderPath, $name, 'public_uploads'
            );

            $mrHeader->pdf_path = $folderPath. '/'. $name;
        }

        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');
        $mrHeader->date = $date->toDateTimeString();
        $mrHeader->save();

        // Create material request detail
        $mrDetail = MaterialRequestDetail::create([
            'header_id'         => $mrHeader->id,
            'item_id'           => 0,
            'quantity'          => 1,
            'quantity_received' => 0,
            'quantity_issued'   => 0,
            'remark'            => $request->input('note')
        ]);

        // Check Approval Feature
        $environment = env('APP_ENV','local');
        $preference = PreferenceCompany::find(1);
        $isApproval = true;
        $approvals = ApprovalRule::where('document_id', $docId)->get();

        // Get priority type for MR approval
        if($priority === 'Part - P1' || $priority === 'Part - P2' || $priority === 'Part - P3'){
            $priorityApproval = 'PART';
        }
        else{
            $priorityApproval = 'NON-PART';
        }

        if($preference->approval_setting == 1) {
            if($approvals->count() > 0){
                foreach($approvals as $approval){
                    if(!empty($approval->user->email_address)){
                        if($environment === 'prod'){
                            for ($try = 0; $try < 3; $try++){
                                try{
                                    Mail::to($approval->user->email_address)->send(new ApprovalMaterialRequestCreated($mrHeader, $approval->user));
                                    Log::info($approval->user->email_address. ' Send Approval Material Request '. $mrHeader->code);
                                    break 1;
                                }
                                catch (\Exception $ex){
                                    Log::error('MaterialRequestHeaderController - storeService : '. $ex);
                                }
                            }
                        }
                        else{
                            try{
                                if($priorityApproval === 'PART'){
                                    Mail::to('hellbardx2@gmail.com')->send(new ApprovalMaterialRequestCreated($mrHeader, $approval->user));
                                }
                                else{
                                    Mail::to('bayuindra091191@gmail.com')->send(new ApprovalMaterialRequestCreated($mrHeader, $approval->user));
                                }
                            }
                            catch (\Exception $ex){
                                error_log($ex);
                            }

                        }
                    }
                }
            }
            else{
                $isApproval = false;
            }
        }
        else{
            $isApproval = false;
        }

        if(!$isApproval){
            // Auto approved MR
            foreach($approvals as $approval){
                ApprovalMaterialRequest::create([
                    'material_request_id'   => $mrHeader->id,
                    'user_id'               => $approval->user_id,
                    'status_id'             => 12,
                    'created_at'            => $now->toDateTimeString(),
                    'updated_at'            => $now->toDateTimeString(),
                    'created_by'            => $user->id,
                    'updated_by'            => $user->id,
                    'priority'              => $priorityApproval
                ]);
            }

            $mrHeader->is_approved = 1;
            $mrHeader->approved_date = $now->toDateTimeString();
            $mrHeader->save();

            // Send notification
            $mrCreator = $mrHeader->createdBy;
            $mrCreator->notify(new MaterialRequestCreated($mrHeader, false, 'true'));

            $roleIds = [4,5,12];

            $roles = Role::whereIn('id', $roleIds)->get();
            foreach($roles as $role){
                $users =  $role->users()->get();
                if($users->count() > 0){
                    foreach ($users as $notifiedUser){
                        $notifiedUser->notify(new MaterialRequestCreated($mrHeader, false, 'false'));
                    }
                }
            }
        }

        Session::flash('message', 'Berhasil membuat material request service!');

        return redirect()->route('admin.material_requests.service.show', ['material_request' => $mrHeader]);
    }

    public function editOther(MaterialRequestHeader $material_request){
        $header = $material_request;

        $user = Auth::user();
        $roleId = $user->roles->pluck('id')[0];
        if($header->is_approved === 1 && $roleId != 14 && $roleId != 1){
            return redirect()->back();
        }

        // Validate status
        if($header->status_id !== 3){
            return redirect()->route('admin.material_requests.other.show', ['material_request' => $header]);
        }

        // Validate PR exists
        if(PurchaseRequestHeader::where('material_request_id', $header->id)->exists()){
            return redirect()->route('admin.material_requests.other.show', ['material_request' => $header]);
        }

        $departments = Department::all();
        $date = Carbon::parse($material_request->date)->format('d M Y');

        $isOldPriority = false;
        if($header->priority == '1' || $header->priority == '2' || $header->priority == '3'){
            $isOldPriority = true;
        }

        // Check PDF exists
        $pdfUrl = null;
        if(!empty($header->pdf_path)){
            $pdfUrl = public_path(). '/'. $header->pdf_path;
        }

        $data = [
            'header'        => $header,
            'departments'   => $departments,
            'date'          => $date,
            'isOldPriority' => $isOldPriority,
            'pdfUrl'        => $pdfUrl
        ];

        return View('admin.inventory.material_requests.other.edit')->with($data);
    }

    public function editFuel(MaterialRequestHeader $material_request){
        $header = $material_request;

        $user = Auth::user();
        $roleId = $user->roles->pluck('id')[0];
        if($header->is_approved === 1 && $roleId != 14 && $roleId != 1){
            return redirect()->back();
        }

        // Validate status
        if($header->status_id !== 3){
            return redirect()->route('admin.material_requests.fuel.show', ['material_request' => $header]);
        }

        // Validate PR exists
        if(PurchaseRequestHeader::where('material_request_id', $header->id)->exists()){
            return redirect()->route('admin.material_requests.fuel.show', ['material_request' => $header]);
        }

        $departments = Department::all();
        $date = Carbon::parse($material_request->date)->format('d M Y');

        $isOldPriority = false;
        if($header->priority == '1' || $header->priority == '2' || $header->priority == '3'){
            $isOldPriority = true;
        }

        // Check PDF exists
        $pdfUrl = null;
        if(!empty($header->pdf_path)){
            $pdfUrl = public_path(). '/'. $header->pdf_path;
        }

        $data = [
            'header'        => $header,
            'departments'   => $departments,
            'date'          => $date,
            'isOldPriority' => $isOldPriority,
            'pdfUrl'        => $pdfUrl
        ];

        return View('admin.inventory.material_requests.fuel.edit')->with($data);
    }

    public function editOil(MaterialRequestHeader $material_request){
        $header = $material_request;

        $user = Auth::user();
        $roleId = $user->roles->pluck('id')[0];
        if($header->is_approved === 1 && $roleId != 14 && $roleId != 1){
            return redirect()->back();
        }

        // Validate status
        if($header->status_id !== 3){
            return redirect()->route('admin.material_requests.oil.show', ['material_request' => $header]);
        }

        // Validate PR exists
        if(PurchaseRequestHeader::where('material_request_id', $header->id)->exists()){
            return redirect()->route('admin.material_requests.oil.show', ['material_request' => $header]);
        }

        $departments = Department::all();
        $date = Carbon::parse($material_request->date)->format('d M Y');

        $isOldPriority = false;
        if($header->priority == '1' || $header->priority == '2' || $header->priority == '3'){
            $isOldPriority = true;
        }

        // Check PDF exists
        $pdfUrl = null;
        if(!empty($header->pdf_path)){
            $pdfUrl = public_path(). '/'. $header->pdf_path;
        }

        $data = [
            'header'        => $header,
            'departments'   => $departments,
            'date'          => $date,
            'isOldPriority' => $isOldPriority,
            'pdfUrl'        => $pdfUrl
        ];

        return View('admin.inventory.material_requests.oil.edit')->with($data);
    }

    public function editService(MaterialRequestHeader $material_request){
        $header = $material_request;

        $user = Auth::user();
        $roleId = $user->roles->pluck('id')[0];
        if($header->is_approved === 1 && $roleId != 14 && $roleId != 1){
            return redirect()->back();
        }

        // Validate status
        if($header->status_id !== 3){
            return redirect()->route('admin.material_requests.service.show', ['material_request' => $header]);
        }

        // Validate PR exists
        if(PurchaseRequestHeader::where('material_request_id', $header->id)->exists()){
            return redirect()->route('admin.material_requests.service.show', ['material_request' => $header]);
        }

        $departments = Department::all();
        $date = Carbon::parse($material_request->date)->format('d M Y');

        $isOldPriority = false;
        if($header->priority == '1' || $header->priority == '2' || $header->priority == '3'){
            $isOldPriority = true;
        }

        // Check PDF exists
        $pdfUrl = null;
        if(!empty($header->pdf_path)){
            $pdfUrl = public_path(). '/'. $header->pdf_path;
        }

        $data = [
            'header'        => $header,
            'departments'   => $departments,
            'date'          => $date,
            'isOldPriority' => $isOldPriority,
            'pdfUrl'        => $pdfUrl
        ];

        return View('admin.inventory.material_requests.service.edit')->with($data);
    }

    public function update(Request $request, MaterialRequestHeader $material_request){
        $validator = Validator::make($request->all(),[
            'km'            => 'max:20',
            'hm'            => 'max:20',
            'date'          => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $type = $request->input('type');

        // Validate department
        if($request->input('department') === '-1'){
            return redirect()->back()->withErrors('Pilih departemen!', 'default')->withInput($request->all());
        }

        // Validate priority
        if($request->input('priority') === '-1'){
            return redirect()->back()->withErrors('Pilih prioritas!', 'default')->withInput($request->all());
        }

        // Validate priority and machinery
        $priority = $request->input('priority');
        if($priority == 'Part - P1' || $priority == 'Part - P2' || $priority == 'Part - P3'){
            if(!$request->filled('km') || !$request->filled('km')){
                return redirect()->back()->withErrors('Unit Alat Berat, KM dan HM wajib diisi!', 'default')->withInput($request->all());
            }
        }

        // Validate uploaded document based on priority
//        if($priority == 'Part - P1' || $priority == 'Part - P2'){
//            if($request->file('document') == null){
//                return redirect()->back()->withErrors('Wajib unggah berita acara untuk prioritas Part P1 dan Part P2!', 'default')->withInput($request->all());
//            }
//        }

        if($type === '4'){
            // Validate service note
            if(!$request->filled('note')){
                return redirect()->back()->withErrors('Keterangan servis wajib diisi!', 'default')->withInput($request->all());
            }
        }

        $user = \Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');

//        $material_request->purpose = $request->input('purpose');
        $material_request->department_id = $request->input('department');
        $material_request->priority = $request->input('priority');
        $material_request->km = $request->input('km');
        $material_request->hm = $request->input('hm');
        $material_request->date = $date;
        $material_request->updated_by = $user->id;
        $material_request->updated_at = $now->toDateTimeString();

        if($request->filled('machinery')){
            $material_request->machinery_id = $request->input('machinery');
        }

        // Check PDF exists
        if($request->file('document') != null){
            if(!empty($material_request->pdf_path)){

                // Delete old pdf
                $deletedPath = public_path($material_request->pdf_path);
                if(file_exists($deletedPath)) unlink($deletedPath);
            }

            $folderPath = 'assets/documents/mr';
            $mrCodeFiltered = str_replace('/', '_', $material_request->code);
            $name = 'BERITA_ACARA_'. $mrCodeFiltered. '_'. $now->format('Ymdhms'). '.pdf';

            $path = $request->file('document')->storeAs(
                $folderPath, $name, 'public_uploads'
            );

            $material_request->pdf_path = $folderPath. '/'. $name;
        }

        $material_request->save();

        if($type === '4'){
            $note = $request->input('note');
            $mrDetail = $material_request->material_request_details->first();
            $mrDetail->remark = $note;
            $mrDetail->save();
        }

        Session::flash('message', 'Berhasil ubah material request!');

        if($type === '1'){
            return redirect()->route('admin.material_requests.other.show', ['material_request' => $material_request]);
        }
        else if($type === '2'){
            return redirect()->route('admin.material_requests.fuel.show', ['material_request' => $material_request]);
        }
        else if($type === '3'){
            return redirect()->route('admin.material_requests.oil.show', ['material_request' => $material_request]);
        }
        else{
            return redirect()->route('admin.material_requests.service.show', ['material_request' => $material_request]);
        }
    }

    /**
     * Store and Replaced Feedback
     * @param Request $request
     * @param MaterialRequestHeader $material_request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function feedback(Request $request, MaterialRequestHeader $material_request){
        $material_request->feedback = $request->get('feedback');
        $material_request->save();

        $type = $material_request->type;

        if($type == 1){
            return redirect()->route('admin.material_requests.other.show', ['material_request' => $material_request]);
        }
        else if($type == 2){
            return redirect()->route('admin.material_requests.fuel.show', ['material_request' => $material_request]);
        }
        else if($type == 3){
            return redirect()->route('admin.material_requests.oil.show', ['material_request' => $material_request]);
        }
        else {
            return redirect()->route('admin.material_requests.service.show', ['material_request' => $material_request]);
        }
    }


    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function getIndex(Request $request){

        $type = 'default';
        if($request->filled('type')){
            $type = $request->input('type');
        }

        $materialRequests = null;

        $status = '0';
        if($request->filled('status')){
            $status = $request->input('status');
            if($status != '0'){
                $materialRequests = MaterialRequestHeader::where('status_id', $status);
            }
            else{
                $materialRequests = MaterialRequestHeader::whereIn('status_id', [3,4,11,13]);
            }
        }
        else{
            $materialRequests = MaterialRequestHeader::where('status_id', 3);
        }

        $mode = 'default';
        if($request->filled('mode')){
            $mode = $request->input('mode');
            if($mode === 'before_create_id'){
                $materialRequests = $materialRequests
                    ->where('purpose', 'non-stock')
                    ->where(function($q) {
                        $q->where('is_issued', 0)
                            ->orWhere('is_issued', 1);
                    });
            }

            $materialRequests = $materialRequests->where('is_approved', 1);
        }

        if($type === 'part'){
            $materialRequests = $materialRequests->where('type', 1);
        }
        else if($type === 'fuel'){
            $materialRequests = $materialRequests->where('type', 2);
        }
        else if($type === 'oil'){
            $materialRequests = $materialRequests->where('type', 3);
        }
        else if($type === 'service'){
            $materialRequests = $materialRequests->where('type', 4);
        }
        else if($type === 'non-service'){
            $materialRequests = $materialRequests->where('type', '!=', 4);
        }

        if($request->filled('is_mr_exists')){
            $isMrExist = $request->input('is_mr_exists') == 'true' ? true : false;
            if(!$isMrExist){
                $materialRequests = $materialRequests->doesntHave('purchase_request_headers');
            }
        }

        $materialRequests = $materialRequests
            ->dateDescending();

        return DataTables::of($materialRequests)
            ->setTransformer(new MaterialRequestHeaderTransformer($mode))
            ->make(true);
    }

    public function getMaterialRequests(Request $request){
        $term = trim($request->q);
        $materialRequests = MaterialRequestHeader::where('status_id', 3)
            ->where('code', 'LIKE', '%'. $term. '%')
            ->get();

        $formatted_tags = [];

        foreach ($materialRequests as $materialRequest) {
            $formatted_tags[] = ['id' => $materialRequest->id, 'text' => $materialRequest->code];
        }

        return \Response::json($formatted_tags);
    }

    public function close(Request $request){
        try{
            $user = \Auth::user();
            $now = Carbon::now('Asia/Jakarta');

            $materialRequest = MaterialRequestHeader::find($request->input('id'));
            $materialRequest->closed_by = $user->id;
            $materialRequest->closed_at = $now->toDateTimeString();
            $materialRequest->close_reason = $request->input('reason');
            $materialRequest->status_id = 11;
            $materialRequest->save();

            // Undo reorder process
            if($materialRequest->is_reorder === 1){
                foreach ($materialRequest->material_request_details as $detail){
                    $itemStock = ItemStock::where('warehouse_id', $materialRequest->warehouse_id)
                        ->where('item_id', $detail->item_id)
                        ->first();
                    $itemStock->stock_on_reorder -= $detail->quantity;
                    $itemStock->save();
                }
            }

            Session::flash('message', 'Berhasil tutup MR!');

            return Response::json(array('success' => 'VALID'));
        }
        catch(\Exception $ex){
            return Response::json(array('errors' => 'INVALID'));
        }
    }

    public function printDocument($id){
        $materialRequest = MaterialRequestHeader::find($id);

        // Check approval
        $setting = PreferenceCompany::find(1);

        $type = $materialRequest->type;
        if($type == 1){
            $docId = 9;
        }
        elseif($type == 2){
            $docId = 10;
        }
        elseif($type == 3){
            $docId = 11;
        }
        else{
            $docId = 12;
        }

        $approval = ApprovalRule::where('document_id', $docId)->first();

        $isApproved = $materialRequest->is_approved === 1 ? true : false;

        $data =[
            'materialRequest'   => $materialRequest,
            'isApproved'        => $isApproved,
            'approvedBy'        => $approval->user ?? null
        ];

        return view('documents.material_requests.material_requests_doc')->with($data);
    }

    public function report(){
        $departments = Department::all();

        return View('admin.inventory.material_requests.report', compact('departments'));
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

        $start = $start->addDays(-1);
        $end = $end->addDays(1);

        $mrHeaders = MaterialRequestHeader::whereBetween('date', array($start->toDateTimeString(), $end->toDateTimeString()));

        // Filter type
        $type = $request->input('type');
        if($type != '0'){
            $mrHeaders = $mrHeaders->where('type', $type);
        }

        // Filter departemen
        $filterDepartment = 'Semua';
        $department = $request->input('department');
        if($department != '0'){
            $mrHeaders = $mrHeaders->where('department_id', $department);
            $filterDepartment = Department::find($department)->name;
        }

        // Filter unit alat berat
        $filterMachinery = 'Semua';
        if($request->filled('machinery')){
            $machineryId = $request->input('machinery');
            $mrHeaders = $mrHeaders->where('machinery_id', $machineryId);
            $filterMachinery = Machinery::find($machineryId)->code;
        }

        // Filter status
        $filterStatus = 'Semua';
        $status = $request->input('status');
        if($status != '0'){
            $mrHeaders = $mrHeaders->where('status_id', $status);
            $filterStatus = Status::find($status)->description;
        }

        $mrHeaders = $mrHeaders->orderByDesc('date')
            ->get();

        // Validate Data
        if($mrHeaders->count() == 0){
            return redirect()->back()->withErrors('Data tidak ditemukan!', 'default')->withInput($request->all());
        }

        if($request->input('is_preview') === 'false'){
            $data =[
                'mrHeaders'         => $mrHeaders,
                'start_date'        => $request->input('start_date'),
                'finish_date'       => $request->input('end_date'),
                'filterDepartment'  => $filterDepartment,
                'filterStatus'      => $filterStatus,
                'filterMachinery'   => $filterMachinery
            ];

            //return view('documents.material_requests.material_requests_pdf')->with($data);

            $now = Carbon::now('Asia/Jakarta');
            $filename = 'MATERIAL_REQUEST_REPORT_' . $now->toDateTimeString();

            $pdf = PDF3::loadView('documents.material_requests.material_requests_pdf', $data)
                ->setOption('footer-right', '[page] of [toPage]');

            return $pdf->download($filename.'.pdf');
        }
        else{
            $data =[
                'mrHeaders'         => $mrHeaders,
                'start_date'        => $request->input('start_date'),
                'finish_date'       => $request->input('end_date'),
                'filterDepartment'  => $filterDepartment,
                'filterStatus'      => $filterStatus,
                'filterMachinery'   => $filterMachinery,
                'type'              => $request->input('type'),
                'department'        => $request->input('department'),
                'machinery'         => $request->input('machinery'),
                'status'            => $request->input('status')
            ];

            return view('documents.material_requests.material_requests_pdf_preview')->with($data);
        }
    }

    public function downloadPdf(MaterialRequestHeader $material_request){
        $downloadPath = public_path(). '/'. $material_request->pdf_path;

        if(file_exists($downloadPath)){
            return response()->download($downloadPath);
        }
        else{
            Session::flash('error', 'File PDF tidak ditemukan!');
            return redirect()->route('admin.material_requests.show', ['material_request' => $material_request->id]);
        }
    }
}