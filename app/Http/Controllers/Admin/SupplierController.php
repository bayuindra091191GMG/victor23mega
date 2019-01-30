<?php

namespace App\Http\Controllers\Admin;


use App\Exports\SupplierExport;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Site;
use App\Models\Supplier;
use App\Transformer\MasterData\EmployeeTransformer;
use App\Transformer\MasterData\SupplierTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
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
        return view('admin.suppliers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        if($user->roles->pluck('id')[0] == 1 ||
            $user->roles->pluck('id')[0] == 3 ||
            $user->roles->pluck('id')[0] == 14 ||
            $user->roles->pluck('id')[0] == 12 ||
            $user->roles->pluck('id')[0] == 15 ||
            $user->roles->pluck('id')[0] == 13){
            return view('admin.suppliers.create');
        }
        else{
            return redirect()->route('admin.suppliers');
        }
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
            'code'                  => 'max:30',
            'name'                  => 'required|max:100',
            'category'              => 'max:100',
            'email'                 => 'nullable|email|max:45',
            'email2'                => 'nullable|email|max:45',
            'phone'                 => 'max:30',
            'phone2'                => 'max:30',
            'fax'                   => 'max:30',
            'contact_person'        => 'max:30',
            'address'               => 'max:150',
            'city'                  => 'max:30',
            'remark'                => 'max:150',
            'npwp'                  => 'max:30',
            'bank_name'             => 'required|max:30',
            'bank_account_number'   => 'required|max:30',
            'bank_account_name'     => 'required|max:30',
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors())->withInput($request->all());

        $dateTimeNow = Carbon::now('Asia/Jakarta');
        $user = Auth::user();

        $supplier = Supplier::create([
            'code'                  => $request->input('code'),
            'name'                  => $request->input('name'),
            'type'                  => $request->input('type'),
            'category'              => $request->input('category'),
            'email1'                => $request->input('email'),
            'email2'                => $request->input('email2'),
            'phone1'                => $request->input('phone'),
            'phone2'                => $request->input('phone2'),
            'fax'                   => $request->input('fax'),
            'contact_person'        => $request->input('contact_person'),
            'address'               => $request->input('address'),
            'city'                  => $request->input('city'),
            'remark'                => $request->input('remark'),
            'npwp'                  => $request->input('npwp'),
            'bank_name'             => $request->input('bank_name'),
            'bank_account_number'   => $request->input('bank_account_number'),
            'bank_account_name'     => $request->input('bank_account_name'),
            'created_by'            => $user->id,
            'created_at'            => $dateTimeNow->toDateTimeString(),
            'updated_by'            => $user->id,
            'updated_at'            => $dateTimeNow->toDateTimeString(),
            'status_id'             => $request->input('status')
        ]);

        Session::flash('message', 'berhasil membuat data vendor baru!');

        return redirect(route('admin.suppliers'));
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
     * @param Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        $contractStart = Carbon::parse($supplier->contract_start_date)->format('d M Y');
        $contractFinish = Carbon::parse($supplier->contract_finish_date)->format('d M Y');
        return view('admin.suppliers.edit', compact('supplier', 'contractStart', 'contractFinish'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        //
        $validator = Validator::make($request->all(), [
            'code'                  => 'max:30',
            'name'                  => 'required|max:100',
            'category'              => 'max:100',
            'email'                 => 'nullable|email|max:45',
            'email2'                => 'nullable|email|max:45',
            'phone'                 => 'max:30',
            'phone2'                => 'max:30',
            'fax'                   => 'max:30',
            'cellphone'             => 'max:30',
            'contact_person'        => 'max:30',
            'address'               => 'max:150',
            'city'                  => 'max:30',
            'remark'                => 'max:150',
            'npwp'                  => 'max:30',
            'bank_name'             => 'required|max:30',
            'bank_account_number'   => 'required|max:30',
            'bank_account_name'     => 'required|max:30',
        ],[
            'code.unique'       => 'Kode vendor telah terpakai!',
            'code.regex'        => 'Kode vendor harus tanpa spasi!'
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());

        $dateTimeNow = Carbon::now('Asia/Jakarta');
        $user = Auth::user();

        $supplier->name = $request->input('name');
        $supplier->code = $request->input('code');
        $supplier->type = $request->input('type');
        $supplier->category = $request->input('category');
        $supplier->email1 = $request->input('email');
        $supplier->email2 = $request->input('email2');
        $supplier->phone1 = $request->input('phone');
        $supplier->phone2 = $request->input('phone2');
        $supplier->fax = $request->input('fax');
        $supplier->cellphone = $request->input('cellphone');
        $supplier->contact_person = $request->input('contact_person');
        $supplier->address = $request->input('address');
        $supplier->city = $request->input('city');
        $supplier->remark = $request->input('remark');
        $supplier->npwp = $request->input('npwp');
        $supplier->bank_name = $request->input('bank_name');
        $supplier->bank_account_number = $request->input('bank_account_number');
        $supplier->bank_account_name = $request->input('bank_account_name');
        $supplier->updated_by = $user->id;
        $supplier->updated_at = $dateTimeNow->toDateTimeString();
        $supplier->status_id = $request->input('status');
        $supplier->save();

        Session::flash('message', 'Berhasil mengubah data vendor!');

        return redirect(route('admin.suppliers.edit', ['supplier' => $supplier->id]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        try{
            $supplier = Supplier::find($request->input('id'));
            $supplier->delete();
            return Response::json(array('success' => 'VALID'));
        }
        catch(\Exception $ex){
            return Response::json(array('errors' => 'INVALID'));
        }
    }

    public function getIndex(Request $request)
    {
        $suppliers = Supplier::orderBy('name')->get();
        $mode = 'default';
        if($request->filled('mode')){
            $mode = $request->input('mode');
        }

        return DataTables::of($suppliers)
            ->addIndexColumn()
            ->setTransformer(new SupplierTransformer($mode))
            ->make(true);
    }

    public function getSuppliers(Request $request){
        $term = trim($request->q);
        $vendors = Supplier::where('name', 'LIKE', '%'. $term. '%')
            ->where('status_id', 1)
            ->get();

        $formatted_tags = [];

        foreach ($vendors as $vendor) {
            $type = $vendor->type === 'REGULAR' ? 'TIDAK TETAP' : 'TETAP';
            $formatted_tags[] = ['id' => $vendor->id, 'text' => $vendor->name. ' - '. $type];
        }

        return \Response::json($formatted_tags);
    }

    public function downloadExcel(){

        $now = Carbon::now('Asia/Jakarta');
        $filename = 'VENDOR_LIST_'. $now->toDateTimeString(). '.xlsx';

        return (new SupplierExport())->download($filename);
    }
}
