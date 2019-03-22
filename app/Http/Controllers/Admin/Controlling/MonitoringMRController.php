<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 08/08/2018
 * Time: 13:54
 */

namespace App\Http\Controllers\Admin\Controlling;


use App\Exports\MonitoringMRExport;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\MaterialRequestHeader;
use App\Models\Site;
use App\Transformer\Controlling\MonitoringMRTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use PDF3;

class MonitoringMRController extends Controller
{
    public function index(Request $request){
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

        $filterStatus = '3';
        if($request->status != null){
            $filterStatus = $request->status;
        }

        $user = Auth::user();
        $site = $user->employee->site_id;

        $filterSite = $site. "";
        if($request->site != null){
            $filterSite = $request->site;
        }

        $filterPriority = "ALL";
        if($request->priority != null){
            $filterPriority = rawurldecode($request->priority) ;
        }

        // Get all sites
        $sites = Site::orderBy('name')->get();

        $data = [
            'filterDateStart'   => $filterDateStart,
            'filterDateEnd'     => $filterDateEnd,
            'filterStatus'      => $filterStatus,
            'filterSite'        => $filterSite,
            'filterPriority'    => $filterPriority,
            'sites'             => $sites
        ];

        return View('admin.controlling.monitoring_mr.index')->with($data);
    }

    public function getIndex(Request $request){
        $materialRequests = null;

        $start = Carbon::createFromFormat('d M Y', $request->input('date_start'), 'Asia/Jakarta');
        $end = Carbon::createFromFormat('d M Y', $request->input('date_end'), 'Asia/Jakarta');

        $materialRequests = MaterialRequestHeader::whereBetween('date', array($start->toDateTimeString(), $end->toDateTimeString()));

        $status = $request->input('status');
        if($status !== '0'){
            $materialRequests = $materialRequests->where('status_id', $status);
        }

        $site = $request->input('site');
        if($site !== '0'){
            $materialRequests = $materialRequests->where('site_id', $site);
        }

        $priority = $request->input('priority');
        if($priority !== 'ALL'){
            $materialRequests = $materialRequests->where('priority', $priority);
        }

        $materialRequests = $materialRequests->orderBy('date','desc')->get();

        return DataTables::of($materialRequests)
            ->setTransformer(new MonitoringMRTransformer)
            ->make(true);
    }

    public function report(){
        //$departments = Department::orderBy('name')->get();
        $dateStart = Carbon::today()->subMonths(1)->format('d M Y');
        $dateEnd = Carbon::today()->format('d M Y');

        // Get all sites
        $sites = Site::orderBy('name')->get();

        $data = [
            'dateStart'     => $dateStart,
            'dateEnd'       => $dateEnd,
            'sites'         => $sites
        ];

        return View('admin.controlling.monitoring_mr.report')->with($data);
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

        // Filter status
        $status = $request->input('status');
        if($status != '0'){
            $mrHeaders = $mrHeaders->where('status_id', $status);
        }

        $mrHeaders = $mrHeaders->orderByDesc('date')
            ->get();

        // Validate Data
        if($mrHeaders->count() == 0){
            return redirect()->back()->withErrors('Data tidak ditemukan!', 'default')->withInput($request->all());
        }

        $data =[
            'mrHeaders'         => $mrHeaders,
            'start_date'        => $request->input('start_date'),
            'finish_date'       => $request->input('end_date')
        ];

        //return view('documents.material_requests.material_requests_pdf')->with($data);

        $pdf = PDF3::loadView('documents.material_requests.material_requests_status_pdf', $data)
            ->setOption('footer-right', '[page] of [toPage]');
        $now = Carbon::now('Asia/Jakarta');
        $filename = 'MATERIAL_REQUEST_STATUS_REPORT_' . $now->toDateTimeString();

        return $pdf->download($filename. '.pdf');
    }

    public function downloadExcel(Request $request){
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

        $dateStart = $request->input('start_date');
        $dateEnd = $request->input('end_date');

        $start = Carbon::createFromFormat('d M Y', $dateStart, 'Asia/Jakarta');
        $end = Carbon::createFromFormat('d M Y', $dateEnd, 'Asia/Jakarta');

        // Validate date
        if($start->gt($end)){
            return redirect()->back()->withErrors('Dari Tanggal tidak boleh lebih besar dari Sampai Tanggal!', 'default')->withInput($request->all());
        }

        $status = $request->input('status');
        $site = $request->input('site');
        $priority = $request->input('priority');

        $now = Carbon::now('Asia/Jakarta');
        $filename = 'MATERIAL_REQUEST_STATUS_REPORT_'. $now->toDateTimeString(). '.xlsx';

        return (new MonitoringMRExport($dateStart, $dateEnd, $status, $site, $priority))->download($filename);
    }
}