<?php

namespace App\Http\Controllers\Admin;

use App\Charts\MaterialRequestApprovalChart;
use App\Models\ApprovalMaterialRequest;
use App\Models\ApprovalPurchaseOrder;
use App\Models\ApprovalPurchaseRequest;
use App\Models\ApprovalRule;
use App\Models\AssignmentMaterialRequest;
use App\Models\AssignmentPurchaseRequest;
use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use App\Models\DeliveryOrderHeader;
use App\Models\ItemStockNotification;
use App\Models\MaterialRequestHeader;
use App\Models\PreferenceCompany;
use App\Models\PurchaseOrderHeader;
use App\Models\PurchaseRequestHeader;
use App\Models\Warehouse;
use App\Transformer\Purchasing\PurchaseOrderHeaderTransformer;
use App\Transformer\Purchasing\PurchaseRequestHeaderTransformer;
use Arcanedev\LogViewer\Entities\Log;
use Arcanedev\LogViewer\Entities\LogEntry;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Transformer\Inventory\MaterialRequestHeaderTransformer;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Lava;

class DashboardController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $counts = [
            'users' => \DB::table('users')->count(),
            'users_unconfirmed' => \DB::table('users')->where('confirmed', false)->count(),
            'users_inactive' => \DB::table('users')->where('active', false)->count(),
            'protected_pages' => 0,
        ];

        $start = Carbon::now('Asia/Jakarta')->subMonths(6);
        $end = Carbon::now('Asia/Jakarta');

        // Generate MR Part status chart
        $mrPartDatas = DB::table('material_request_headers')
            ->select(DB::raw('SUM(status_id = 13) as rejected, '.
                'SUM(status_id = 11) as manual_closed, '.
                'SUM(status_id = 4) as finished, '.
                'SUM(is_pr_created = 0 AND status_id = 3) as unprocessed, '.
                'SUM(is_pr_created = 1 AND status_id = 3) as processed, '.
                'YEAR(date) as year, '.
                'MONTHNAME(date) as month_name'))
            ->where('purpose', 'stock')
            ->where(function ($q) {
                $q->where('priority', 'Part - P1')
                    ->orWhere('priority', 'Part - P2')
                    ->orWhere('priority', 'Part - P3');
            })
            ->whereBetween('date', array($start->toDateTimeString(), $end->toDateTimeString()))
            ->orderBy('date')
            ->groupBy(DB::raw('MONTHNAME(date)'))
            ->groupBy(DB::raw('YEAR(date)'))
            ->get();

        $mrPartChart = \Lava::Datatable();

        $mrPartChart->addStringColumn('Month')
            ->addNumberColumn('Ditolak')
            ->addRoleColumn('string', 'annotation')
            ->addNumberColumn('Tutup Manual')
            ->addRoleColumn('string', 'annotation')
            ->addNumberColumn('Open - Belum Proses PR')
            ->addRoleColumn('string', 'annotation')
            ->addNumberColumn('Open - Sudah Proses PR')
            ->addRoleColumn('string', 'annotation')
            ->addNumberColumn('Selesai')
            ->addRoleColumn('string', 'annotation');

        foreach ($mrPartDatas as $mrData){
            $mrPartChart->addRow([$mrData->month_name. ' '. $mrData->year,
                $mrData->rejected,
                $mrData->rejected,
                $mrData->manual_closed,
                $mrData->manual_closed,
                $mrData->unprocessed,
                $mrData->unprocessed,
                $mrData->processed,
                $mrData->processed,
                $mrData->finished,
                $mrData->finished]);
        }

        \Lava::ColumnChart('MRPartDatas', $mrPartChart,[
            'height' => 500,
            'colors' => ['red','#E9806E','#C0C781','#78BC61','blue',],
            'events' => ['select'=>'getMrNonPartDetails']
        ]);

        // Generate MR Non-Part status chart
        $mrNonPartDatas = DB::table('material_request_headers')
            ->select(DB::raw('SUM(status_id = 13) as rejected, '.
                'SUM(status_id = 11) as manual_closed, '.
                'SUM(status_id = 4) as finished, '.
                'SUM(is_pr_created = 0 AND status_id = 3) as unprocessed, '.
                'SUM(is_pr_created = 1 AND status_id = 3) as processed, '.
                'YEAR(date) as year, '.
                'MONTHNAME(date) as month_name'))
            ->where('purpose', 'stock')
            ->where(function ($q) {
                $q->where('priority', 'Non-Part - P1')
                    ->orWhere('priority', 'Non-Part - P2')
                    ->orWhere('priority', 'Non-Part - P3');
            })
            ->whereBetween('date', array($start->toDateTimeString(), $end->toDateTimeString()))
            ->orderBy('date')
            ->groupBy(DB::raw('MONTHNAME(date)'))
            ->groupBy(DB::raw('YEAR(date)'))
            ->get();

        $mrNonPartChart = \Lava::Datatable();

        $mrNonPartChart->addStringColumn('Month')
            ->addNumberColumn('Ditolak')
            ->addRoleColumn('string', 'annotation')
            ->addNumberColumn('Tutup Manual')
            ->addRoleColumn('string', 'annotation')
            ->addNumberColumn('Open - Belum Proses PR')
            ->addRoleColumn('string', 'annotation')
            ->addNumberColumn('Open - Sudah Proses PR')
            ->addRoleColumn('string', 'annotation')
            ->addNumberColumn('Selesai')
            ->addRoleColumn('string', 'annotation');

        foreach ($mrNonPartDatas as $mrData){
            $mrNonPartChart->addRow([$mrData->month_name. ' '. $mrData->year,
                $mrData->rejected,
                $mrData->rejected,
                $mrData->manual_closed,
                $mrData->manual_closed,
                $mrData->unprocessed,
                $mrData->unprocessed,
                $mrData->processed,
                $mrData->processed,
                $mrData->finished,
                $mrData->finished]);
        }

        \Lava::ColumnChart('MRNonPartDatas', $mrNonPartChart,[
            'height' => 500,
            'colors' => ['red','#E9806E','#C0C781','#78BC61','blue',],
            'events' => ['select'=>'getMrPartDetails']
        ]);

        // Generate PR status chart
        $rawSql = 'SUM(status_id = 11) as manual_closed, '.
            'SUM(is_all_poed = 0 AND status_id = 3) as unpoed, '.
            'SUM(is_all_poed = 2 AND status_id = 3) as partial_poed, '.
            'SUM(is_all_poed = 1 AND status_id = 3) as all_poed, '.
            'SUM(status_id = 4) as finished, '.
            'YEAR(date) as year, '.
            'MONTHNAME(date) as month_name';

        $prDatas = DB::table('purchase_request_headers')
            ->select(DB::raw($rawSql))
            ->whereBetween('date', array($start->toDateTimeString(), $end->toDateTimeString()))
            ->orderBy('date')
            ->groupBy(DB::raw('MONTHNAME(date)'))
            ->groupBy(DB::raw('YEAR(date)'))
            ->get();

        $prChart = \Lava::Datatable();


        $prChart->addStringColumn('Month')
            ->addNumberColumn('Tutup Manual')
            ->addRoleColumn('string', 'annotation')
            ->addNumberColumn('Open - Belum Proses PO')
            ->addRoleColumn('string', 'annotation')
            ->addNumberColumn('Open - Sebagian Proses PO')
            ->addRoleColumn('string', 'annotation')
            ->addNumberColumn('Open - Sudah Proses PO')
            ->addRoleColumn('string', 'annotation')
            ->addNumberColumn('Selesai')
            ->addRoleColumn('string', 'annotation');

        foreach ($prDatas as $prData){
            $prChart->addRow([$prData->month_name. ' '. $prData->year,
                $prData->manual_closed,
                $prData->manual_closed,
                $prData->unpoed,
                $prData->unpoed,
                $prData->partial_poed,
                $prData->partial_poed,
                $prData->all_poed,
                $prData->all_poed,
                $prData->finished,
                $prData->finished]);
        }

        \Lava::ColumnChart('PRDatas', $prChart,[
            'height' => 500,
            'colors' => ['#E9806E','#C59B76','#C0C781','#78BC61','blue'],
            'events' => ['select'=>'getPrDetails']
        ]);

        // Generate PO status chart

//        $rawSql = 'SUM(status_id = 13) as rejected, '.
//            'SUM(status_id = 11) as manual_closed, '.
//            'SUM(status_id = 4) as finished, '.
//            'SUM(is_all_received = 0 AND status_id = 3) as unreceived, '.
//            'SUM(is_all_received = 2 AND status_id = 3) as partial_received, '.
//            'SUM(is_all_received = 1 AND status_id = 3) as all_received, '.
//            'SUM(is_all_invoiced = 0 AND status_id = 3) as uninvoiced, '.
//            'SUM(is_all_invoiced = 2 AND status_id = 3) as partial_invoiced, '.
//            'SUM(is_all_invoiced = 1 AND status_id = 3) as all_invoiced, '.
//            'MONTHNAME(date) as month_name';

        $rawSql = 'SUM(status_id = 13) as rejected, '.
            'SUM(status_id = 11) as manual_closed, '.
            'SUM(status_id = 3) as open, '.
            'SUM(status_id = 4) as finished, '.
            'YEAR(date) as year, '.
            'MONTHNAME(date) as month_name';

        $poDatas = DB::table('purchase_order_headers')
            ->select(DB::raw($rawSql))
            ->whereBetween('date', array($start->toDateTimeString(), $end->toDateTimeString()))
            ->orderBy('date')
            ->groupBy(DB::raw('MONTHNAME(date)'))
            ->groupBy(DB::raw('YEAR(date)'))
            ->get();

        $poChart = \Lava::Datatable();

//        $poChart->addStringColumn('Month')
//            ->addNumberColumn('Ditolak')
//            ->addNumberColumn('Tutup Manual')
//            ->addNumberColumn('Selesai')
//            ->addNumberColumn('Open - Belum Proses GR')
//            ->addNumberColumn('Open - Sebagian Proses GR')
//            ->addNumberColumn('Open - Sudah Proses GR')
//            ->addNumberColumn('Open - Belum Proses Invoice')
//            ->addNumberColumn('Open - Sebagian Proses Invoice')
//            ->addNumberColumn('Open - Sudah Proses Invoice');

        $poChart->addStringColumn('Month')
            ->addNumberColumn('Ditolak')
            ->addRoleColumn('string', 'annotation')
            ->addNumberColumn('Tutup Manual')
            ->addRoleColumn('string', 'annotation')
            ->addNumberColumn('Open')
            ->addRoleColumn('string', 'annotation')
            ->addNumberColumn('Selesai')
            ->addRoleColumn('string', 'annotation');

//        foreach ($poDatas as $poData){
//            $poChart->addRow([$poData->month_name,
//                $poData->rejected,
//                $poData->manual_closed,
//                $poData->finished,
//                $poData->unreceived,
//                $poData->partial_received,
//                $poData->all_received,
//                $poData->uninvoiced,
//                $poData->partial_invoiced,
//                $poData->all_invoiced]);
//        }

        foreach ($poDatas as $poData){
            $poChart->addRow([$poData->month_name. ' '. $poData->year,
                $poData->rejected,
                $poData->rejected,
                $poData->manual_closed,
                $poData->manual_closed,
                $poData->open,
                $poData->open,
                $poData->finished,
                $poData->finished]);
        }

        \Lava::ColumnChart('PODatas', $poChart,[
            'height' => 500,
            'colors' => ['red','#E9806E','#78BC61','blue'],
            'events' => ['select'=>'getPoDetails']
        ]);

        // Get active SJ
        $deliveryHeaderActiveCount = DeliveryOrderHeader::where('status_id', 3)->get()->count();

        $user = Auth::user();
        $currentRole = $user->roles->pluck('id')->first();

        // Get PR priority limit date warnings
        $prPartWarnings = new Collection();
        $prNonPartWarnings = new Collection();
        $prPartWarningCounter = 0;
        $prNonPartWarningCounter = 0;
        if($currentRole === 1 ||
            $currentRole === 15 ||
            $currentRole === 14 ||
            $currentRole === 13 ||
            $currentRole === 3 ||
            $currentRole === 4 ||
            $currentRole === 8 ||
            $currentRole === 10 ||
            $currentRole === 12 ||
            $currentRole === 5){

            // PR with Part priority
            $prPartHeaders = PurchaseRequestHeader::where('status_id', 3)
                ->where(function ($q) {
                    $q->where('priority', 'Part - P1')
                        ->orWhere('priority', 'Part - P2')
                        ->orWhere('priority', 'Part - P3');
                })
                ->orderByDesc('date')
                ->get();

            $prPartWarningCounter = 0;
            foreach ($prPartHeaders as $header){
                if($header->material_request_header->status_id === 3){
                    if($header->priority_expired){
                        if($prPartWarningCounter < 10) $prPartWarnings->add($header);
                        $prPartWarningCounter++;
                    }
                    else{
                        if($header->day_left <= 3){
                            if($prPartWarningCounter < 10)$prPartWarnings->add($header);
                            $prPartWarningCounter++;
                        }
                    }
                }
            }

            // PR with Non-Part priority
            $prNonPartHeaders = PurchaseRequestHeader::where('status_id', 3)
                ->where(function ($q) {
                    $q->where('priority', 'Non-Part - P1')
                        ->orWhere('priority', 'Non-Part - P2')
                        ->orWhere('priority', 'Non-Part - P3');
                })
                ->orderByDesc('date')
                ->get();

            $prNonPartWarningCounter = 0;
            foreach ($prNonPartHeaders as $header){
                if($header->material_request_header->status_id === 3){
                    if($header->priority_expired){
                        if($prNonPartWarningCounter < 10) $prNonPartWarnings->add($header);
                        $prNonPartWarningCounter++;
                    }
                    else{
                        if($header->day_left <= 3){
                            if($prNonPartWarningCounter < 10)$prNonPartWarnings->add($header);
                            $prNonPartWarningCounter++;
                        }
                    }
                }
            }
        }

        $prHeaders = PurchaseRequestHeader::where('status_id', 3)
            ->get();

        // Get all active PR
        $prActiveCount = $prHeaders->count();

        // Check Approval Feature
        $isMrApprover = false;
        $isPoApprover = false;
        $preference = PreferenceCompany::find(1);
        $approvalMaterialRequests = new Collection();
        $approvalPurchaseOrders = new Collection();
        $approvalMrCount = 0;
        $approvalPoCount = 0;
        $approvalIdx = 0;
        if($preference->approval_setting === 1){
            // Get MR approval notifications
            $mrHeaders = MaterialRequestHeader::where('status_id', 3)
                ->where('is_approved', 0)
                ->orderBy('date', 'DESC')
                ->get();
            $approvalMrCount = $mrHeaders->count();
            if(ApprovalRule::where('document_id', 4)->where('user_id', $user->id)->exists()){
                $isMrApprover = true;
                foreach ($mrHeaders as $header){
                    if($approvalIdx === 5) break;
                    if(!ApprovalMaterialRequest::where('material_request_id', $header->id)->where('user_id', $user->id)->exists()){
                        $approvalMaterialRequests->add($header);
                        $approvalIdx++;
                    }
                }
            }

            $approvalIdx = 0;

            // Get PO approval notifications
            $poHeaders = PurchaseOrderHeader::where('status_id', 3)
                ->where('is_approved', 0)
                ->orderBy('date', 'DESC')
                ->get();
            $approvalPoCount = $poHeaders->count();
            if(ApprovalRule::where('document_id', 4)->where('user_id', $user->id)->exists()){
                $isPoApprover = true;
                foreach ($poHeaders as $header){
                    if($approvalIdx === 5) break;
                    if(!ApprovalPurchaseOrder::where('purchase_order_id', $header->id)->where('user_id', $user->id)->exists()){
                        $approvalPurchaseOrders->add($header);
                        $approvalIdx++;
                    }
                }
            }
        }

        // Get item stock warning notification
        $stockWarnings = null;
        $warningCount = 0;
        if($currentRole === 1 ||
            $currentRole === 15 ||
            $currentRole === 14 ||
            $currentRole === 13 ||
            $currentRole === 3 ||
            $currentRole === 7 ||
            $currentRole === 9 ||
            $currentRole === 17 ||
            $currentRole === 18){

            $site = $user->employee->site;
            $warehouseIds = array();
            if($site->id === 1){
                $warehouseAll = Warehouse::all();
                foreach($warehouseAll as $warehouse){
                    array_push($warehouseIds, $warehouse->id);
                }
            }
            else{
                foreach($site->warehouses as $warehouse){
                    array_push($warehouseIds, $warehouse->id);
                }
            }

            $stockWarnings = ItemStockNotification::with('item', 'item_stock')
                ->whereIn('warehouse_id', $warehouseIds)
                ->whereHas('item_stock', function($query){
                    $query->whereColumn('item_stocks.stock', '<=', 'item_stocks.stock_min');
                });

            $warningCount = $stockWarnings->count();
            $stockWarnings = $stockWarnings->take(5)->get();

        }

//        foreach ($stockWarnings as $warning){
//            dd($warning->item_stock->stock);
//        }

        // Get new MR/PR document to assign for purchasing manager
        $newMrToAssignList = null;
        $newMrToAssignCount = 0;
        $newPrToAssignList = null;
        $newPrToAssignCount = 0;
        $preference = PreferenceCompany::find(1);
        $assignerRoleId = intval($preference->assigner_role_id);
        $isAssignerRole = false;
        if($currentRole === $assignerRoleId || $currentRole === 1 || $currentRole === 3){
            $isAssignerRole = true;
            $newMrToAssignList = MaterialRequestHeader::where('status_id', 3)
                ->where('is_approved', 1)
                ->where('is_pr_created', 0)
                ->whereNull('assigned_to')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $newMrToAssignCount = DB::table('material_request_headers')
                ->where('status_id', 3)
                ->where('is_approved', 1)
                ->where('is_pr_created', 0)
                ->whereNull('assigned_to')
                ->count();

            $newPrToAssignList = PurchaseRequestHeader::where('status_id', 3)
                ->where('is_approved', 1)
                ->where('is_all_poed', 0)
                ->whereNull('assigned_to')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $newPrToAssignCount = DB::table('purchase_request_headers')
                ->where('status_id', 3)
                ->where('is_approved', 1)
                ->where('is_all_poed', 0)
                ->whereNull('assigned_to')
                ->count();
        }

        // Get assignment notifications for purchasing staff
        $assignmentMrList = null;
        $assignmentMrCount = 0;
        $assignmentPrList = null;
        $assignmentPrCount = 0;
        $isAssignedRole = false;
        if($currentRole === 5 || $currentRole === 1 || $currentRole === 3){
            $isAssignedRole = true;

            $assignmentMrList = AssignmentMaterialRequest::where('status_id', 17)
                ->where('assigned_user_id', $user->id)
                ->orderBy('created_at')
                ->take(5)
                ->get();

            $assignmentMrCount = DB::table('assignment_material_requests')
                ->where('status_id', 17)
                ->where('assigned_user_id', $user->id)
                ->count();

            $assignmentPrList = AssignmentPurchaseRequest::where('status_id', 17)
                ->where('assigned_user_id', $user->id)
                ->orderBy('created_at')
                ->take(5)
                ->get();

            $assignmentPrCount = DB::table('assignment_purchase_requests')
                ->where('status_id', 17)
                ->where('assigned_user_id', $user->id)
                ->count();
        }

        $data = [
            'counts'                    => $counts,
            'prActiveCount'             => $prActiveCount,
            'deliveryHeaderActiveCount' => $deliveryHeaderActiveCount,
            'prPartWarnings'            => $prPartWarnings,
            'prNonPartWarnings'         => $prNonPartWarnings,
            'prPartWarningCounter'      => $prPartWarningCounter,
            'prNonPartWarningCounter'   => $prNonPartWarningCounter,
            'approvalFeatured'          => $preference->approval_setting,
            'isMrApprover'              => $isMrApprover,
            'isPoApprover'              => $isPoApprover,
            'approvalMaterialRequests'  => $approvalMaterialRequests,
            'approvalMrCount'           => $approvalMrCount,
            'approvalPurchaseOrders'    => $approvalPurchaseOrders,
            'approvalPoCount'           => $approvalPoCount,
            'warningCount'              => $warningCount,
            'stockWarnings'             => $stockWarnings,
            'isAssignerRole'            => $isAssignerRole,
            'newMrToAssignList'         => $newMrToAssignList,
            'newPrToAssignList'         => $newPrToAssignList,
            'newMrToAssignCount'        => $newMrToAssignCount,
            'newPrToAssignCount'        => $newPrToAssignCount,
            'isAssignedRole'            => $isAssignedRole,
            'assignmentMrList'          => $assignmentMrList,
            'assignmentPrList'          => $assignmentPrList,
            'assignmentMrCount'         => $assignmentMrCount,
            'assignmentPrCount'         => $assignmentPrCount
        ];

        return view('admin.dashboard')->with($data);
    }


    public function getLogChartData(Request $request)
    {
        \Validator::make($request->all(), [
            'start' => 'required|date|before_or_equal:now',
            'end' => 'required|date|after_or_equal:start',
        ])->validate();

        $start = new Carbon($request->get('start'));
        $end = new Carbon($request->get('end'));

        $dates = collect(\LogViewer::dates())->filter(function ($value, $key) use ($start, $end) {
            $value = new Carbon($value);
            return $value->timestamp >= $start->timestamp && $value->timestamp <= $end->timestamp;
        });


        $levels = \LogViewer::levels();

        $data = [];

        while ($start->diffInDays($end, false) >= 0) {

            foreach ($levels as $level) {
                $data[$level][$start->format('Y-m-d')] = 0;
            }

            if ($dates->contains($start->format('Y-m-d'))) {
                /** @var  $log Log */
                $logs = \LogViewer::get($start->format('Y-m-d'));

                /** @var  $log LogEntry */
                foreach ($logs->entries() as $log) {
                    $data[$log->level][$log->datetime->format($start->format('Y-m-d'))] += 1;
                }
            }

            $start->addDay();
        }

        return response($data);
    }

    public function getRegistrationChartData()
    {

        $data = [
            'registration_form' => User::whereDoesntHave('providers')->count(),
            'google' => User::whereHas('providers', function ($query) {
                $query->where('provider', 'google');
            })->count(),
            'facebook' => User::whereHas('providers', function ($query) {
                $query->where('provider', 'facebook');
            })->count(),
            'twitter' => User::whereHas('providers', function ($query) {
                $query->where('provider', 'twitter');
            })->count(),
        ];

        return response($data);
    }

    public function indexMr(Request $request){
        $status = $request->status;
        $month = $request->month;
        $type = $request->type;

        if($status === 0){
            $filter = "Ditolak";
        }
        else if($status === 1){
            $filter = "Tutup Manual";
        }
        else if($status === 2){
            $filter = "Open - Belum Proses PR";
        }
        else if($status === 3){
            $filter = "Open - Sudah Proses PR";
        }
        else{
            $filter = "Selesai";
        }

        $data = [
            'month'     => $month,
            'filter'    => $filter,
            'type'      => $type,
            'status'    => $status
        ];

        return View('admin.inventory.material_requests.index_chart')->with($data);
    }

    public function getIndexMR(Request $request){
        $statusTemp = $request->input('status');
        $status = '0';
        $isCreated = 9;

        //Kasus ditolak
        if($statusTemp == 0){
            $status = 13;
        }
        //Kasus Tutup Manual
        else if($statusTemp == 1){
            $status = 11;
        }
        //Kasus Open - Belum Proses PR
        else if($statusTemp == 2){
            $status = 3;
            $isCreated = 0;
        }
        //Kasus Open - Sudah Proses PR
        else if($statusTemp == 3){
            $status = 3;
            $isCreated = 1;
        }
        //Kasus Selesai
        else if($statusTemp == 4){
            $status = 4;
        }

        $legend = explode(' ', $request->input('month'));

        $startStr = 'first day of ' . $legend[0] . ' ' . $legend[1];
        $finishStr = 'last day of ' . $legend[0] . ' ' . $legend[1];

        $start = new Carbon($startStr);
        $finish = new Carbon($finishStr);

        $type = $request->input('type');
        if($type == 0) {
            if ($isCreated == 9) {
                $mrPartDatas = MaterialRequestHeader::where('status_id', $status)
                    ->where('purpose', 'stock')
                    ->where(function ($q) {
                        $q->where('priority', 'Part - P1')
                            ->orWhere('priority', 'Part - P2')
                            ->orWhere('priority', 'Part - P3');
                    })
                    ->whereBetween('date', array($start->toDateTimeString(), $finish->toDateTimeString()))
                    ->orderByDesc('date');
            } else {
                $mrPartDatas = MaterialRequestHeader::where('status_id', $status)
                    ->where('purpose', 'stock')
                    ->where(function ($q) {
                        $q->where('priority', 'Part - P1')
                            ->orWhere('priority', 'Part - P2')
                            ->orWhere('priority', 'Part - P3');
                    })
                    ->where('is_pr_created', $isCreated)
                    ->whereBetween('date', array($start->toDateTimeString(), $finish->toDateTimeString()))
                    ->orderByDesc('date');
            }
        }
        else{
            if($isCreated == 9){
                $mrPartDatas = MaterialRequestHeader::where('status_id', $status)
                    ->where('purpose', 'stock')
                    ->where(function ($q) {
                        $q->where('priority', 'Non-Part - P1')
                            ->orWhere('priority', 'Non-Part - P2')
                            ->orWhere('priority', 'Non-Part - P3');
                    })
                    ->whereBetween('date', array($start->toDateTimeString(), $finish->toDateTimeString()))
                    ->orderByDesc('date');
            }
            else{
                $mrPartDatas = MaterialRequestHeader::where('status_id', $status)
                    ->where('purpose', 'stock')
                    ->where(function ($q) {
                        $q->where('priority', 'Non-Part - P1')
                            ->orWhere('priority', 'Non-Part - P2')
                            ->orWhere('priority', 'Non-Part - P3');
                    })
                    ->where('is_pr_created', $isCreated)
                    ->whereBetween('date', array($start->toDateTimeString(), $finish->toDateTimeString()))
                    ->orderByDesc('date');
            }
        }
        $mode = 'default';

        return DataTables::of($mrPartDatas)
            ->setTransformer(new MaterialRequestHeaderTransformer($mode))
            ->make(true);
    }

    public function indexPr(Request $request){
        $status = $request->status;
        $month = $request->month;

        if($status === 0){
            $filter = "Tutup Manual";
        }
        else if($status === 1){
            $filter = "Open - Belum Proses PO";
        }
        else if($status === 2){
            $filter = "Open - Sebagian Proses PO";
        }
        else if($status === 3){
            $filter = "Open - Sudah Proses PO";
        }
        else{
            $filter = "Selesai";
        }

        $data = [
            'month'     => $month,
            'filter'    => $filter,
            'status'    => $status
        ];

        return View('admin.purchasing.purchase_requests.index_chart')->with($data);
    }

    public function getIndexPR(Request $request){
        $statusTemp = $request->input('status');
        $status = '0';
        $isAllPoed = 9;

        //Kasus Tutup Manual
        if($statusTemp == 0){
            $status = 11;
        }
        //Kasus Open - Belum Proses PO
        else if($statusTemp == 1){
            $status = 3;
            $isAllPoed = 0;
        }
        //Kasus Open - Sudah Proses PO Sebagian
        else if($statusTemp == 2){
            $status = 3;
            $isAllPoed = 2;
        }
        //Kasus Open - Sudah Semua di-PO
        else if($statusTemp == 3){
            $status = 3;
            $isAllPoed = 1;
        }
        //Kasus Selesai
        else if($statusTemp == 4){
            $status = 4;
        }

        $legend = explode(' ', $request->input('month'));

        $startStr = 'first day of ' . $legend[0] . ' ' . $legend[1];
        $finishStr = 'last day of ' . $legend[0] . ' ' . $legend[1];

        $start = new Carbon($startStr);
        $finish = new Carbon($finishStr);

        $purchaseRequests = PurchaseRequestHeader::where('status_id', $status)
            ->whereBetween('date', array($start->toDateTimeString(), $finish->toDateTimeString()));

        if($isAllPoed !== 9){
            $purchaseRequests = $purchaseRequests->where('is_all_poed', $isAllPoed);
        }

        return DataTables::of($purchaseRequests)
            ->setTransformer(new PurchaseRequestHeaderTransformer("default"))
            ->make(true);
    }

    public function indexPo(Request $request){
        $status = $request->status;
        $month = $request->month;

        if($status === 0){
            $filter = "Ditolak";
        }
        else if($status === 1){
            $filter = "Tutup Manual";
        }
        else if($status === 2){
            $filter = "Open";
        }
        else{
            $filter = "Selesai";
        }

        $data = [
            'month'     => $month,
            'filter'    => $filter,
            'status'    => $status
        ];

        return View('admin.purchasing.purchase_orders.index_chart')->with($data);
    }

    public function getIndexPO(Request $request){
        $statusTemp = $request->input('status');
        $status = '0';

        // Kasus Ditolak
        if($statusTemp == 0){
            $status = 13;
        }
        // Kasus Tutup Manual
        else if($statusTemp == 1){
            $status = 11;
        }
        // Kasus Open
        else if($statusTemp == 2){
            $status = 3;
        }
        // Kasus Selesai
        else{
            $status = 4;
        }

        $legend = explode(' ', $request->input('month'));

        $startStr = 'first day of ' . $legend[0] . ' ' . $legend[1];
        $finishStr = 'last day of ' . $legend[0] . ' ' . $legend[1];

        $start = new Carbon($startStr);
        $finish = new Carbon($finishStr);

        $purchaseOrders = PurchaseOrderHeader::where('status_id', $status)
            ->whereBetween('date', array($start->toDateTimeString(), $finish->toDateTimeString()));

        return DataTables::of($purchaseOrders)
            ->setTransformer(new PurchaseOrderHeaderTransformer("default"))
            ->make(true);
    }
}
