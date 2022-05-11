<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 01/02/2018
 * Time: 15:45
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\MachineryBrand;
use App\Transformer\MasterData\MachineryBrandTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class MachineryBrandController extends Controller
{
    public function index(){
        return View('admin.machinery_brands.index');
    }

    public function create(){
        return View('admin.machinery_brands.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'code'          => 'required|max:30|regex:/^\S*$/u|unique:machinery_brands',
            'name'          => 'required|max:45',
            'description'   => 'max:200'
        ],[
            'code.unique'   => 'Kode merek alat berat telah terpakai',
            'code.regex'    => 'Kode merek alat berat harus tanpa spasi'
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors())->withInput($request->all());

        $description = '';
        if($request->filled('description')){
            $description = trim($request->input('description'));
        }

        $newMachineryBrand = MachineryBrand::create([
            'name' => trim($request->input('name')),
            'code' => trim($request->input('code')),
            'description' => $description,
            'is_synced' => false,
            'created_on' => 'online'
        ]);

        Session::flash('message', 'Berhasil membuat data merek alat berat baru!');

        return redirect()->route('admin.machinery_brands');
    }

    public function edit(MachineryBrand $machineryBrand){
        return View('admin.machinery_brands.edit',['machineryBrand' => $machineryBrand]);
    }

    public function update(Request $request, MachineryBrand $machineryBrand){
        $validator = Validator::make($request->all(), [
            'code' => [
                'required',
                'max:30',
                'regex:/^\S*$/u',
                Rule::unique('machinery_brands')->ignore($machineryBrand->id)
            ],
            'name'          => 'required|max:45',
            'description'   => 'max:200'
        ],[
            'code.unique'   => 'Kode merek alat berat telah terpakai',
            'code.regex'    => 'Kode merek alat berat harus tanpa spasi'
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());

        $machineryBrand->name = trim($request->input('name'));
        $machineryBrand->code = trim($request->input('code'));
        $machineryBrand->description = trim($request->input('description'));
        $machineryBrand->is_synced = false;
        $machineryBrand->save();

        Session::flash('message', 'Berhasil mengubah data merek alat berat!');

        return redirect()->route('admin.machinery_brands.edit', ['machinery_brand' => $machineryBrand]);
    }

    public function destroy(Request $request)
    {
        try{
            $machineryBrand = MachineryBrand::find($request->input('id'));
            $machineryBrand->delete();

            Session::flash('message', 'Berhasil menghapus data merek alat berat '. $machineryBrand->name);
            return Response::json(array('success' => 'VALID'));
        }
        catch(\Exception $ex){
            return Response::json(array('errors' => 'INVALID'));
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getIndex()
    {
        $machineryBrands = MachineryBrand::all();
        return DataTables::of($machineryBrands)
            ->setTransformer(new MachineryBrandTransformer())
            ->addIndexColumn()
            ->make(true);
    }
}