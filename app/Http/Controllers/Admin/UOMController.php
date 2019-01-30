<?php

namespace App\Http\Controllers\Admin;

use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use App\Models\Uom;
use App\Transformer\MasterData\UOMTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class UOMController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('admin.uoms.index');
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
        $uoms = Uom::all();
        return DataTables::of($uoms)
            ->setTransformer(new UOMTransformer)
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
        return view('admin.uoms.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|max:45|unique:uoms'
        ],[
            'description.unique'    => 'UOM telah terpakai!'
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        $dateTimeNow = Carbon::now('Asia/Jakarta');

        $uom = Uom::create([
            'description'          => $request->get('description')
        ]);

//        return redirect()->intended(route('admin.uoms'));
        Session::flash('message', 'Berhasil membuat data alat satuan unit!');

        return redirect()->route('admin.uoms');
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
     * @param Uom $uom
     * @return \Illuminate\Http\Response
     */
    public function edit(Uom $uom)
    {
        return view('admin.uoms.edit', ['uom' => $uom]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Uom $uom
     * @return mixed
     */
    public function update(Request $request, Uom $uom)
    {
        $validator = Validator::make($request->all(), [
            'description' => [
                'required',
                'max:45',
                Rule::unique('uoms')->ignore($uom->id)
            ],
        ],[
            'description.unique'    => 'UOM telah terpakai!'
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());

        $dateTimeNow = Carbon::now('Asia/Jakarta');

        $uom->description = $request->get('description');

        $uom->save();

//        return redirect()->intended(route('admin.uoms'));
        Session::flash('message', 'Berhasil mengubah data satuan unit!');

        return redirect()->route('admin.uoms.edit', ['uom' => $uom]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try{
            $uom = Uom::find($request->input('id'));
            $uom->delete();

            Session::flash('message', 'Berhasil menghapus data satuan unit '. $uom->description);
            return Response::json(array('success' => 'VALID'));
        }
        catch(\Exception $ex){
            return Response::json(array('errors' => 'INVALID'));
        }
    }
}
