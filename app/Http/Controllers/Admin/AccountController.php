<?php
/**
 * Created by PhpStorm.
 * User: hellb
 * Date: 10/2/2018
 * Time: 9:11 AM
 */

namespace App\Http\Controllers\Admin;


use App\Exports\CostCodeExport;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Transformer\AccountTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AccountController extends Controller
{
    public function index()
    {
        return View('admin.accounts.index');
    }

    public function getIndex(){
        $purchaseRequests = Account::query();
        return DataTables::of($purchaseRequests)
            ->setTransformer(new AccountTransformer())
            ->addIndexColumn()
            ->make(true);
    }

    public function create(){
        return View('admin.accounts.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'code'          => 'required|max:40|unique:accounts',
        ],[
            'code.unique'   => 'Kode Cost telah terpakai!'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');

        $account = Account::create([
            'code'          => $request->input('code'),
            'location'      => $request->input('location') ?? null,
            'department'    => $request->input('department') ?? null,
            'division'      => $request->input('division') ?? null,
            'description'   => $request->input('description') ?? null,
            'remark'        => $request->input('remark') ?? null,
            'brand'         => $request->input('brand') ?? null,
            'created_by'    => $user->id,
            'updated_by'    => $user->id,
            'created_at'    => $now->toDateString(),
            'updated_at'    => $now->toDateString()
        ]);

        Session::flash('message', 'Berhasil membuat Cost Code!');

        return redirect()->route('admin.accounts');
    }

    public function edit(Account $account){
        return View('admin.accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account){

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');

        $account->location = $request->input('location') ?? null;
        $account->department = $request->input('department') ?? null;
        $account->division = $request->input('division') ?? null;
        $account->description = $request->input('description') ?? null;
        $account->remark = $request->input('remark') ?? null;
        $account->brand = $request->input('brand') ?? null;
        $account->is_synced = false;
        $account->updated_by = $user->id;
        $account->updated_at = $now->toDateTimeString();
        $account->save();

        Session::flash('message', 'Berhasil mengubah Cost Code '. $account->code. '!');

        return redirect()->route('admin.accounts');
    }

    public function delete(Request $request)
    {
        try{
            $account = Account::find($request->input('id'));

            if($account->issued_docket_headers->count() > 0){
                return Response::json(array('errors' => 'INVALID'));
            }

            $account->delete();

            Session::flash('message', 'Berhasil menghapus Cost Code '. $account->code. '!');
            return Response::json(array('success' => 'VALID'));
        }
        catch(\Exception $ex){
            return Response::json(array('errors' => 'INVALID'));
        }
    }

    public function getAccounts(Request $request){
        $term = trim($request->q);
        $accounts = Account::where('code', 'LIKE', '%'. $term. '%')
                    ->orWhere('description', 'LIKE', '%'. $term. '%')
                    ->orWhere('location', 'LIKE', '%'. $term. '%')
                    ->get();

        $formatted_tags = [];

        foreach ($accounts as $account) {
            $text = $account->code;
            if(!empty($account->description)) $text .= ' - '. $account->description. " - ". $account->location;
            $formatted_tags[] = ['id' => $account->id, 'text' => $text];
        }

        return Response::json($formatted_tags);
    }

    public function getAccountsWithName(Request $request){
        $term = trim($request->q);
        $accounts = Account::where('code', 'LIKE', '%'. $term. '%')
            ->orWhere('description', 'LIKE', '%'. $term. '%')
            ->orWhere('location', 'LIKE', '%'. $term. '%')
            ->get();

        $formatted_tags = [];

        foreach ($accounts as $account) {
            $text = $account->code;
            $value = $account->id. '#'. $account->code;
            if(!empty($account->description)) {
                $text .= ' - '. $account->description. " - ". $account->location;
                $value .= '#'. $account->description;
            }
            $formatted_tags[] = ['id' => $value, 'text' => $text];
        }

        return Response::json($formatted_tags);
    }

    public function downloadExcel(Request $request){
        $now = Carbon::now('Asia/Jakarta');
        $filename = 'COST_CODE_'. $now->toDateTimeString(). '.xlsx';

        return (new CostCodeExport())->download($filename);
    }
}