<?php

namespace App\Http\Controllers\Admin;

use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use App\Models\IssuedDocketDetail;
use App\Models\IssuedDocketHeader;
use App\Models\Machinery;
use App\Models\MachineryBrand;
use App\Models\MachineryCategory;
use App\Models\MachineryType;
use App\Transformer\MasterData\MachineryTransformer;
use App\Transformer\MasterData\MachineryTypeTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade as PDF;

class MachineryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('admin.machineries.index');
    }

    public function show(Machinery $machinery){
        return View('admin.machineries.show', compact('machinery'));
    }

    //DataTables

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getIndex()
    {
        $machineries = Machinery::all();
        return DataTables::of($machineries)
            ->setTransformer(new MachineryTransformer())
            ->addIndexColumn()
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $machineryCategories = MachineryCategory::orderBy('name')->get();
        $machineryBrands = MachineryBrand::orderBy('name')->get();

        $data = [
            'machineryCategories'   => $machineryCategories,
            'machineryBrands'       => $machineryBrands
        ];

        return view('admin.machineries.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code'              => 'required|max:45|unique:machineries',
            'description'       => 'max:200',
            'engine_type'       => 'max:100',
            'sn_chasis'         => 'max:100',
            'sn_engine'         => 'max:100',
            'location'          => 'max:30'
        ],[
            'code.unique'       => 'Kode alat berat telah terpakai!'
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors())->withInput($request->all());

        if($request->input('machinery_category') === '-1'){
            return redirect()->back()->withErrors('Pilih kategori alat berat!', 'default')->withInput($request->all());
        }

        if($request->input('machinery_brand') === '-1'){
            return redirect()->back()->withErrors('Pilih merek alat berat!', 'default')->withInput($request->all());
        }

        $user = Auth::user();
        $dateTimeNow = Carbon::now('Asia/Jakarta');

        $machinery = Machinery::create([
            'code'                  => $request->input('code'),
            'category_id'           => $request->input('machinery_category'),
            'brand_id'              => $request->input('machinery_brand'),
            'type'                  => $request->input('machinery_type'),
            'status_id'             => $request->input('status'),
            'sn_chasis'             => $request->input('sn_chasis'),
            'engine_model'          => $request->input('engine_type'),
            'sn_engine'             => $request->input('sn_engine'),
            'production_year'       => $request->input('production_year'),
            'location'              => $request->input('location'),
            'description'           => $request->input('description'),
            'is_synced'             => false,
            'created_on'            => 'online',
            'created_by'            => $user->id,
            'created_at'            => $dateTimeNow->toDateTimeString(),
            'updated_by'            => $user->id
        ]);

        if($request->filled('purchase_date')){
            $purchaseDate = Carbon::createFromFormat('d M Y', $request->input('purchase_date'), 'Asia/Jakarta');
            $machinery->purchase_date = $purchaseDate->toDateString();
            $machinery->save();
        }

        Session::flash('message', 'Berhasil membuat data alat berat baru!');

        return redirect()->route('admin.machineries.show', ['machinery' => $machinery]);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
//    public function show(User $user)
//    {
//        return view('admin.users.show', ['user' => $user]);
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Machinery $machinery
     * @return \Illuminate\Http\Response
     */
    public function edit(Machinery $machinery)
    {
        $machineryCategories = MachineryCategory::orderBy('name')->get();
        $machineryBrands = MachineryBrand::orderBy('name')->get();

        $purchaseDate = null;
        if(!empty($machinery->purchase_date)){
            $purchaseDate = Carbon::parse($machinery->purchase_date)->format('d M Y');
        }

        $data = [
            'machinery'             => $machinery,
            'machineryCategories'   => $machineryCategories,
            'machineryBrands'       => $machineryBrands,
            'purchaseDate'          => $purchaseDate
        ];

        return view('admin.machineries.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Machinery $machinery
     * @return mixed
     */
    public function update(Request $request, Machinery $machinery)
    {
        $validator = Validator::make($request->all(), [
            'description'       => 'max:200',
            'engine_type'       => 'max:100',
            'sn_chasis'         => 'max:100',
            'sn_engine'         => 'max:100',
            'location'          => 'max:30'
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());

        $user = Auth::user();
        $dateTimeNow = Carbon::now('Asia/Jakarta');

        $machinery->type = Input::get('machinery_type');
        $machinery->category_id = $request->input('machinery_category');
        $machinery->brand_id = $request->input('machinery_brand');
        $machinery->description = $request->input('description');
        $machinery->engine_model = $request->input('engine_type');
        $machinery->sn_chasis = $request->input('sn_chasis');
        $machinery->sn_engine = $request->input('sn_engine');
        $machinery->location = $request->input('location');
        $machinery->production_year = $request->input('production_year');
        $machinery->is_synced = false;
        $machinery->status_id = $request->input('status');
        $machinery->updated_at = $dateTimeNow->toDateTimeString();
        $machinery->updated_by = $user->id;

        if($request->filled('purchase_date')){
            $purchaseDate = Carbon::createFromFormat('d M Y', $request->input('purchase_date'), 'Asia/Jakarta');
            $machinery->purchase_date = $purchaseDate->toDateString();
        }

        $machinery->save();

        Session::flash('message', 'Berhasil mengubah data alat berat!');

        return redirect()->route('admin.machineries.edit', ['machinery' => $machinery->id]);
    }

    public function destroy(Request $request)
    {
        try{
            $machinery = Machinery::find($request->input('id'));
            $machinery->delete();

            Session::flash('message', 'Berhasil menghapus data alat berat '. $machinery->code);

            return Response::json(array('success' => 'VALID'));
        }
        catch(\Exception $ex){
            return Response::json(array('errors' => 'INVALID'));
        }
    }

    public function getMachineries(Request $request){
        $term = trim($request->q);
        $machineries = Machinery::where('code', 'LIKE', '%'. $term. '%')
            ->where('status_id', 6)
            ->get();

        $formatted_tags = [];

        foreach ($machineries as $machinery) {
            $formatted_tags[] = ['id' => $machinery->id, 'text' => $machinery->code];
        }

        return Response::json($formatted_tags);
    }

    public function getDetailedMachineries(Request $request){
        $term = trim($request->q);
        $machineries = Machinery::where('code', 'LIKE', '%'. $term. '%')
            ->where('status_id', 6)
            ->get();

        $formatted_tags = [];


        foreach ($machineries as $machinery) {
            $id = $machinery->id. "#". $machinery->engine_model. "#". $machinery->sn_chasis. "#". $machinery->sn_engine;
            $formatted_tags[] = ['id' => $id, 'text' => $machinery->code];
        }

        return Response::json($formatted_tags);
    }

    public function report(){
        $machinery = null;
        if(!empty(request()->machinery)){
            $machinery = Machinery::find(request()->machinery);
        }

        return View('admin.machineries.report', compact('machinery'));
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

        // Validate machinery
        if(!$request->filled('machinery') && !$request->filled('machinery_id')){
            return redirect()->back()->withErrors('Mohon pilih unit alat berat!', 'default')->withInput($request->all());
        }

        // Validate date
        if($start->gt($end)){
            return redirect()->back()->withErrors('Dari Tanggal tidak boleh lebih besar dari Sampai Tanggal!', 'default')->withInput($request->all());
        }

        $start = $start->addDays(-1);
        $end = $end->addDays(1);

        $idHeaders = IssuedDocketHeader::whereBetween('date', array($start->toDateTimeString(), $end->toDateTimeString()))
                        ->where('type', 1);

        // Filter machinery
        $machineryId = '-1';
        if($request->filled('machinery')){
            $machineryId = $request->input('machinery');
        }
        else{
            $machineryId = $request->input('machinery_id');
        }

        $machinery = Machinery::find($machineryId);
        $idHeaders = $idHeaders->where('unit_id', $machineryId)
                ->orderByDesc('date')
                ->get();

        $idDetailFuels = IssuedDocketDetail::where('machinery_id', $machineryId)->get();

        // Validate Data
        if($idHeaders->count() == 0 && $idDetailFuels->count() == 0){
            return redirect()->back()->withErrors('Data tidak ditemukan!', 'default')->withInput($request->all());
        }

        // Count total cost
        $totalValue = 0;
        if($idDetailFuels->count() > 0){
            foreach($idHeaders as $header){
                foreach($header->issued_docket_details as $detail){
                    $value = $detail->quantity * $detail->item->value;
                    $totalValue += $value;
                }
            }
        }

        if($idDetailFuels->count() > 0){
            foreach($idDetailFuels as $detailFuel){
                $value = $detailFuel->quantity * $detailFuel->item->value;
                $totalValue += $value;
            }
        }

        $data =[
            'idHeaders'         => $idHeaders,
            'idDetailFuels'     => $idDetailFuels,
            'start_date'        => $request->input('start_date'),
            'finish_date'       => $request->input('end_date'),
            'machinery'         => $machinery,
            'total'             => number_format($totalValue, 2, ",", ".")
        ];

        return view('documents.machineries.machinery_cost_pdf')->with($data);

//        $pdf = PDF::loadView('documents.machineries.machinery_cost_pdf', $data)
//            ->setPaper('a4', 'portrait');
//        $now = Carbon::now('Asia/Jakarta');
//        $filename = 'ALAT_BERAT_COST_REPORT_' . $now->toDateTimeString();
//        $pdf->setOptions(["isPhpEnabled"=>true]);
//
//        return $pdf->download($filename.'.pdf');
    }
}
