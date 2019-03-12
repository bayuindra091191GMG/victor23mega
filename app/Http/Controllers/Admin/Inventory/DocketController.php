<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Exports\IssuedDocketCostCodeExport;
use App\Exports\IssuedDocketExport;
use App\Http\Controllers\Controller;
use App\Libs\Utilities;
use App\Models\Account;
use App\Models\Department;
use App\Models\Document;
use App\Models\IssuedDocketDetail;
use App\Models\IssuedDocketHeader;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\Machinery;
use App\Models\MaterialRequestHeader;
use App\Models\NumberingSystem;
use App\Models\PermissionMenu;
use App\Models\StockCard;
use App\Models\Warehouse;
use App\Transformer\Inventory\IssuedDocketTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade as PDF;
use PDF3;

class DocketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return View('admin.inventory.docket.index');
    }

    public function beforeCreate(){
        return View('admin.inventory.docket.before_create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sysNo = NumberingSystem::where('doc_id', '1')->first();
        $document = Document::where('id', '1')->first();
        $autoNumber = Utilities::GenerateNumber($document->code, $sysNo->next_no);
        $warehouses = Warehouse::where('id', '!=', 0)->get();
        $departments = Department::orderBy('name')->get();

        $data = [
            'departments'       => $departments,
            'autoNumber'        => $autoNumber,
            'warehouses'        => $warehouses
        ];

        return view('admin.inventory.docket.create')->with($data);
    }

    public function createFuel()
    {
        $sysNo = NumberingSystem::where('doc_id', '1')->first();
        $document = Document::where('id', '1')->first();
        $autoNumber = Utilities::GenerateNumber($document->code, $sysNo->next_no);
        $warehouses = Warehouse::where('id', '!=', 0)->get();
        $departments = Department::orderBy('name')->get();

        $data = [
            'departments'       => $departments,
            'autoNumber'        => $autoNumber,
            'warehouses'        => $warehouses
        ];

        return view('admin.inventory.docket.create_fuel')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'code'          => 'max:40',
            'date'          => 'required',
            'account'       => 'required'
        ],[
            'account.required'      => 'Cost Code harus dipilih!',
            'date.required'         => 'Mohon isi tanggal dokumen!'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate KM & HM
        if($request->filled('machinery')){
            if(!$request->filled('km') || !$request->filled('hm')){
                return redirect()->back()->withErrors('KM dan HM wajib diisi apabila pada MR terdapat unit alat berat!', 'default')->withInput($request->all());
            }
        }

        // Validate department
        if($request->input('department') === '-1'){
            return redirect()->back()->withErrors('Departemen wajib diisi!', 'default')->withInput($request->all());
        }

        // Validate details
        $items = $request->input('item');

        if(count($items) == 0){
            return redirect()->back()->withErrors('Detail inventory wajib diisi!', 'default')->withInput($request->all());
        }

        // Validate details
//        $mrId = Input::get('mr_id');
        $qtys = Input::get('qty');
        $mrCheck = true;
        $qtyCheck = true;
        $wrCheck = true;
        $wrQtyCheck = true;
        $i = 0;
//        $materialRequest = MaterialRequestHeader::where('code', $mrId)->first();

        foreach($items as $item){
            if(empty($item)) {
                return redirect()->back()->withErrors('Inventory dan Kuantitas wajib diisi!', 'default')->withInput($request->all());
            }

            if(empty($qtys[$i]) || $qtys[$i] == '0'){
                return redirect()->back()->withErrors('Inventory dan Kuantitas wajib diisi!', 'default')->withInput($request->all());
            }

            // Validate MR relation
//            $mrDetail = $materialRequest->material_request_details->where('item_id', $item)->first();
//            if(empty($mrDetail)){
//                return redirect()->back()->withErrors('Inventory tidak ada dalam MR berkaitan!', 'default')->withInput($request->all());
//            }

//            $qtyInt = (int) $qtys[$i];
//            $qtyResult = $mrDetail->quantity - $mrDetail->quantity_issued;
//            if($qtyResult < $qtyInt){
//                return redirect()->back()->withErrors('Inventory tidak boleh melebihi kuantitas pada MR berkaitan!', 'default')->withInput($request->all());
//            }

            // Check Item in Stock
            $tempItem = ItemStock::where('item_id', $item)
                ->where('warehouse_id', Input::get('warehouse'))
                ->first();

            if($tempItem == null){
                $wrCheck = false;
            }
            else if($tempItem->stock == null || $tempItem->stock == 0){
                $wrQtyCheck = false;
            }
            else {
                $lastStock = $tempItem->stock - $qtys[$i];
                if ($tempItem->stock < $qtys[$i] || $lastStock < 0) {
                    $qtyCheck = false;
                }
            }

            $i++;
        }

        if(!$qtyCheck){
            return redirect()->back()->withErrors('Stock tidak mencukupi!', 'default')->withInput($request->all());
        }
        if(!$wrCheck){
            return redirect()->back()->withErrors('Inventory tidak ada di gudang yang dipilih!', 'default')->withInput($request->all());
        }
        if(!$wrQtyCheck){
            return redirect()->back()->withErrors('Stock tidak ada pada gudang yang dipilih!', 'default')->withInput($request->all());
        }

        if(!$mrCheck){
            return redirect()->back()->withErrors('Kuantitas tidak boleh melebihi kuantitas di MR!', 'default')->withInput($request->all());
        }

        // Check duplicate inventory
        $valid = Utilities::arrayIsUnique($items);
        if(!$valid){
            return redirect()->back()->withErrors('Detail inventory tidak boleh kembar!', 'default')->withInput($request->all());
        }

        $user = Auth::user();

        // Generate auto number
        if(Input::get('auto_number')) {
            $sysNo = NumberingSystem::where('doc_id', '1')->first();
            $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
            $docketNumber = Utilities::GenerateNumber($docCode, $sysNo->next_no);

            // Check existing number
            $check = IssuedDocketHeader::where('code', $docketNumber)->first();
            if($check != null){
                return redirect()->back()->withErrors('Nomor Issued Docket sudah terdaftar!', 'default')->withInput($request->all());
            }

            $sysNo->next_no++;
            $sysNo->save();
        }
        else{
            if(empty(Input::get('code'))){
                return redirect()->back()->withErrors('Nomor Issued Docket wajib diisi!', 'default')->withInput($request->all());
            }

            $docketNumber = Input::get('code');

            // Check existing number
            $check = IssuedDocketHeader::where('code', $docketNumber)->first();
            if($check != null){
                return redirect()->back()->withErrors('Nomor Issued Docket sudah terdaftar!', 'default')->withInput($request->all());
            }
        }

        $now = Carbon::now('Asia/Jakarta');
        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');

        $docketHeader = IssuedDocketHeader::create([
            'code'                          => $docketNumber,
            'type'                          => 1,
            'site_id'                       => $user->employee->site_id,
            'date'                          => $date->toDateTimeString(),
//            'material_request_header_id'    => $materialRequest->id,
            'department_id'                 => $request->input('department'),
            'unit_id'                       => $request->filled('machinery') ? null : $request->input('machinery'),
            'division'                      => $request->input('division'),
            'warehouse_id'                  => $request->input('warehouse'),
            'hm'                            => $request->input('hm'),
            'km'                            => $request->input('km'),
            'is_retur'                      => 0,
            'status_id'                     => 3,
            'created_by'                    => $user->id,
            'updated_by'                    => $user->id,
            'created_at'                    => $now->toDateString(),
            'account_id'                    => $request->input('account') ?? null
        ]);

        if(!empty($request->input('account'))){
            $docketHeader->account_id = $request->input('account');
        }

        // Create Issued Docket Detail
        $remark = Input::get('remark');
        $idx = 0;

        foreach($items as $item){
            if(!empty($item)){
                $qty = (int) $qtys[$idx];

                $docketDetail = IssuedDocketDetail::create([
                    'header_id'         => $docketHeader->id,
                    'item_id'           => $item,
                    'machinery_id'      => $docketHeader->machinery_id,
                    'quantity'          => $qty,
                    'quantity_retur'    => 0
                ]);

                if(!empty($remark[$idx])) $docketDetail->remarks = $remark[$idx];
                $docketDetail->save();

                // Update warehouse stock
                $itemStockData = ItemStock::where('item_id', $docketDetail->item_id)
                    ->where('warehouse_id', $docketHeader->warehouse_id)
                    ->first();
                $itemStockData->stock = $itemStockData->stock - $qty;
                $itemStockData->save();

                // Get warehouse stock result
                $stockResultWarehouse = $itemStockData->stock;

                // Update total stock
                $itemData = Item::where('id', $item)->first();
                $itemData->stock = $itemData->stock - $qty;
                $itemData->save();

                // Stock Card
                StockCard::create([
                    'item_id'               => $item,
                    'reference'             => 'Issued Docket ' . $docketHeader->code,
                    'in_qty'                => 0,
                    'out_qty'               => $qty,
                    'result_qty'            => $itemData->stock,
                    'result_qty_warehouse'  => $stockResultWarehouse,
                    'warehouse_id'          => $request->input('warehouse'),
                    'created_by'            => $user->id,
                    'created_at'            => $now->toDateTimeString(),
                    'updated_by'            => $user->id,
                    'updated_at'            => $now->toDateTimeString()
                ]);

                // Update MR quantity issued
//                $mrDetail = $materialRequest->material_request_details->where('item_id', $item)->first();
//                $mrDetail->quantity_issued += $qty;
//                $mrDetail->save();
            }
            $idx++;
        }

        // Check all issued or not
//        $materialRequest = MaterialRequestHeader::where('code', $mrId)->first();
//        $isAllIssued = true;
//        foreach ($materialRequest->material_request_details as $detail){
//            if($detail->quantity > $detail->quantity_issued){
//                $isAllIssued = false;
//            }
//        }
//
//        if($isAllIssued){
//            if($materialRequest->purpose === 'non-stock'){
//                $materialRequest->status_id = 4;
//            }
//            $materialRequest->is_issued = 2;
//        }
//        else{
//            $materialRequest->is_issued = 1;
//        }
//        $materialRequest->save();

        Session::flash('message', 'Berhasil membuat Issued Docket!');

        return redirect()->route('admin.issued_dockets.show', ['issued_docket' => $docketHeader]);
    }

    public function storeFuel(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'code'          => 'max:40',
            'date'          => 'required',
            'account'       => 'required'
        ],[
            'account.required'  => 'Cost Code harus dipilih!',
            'date.required'     => 'Mohon isi tanggal dokumen!'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate department
        if($request->input('department') === '-1'){
            return redirect()->back()->withErrors('Departemen wajib diisi!', 'default')->withInput($request->all());
        }

        // Validate KM & HM
//        if($request->input('machinery_id') != '0'){
//            if(!$request->filled('km') || !$request->filled('hm')){
//                return redirect()->back()->withErrors('KM dan HM wajib diisi apabila pada MR terdapat unit alat berat!', 'default')->withInput($request->all());
//            }
//        }

        // Validate details
//        $mrId = Input::get('mr_id');
//        $materialRequest = MaterialRequestHeader::where('code', $mrId)->first();

        $items = Input::get('item');
        $machineries = Input::get('machinery');
        $qtys = Input::get('qty');
        $shifts = Input::get('shift');
        $times = Input::get('time');
        $hms = Input::get('hm');
        $kms = Input::get('km');
        $fuelmans = Input::get('fuelman');
        $operators = Input::get('operator');
        $valid = true;
        $stockCheck = true;
        $i = 0;

        $uniqueItems = array_unique($items);

        foreach($items as $item){
            if(empty($item)) $valid = false;
            if(empty($qtys[$i]) || $qtys[$i] == '0') $valid = false;
            if(empty($machineries[$i])) $valid = false;
            $i++;
        }

        if(!$valid){
            return redirect()->back()->withErrors('Inventory, Unit Alat Berat dan Kuantitas wajib diisi!', 'default')->withInput($request->all());
        }

        // Validate item stocks
        foreach ($uniqueItems as $uniqueItem){
            $totalInputQty = 0;
            $i = 0;
            foreach ($items as $item){
                if($uniqueItem === $item){
                    $qtyInt = (int) $qtys[$i];
                    $totalInputQty += $qtyInt;
                }
            }

            // Validate MR relation
//            $mrDetail = $materialRequest->material_request_details->where('item_id', $uniqueItem)->first();
//            if(empty($mrDetail)){
//                return redirect()->back()->withErrors('Inventory tidak ada dalam MR berkaitan!', 'default')->withInput($request->all());
//            }

//            $qtyResult = $mrDetail->quantity - $mrDetail->quantity_issued;
//            if($qtyResult < $totalInputQty){
//                return redirect()->back()->withErrors('Inventory tidak boleh melebihi kuantitas pada MR berkaitan!', 'default')->withInput($request->all());
//            }

            // Check Item in Stock
            $itemStock = ItemStock::where('item_id', $uniqueItem)
                ->where('warehouse_id', Input::get('warehouse'))
                ->first();

            if(empty($itemStock)){
                $stockCheck = false;
            }
            else{
                if($itemStock->stock < $totalInputQty){
                    $stockCheck = false;
                }
            }

            if(!$stockCheck){
                $warehouse = Warehouse::find($request->input('warehouse'));
                return redirect()->back()->withErrors('Stock tidak mencukupi di '. $warehouse->name, 'default')->withInput($request->all());
            }
        }

        // Check duplicate machinery
//        $valid = Utilities::arrayIsUnique($machineries);
//        if(!$valid){
//            return redirect()->back()->withErrors('Detail unit alat berat tidak boleh kembar!', 'default')->withInput($request->all());
//        }

        $user = Auth::user();

        // Generate auto number
        if(Input::get('auto_number')) {
            $sysNo = NumberingSystem::where('doc_id', '1')->first();
            $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
            $docketNumber = Utilities::GenerateNumber($docCode, $sysNo->next_no);

            // Check existing number
            $check = IssuedDocketHeader::where('code', $docketNumber)->first();
            if($check != null){
                return redirect()->back()->withErrors('Nomor Issued Docket sudah terdaftar!', 'default')->withInput($request->all());
            }

            $sysNo->next_no++;
            $sysNo->save();
        }
        else{
            if(empty(Input::get('code'))){
                return redirect()->back()->withErrors('Nomor Issued Docket wajib diisi!', 'default')->withInput($request->all());
            }

            $docketNumber = Input::get('code');

            // Check existing number
            $check = IssuedDocketHeader::where('code', $docketNumber)->first();
            if($check != null){
                return redirect()->back()->withErrors('Nomor Issued Docket sudah terdaftar!', 'default')->withInput($request->all());
            }
        }

        $now = Carbon::now('Asia/Jakarta');
        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');

        $docketHeader = IssuedDocketHeader::create([
            'code'                          => $docketNumber,
            'site_id'                       => $user->employee->site_id,
            'type'                          => 2,
            'date'                          => $date->toDateTimeString(),
//            'material_request_header_id'    => $materialRequest->id,
            'department_id'                 => $request->input('department'),
//            'unit_id'                       => $materialRequest->machinery_id,
            'division'                      => $request->input('division'),
            'warehouse_id'                  => $request->input('warehouse'),
            'is_retur'                      => 0,
            'status_id'                     => 3,
            'created_by'                    => $user->id,
            'updated_by'                    => $user->id,
            'created_at'                    => $now->toDateString(),
            'updated_at'                    => $now->toDateString(),
            'account_id'                    => $request->input('account') ?? null
        ]);

        // Create Issued Docket Detail
        $remark = Input::get('remark');
        $idx = 0;

        foreach($items as $item){
            if(!empty($item)){
                $qty = (int) $qtys[$idx];

                $docketDetail = IssuedDocketDetail::create([
                    'header_id'         => $docketHeader->id,
                    'item_id'           => $item,
                    'machinery_id'      => $machineries[$idx],
                    'quantity'          => $qty,
                    'quantity_retur'    => 0,
                    'shift'             => $shifts[$idx],
                    'time'              => $times[$idx],
                    'hm'                => $hms[$idx],
                    'km'                => $kms[$idx],
                    'fuelman'           => $fuelmans[$idx],
                    'operator'          => $operators[$idx]
                ]);

                if(!empty($remark[$idx])) $docketDetail->remarks = $remark[$idx];
                $docketDetail->save();

                // Update warehouse stock
                $itemStockData = ItemStock::where('item_id', $docketDetail->item_id)
                    ->where('warehouse_id', $request->input('warehouse'))
                    ->first();
                $itemStockData->stock = $itemStockData->stock - $qty;
                $itemStockData->save();

                // Get warehouse stock result
                $stockResultWarehouse = $itemStockData->stock;

                // Update total stock
                $itemData = Item::where('id', $item)->first();
                $itemData->stock = $itemData->stock - $qty;
                $itemData->save();

                // Stock Card
                StockCard::create([
                    'item_id'               => $item,
                    'in_qty'                => 0,
                    'out_qty'               => $qty,
                    'result_qty'            => $itemData->stock,
                    'result_qty_warehouse'  => $stockResultWarehouse,
                    'warehouse_id'          => $request->input('warehouse'),
                    'created_by'            => $user->id,
                    'created_at'            => $now->toDateTimeString(),
                    'updated_by'            => $user->id,
                    'updated_at'            => $now->toDateTimeString(),
                    'reference'             => 'Issued Docket BBM ' . $docketHeader->code, ' - '. $docketDetail->machinery->code
                ]);

                // Update MR quantity issued
//                $mrDetail = $materialRequest->material_request_details->where('item_id', $item)->first();
//                $mrDetail->quantity_issued += $qty;
//                $mrDetail->save();
            }
            $idx++;
        }

        // Check all issued or not
//        $materialRequest = MaterialRequestHeader::where('code', $mrId)->first();
//        $isAllIssued = true;
//        foreach ($materialRequest->material_request_details as $detail){
//            if($detail->quantity > $detail->quantity_issued){
//                $isAllIssued = false;
//            }
//        }
//
//        if($isAllIssued){
//            if($materialRequest->purpose === 'non-stock'){
//                $materialRequest->status_id = 4;
//            }
//            $materialRequest->is_issued = 2;
//        }
//        else{
//            $materialRequest->is_issued = 1;
//        }
//        $materialRequest->save();

        Session::flash('message', 'Berhasil membuat Issued Docket!');

        return redirect()->route('admin.issued_dockets.show', ['issued_docket' => $docketHeader]);
    }

    /**
     * Display the specified resource.
     *
     * @param IssuedDocketHeader $issued_docket
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show(IssuedDocketHeader $issued_docket)
    {
        $header = $issued_docket;

        // Get retur status
        if($header->is_retur === 0){
            $returStr = 'Tidak Ada';
        }
        else if($header->is_retur === 1){
            $returStr = 'Sebagian';
        }
        else{
            $returStr = 'Semua';
        }

        $data = [
            'header'        => $header,
            'returStr'      => $returStr
        ];

        return View('admin.inventory.docket.show')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param IssuedDocketHeader $issuedDocketHeader
     * @internal param PurchaseRequestHeader $purchase_request
     * @internal param int $id
     */
    public function edit($id){
        $header = IssuedDocketHeader::find($id);
        $departments = Department::all();

        return View('admin.inventory.docket.edit', compact('header', 'departments'));
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
        $validator = Validator::make($request->all(),[
            'division'      => 'max:90'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        if(Input::get('department') === '-1'){
            return redirect()->back()->withErrors('Pilih departemen!', 'default')->withInput($request->all());
        }

        $user = \Auth::user();
        $now = Carbon::now('Asia/Jakarta');

        $docketHeader = IssuedDocketHeader::find($id);
        $docketHeader->department_id = Input::get('department');
        $docketHeader->division = Input::get('division');
        $docketHeader->updated_by = $user->id;
        $docketHeader->updated_at = $now->toDateString();

        $docketHeader->save();

        if(!empty(Input::get('machinery'))){
            $docketHeader->unit_id = Input::get('machinery');
            $docketHeader->save();
        }

        if(!empty(Input::get('purchase_request_header'))){
            $docketHeader->purchase_request_id = Input::get('purchase_request_header');
            $docketHeader->save();
        }

        Session::flash('message', 'Berhasil mengubah Issued Docket!');

        return redirect()->route('admin.issued_dockets.edit', ['issued_docket' => $docketHeader->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function printDocument($id){
        $header = IssuedDocketHeader::find($id);

        return view('documents.issued_dockets.issued_docket_doc', compact('header'));
    }

    public function downloadExcel($id){
        $issuedDocket = IssuedDocketHeader::find($id);
        $issuedDocketDetails = IssuedDocketDetail::where('header_id', $issuedDocket->id)->get();

        try {
            $newFileName = $issuedDocket->code.Carbon::now('Asia/Jakarta')->format('Ymdhms');
            $filePath = '/Form Issued Docket.xlsx';

            $path = public_path('documents/');
            Facades\Excel::load($path . $filePath, function($reader) use($issuedDocket, $issuedDocketDetails)
            {
                $reader->sheet('Sheet1', function($sheet) use($issuedDocket, $issuedDocketDetails)
                {
                    //Set The field Data
                    //Header
                    $sheet->getCell('C4')->setValueExplicit(": ".$issuedDocket->date);
                    $sheet->getCell('C5')->setValueExplicit(": ".$issuedDocket->machinery->code);
                    $sheet->getCell('C6')->setValueExplicit(": ".$issuedDocket->department->name);
                    $sheet->getCell('C7')->setValueExplicit(": ".$issuedDocket->division);
                    $sheet->getCell('G4')->setValueExplicit(": ".$issuedDocket->code);
                    $sheet->getCell('G5')->setValueExplicit(": ".$issuedDocket->purchase_request_header->code);

                    //Details
                    $i = 1;
                    $start = 11;
                    foreach ($issuedDocketDetails as $detail){
                        $sheet->getCell('A'.$start)->setValueExplicit($i);
                        $sheet->getCell('B'.$start)->setValueExplicit($detail->time);
                        $sheet->getCell('C'.$start)->setValueExplicit($detail->item->name);
                        $sheet->getCell('D'.$start)->setValueExplicit($detail->item->code);
                        $sheet->getCell('E'.$start)->setValueExplicit($detail->item->uom->description);
                        $sheet->getCell('F'.$start)->setValueExplicit($detail->quantity);
                        $sheet->getCell('G'.$start)->setValueExplicit($detail->remarks);

                        $start++;
                        $i++;
                    }
                });
            })->setFilename($newFileName)->export('xlsx');
        }
        catch (Exception $ex){
            //Utilities::ExceptionLog($ex);
            return response($ex, 500)
                ->header('Content-Type', 'text/plain');
        }
    }

    public function report(){
        $departments = Department::all();
        $warehouses = Warehouse::where('id', '>', 0)->orderBy('name')->get();
        $isHo = 0;

        $data = [
            'departments'   => $departments,
            'warehouses'    => $warehouses,
            'isHo'          => $isHo
        ];

        return View('admin.inventory.docket.report')->with($data);
    }

    public function reportHo(){
        $departments = Department::all();
        $warehouses = Warehouse::where('id', '>', 0)->orderBy('name')->get();
        $isHo = 1;

        $data = [
            'departments'   => $departments,
            'warehouses'    => $warehouses,
            'isHo'          => $isHo
        ];

        return View('admin.inventory.docket.report')->with($data);
    }

    public function reportCostCode(){
        $departments = Department::all();
        $warehouses = Warehouse::where('id', '>', 0)->orderBy('name')->get();
        $isHo = 0;

        $data = [
            'departments'   => $departments,
            'warehouses'    => $warehouses,
            'isHo'          => $isHo
        ];

        return View('admin.inventory.docket.report_source_account')->with($data);
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

        // Validate ID type
        if($request->input('type') === 'bbm' && !$request->filled('item')){
            return redirect()->back()->withErrors('Penggunaan BBM wajib pilih inventory BBM!', 'default')->withInput($request->all());
        }

        $start = $start->addDays(-1);
        $end = $end->addDays(1);

        if($request->input('is_excel') === 'true'){
            $nowExcel = Carbon::now('Asia/Jakarta');
            $filenameExcel = 'ISSUED_DOCKET_REPORT_' . $nowExcel->toDateTimeString(). '.xlsx';
            if($request->input('type') === 'bbm'){
                $filenameExcel = 'ISSUED_DOCKET_REPORT_BBM_' . $nowExcel->toDateTimeString(). '.xlsx';
            }

            return (new IssuedDocketExport(
                $start->toDateTimeString(),
                $end->toDateTimeString(),
                (int) $request->input('department'),
                (int) $request->input('warehouse'),
                $request->filled('machinery') ? (int) $request->input('machinery') : -1,
                $request->input('type'),
                $request->filled('item') ? (int) $request->input('item') : -1,
                $request->input('is_ho')))
                ->download($filenameExcel);
        }

        $idHeaders = IssuedDocketHeader::with(['issued_docket_details', 'issued_docket_details.item', 'account', 'site', 'issued_docket_details.machinery', 'createdBy', 'department'])
            ->whereBetween('date', array($start->toDateTimeString(), $end->toDateTimeString()));

        // Filter departemen
        $filterDepartment = 'Semua';
        $department = $request->input('department');
        if($department != '0'){
            $idHeaders = $idHeaders->where('department_id', $department);
            $filterDepartment = Department::find($department)->name;
        }

        // Filter warehouse
        $filterWarehouse = 'Semua';
        $warehouse = $request->input('warehouse');
        if($warehouse != '-1'){
            $idHeaders = $idHeaders->where('warehouse_id', $warehouse);
            $filterWarehouse = Warehouse::find($warehouse)->name;
        }

        // Filter Machinery
        $filterMachinery = 'Semua';
        $filterMachineryId = -1;
        if($request->filled('machinery')){
            $machineryId = $request->input('machinery');
            $idHeaders = $idHeaders->whereHas('issued_docket_details', function ($query) use($machineryId){
                $query->where('machinery_id', $machineryId);
            });
            $filterMachinery = Machinery::find($machineryId)->code;
            $filterMachineryId = (int) $machineryId;
        }


        // Filter ID type
        $type = $request->input('type');
        $item = null;
        $totalQty = 0;
        if($type === 'bbm'){
            $item = Item::find($request->input('item'));
            $idHeaders = $idHeaders->whereHas('issued_docket_details', function ($query) use($item){
                $query->where('item_id', $item->id);
            })->where('type', 2);
        }
        elseif($type === 'non-bbm'){
            $idHeaders = $idHeaders->where('type', 1);
        }

        $idHeaders = $idHeaders->orderByDesc('date')
            ->get();

        $totalValue = 0;
        $isHo = $request->input('is_ho');

        if($isHo === '1'){
            // Check menu permission
            $user = \Auth::user();
            $roleId = $user->roles->pluck('id')[0];

            if(!PermissionMenu::where('role_id', $roleId)->where('menu_id', 42)->first()){
                $isHo = '0';
            }
        }

        if($type === 'bbm'){
            foreach ($idHeaders as $idHeader){
                foreach ($idHeader->issued_docket_details as $idDetail){
                    if($idDetail->item_id === $item->id){
                        if($filterMachineryId > -1 && $idDetail->machinery_id !== $filterMachineryId){
                            continue;
                        }
                        $totalQty += $idDetail->quantity;

                        if($isHo === '1'){
                            $value = $idDetail->item->value ?? 0;
                            $subTotalValue = $value * $idDetail->quantity;
                            $totalValue += $subTotalValue;
                        }
                    }
                }
            }
        }
        else{
            if($isHo === '1'){
                foreach ($idHeaders as $idHeader){
                    foreach ($idHeader->issued_docket_details as $idDetail){
                        if($filterMachineryId > -1 && $idDetail->machinery_id !== $filterMachineryId){
                            continue;
                        }

                        $value = $idDetail->item->value ?? 0;
                        $subTotalValue = $value * $idDetail->quantity;
                        $totalValue += $subTotalValue;
                    }
                }
            }
        }

        // Validate Data
        if($idHeaders->count() == 0){
            return redirect()->back()->withErrors('Data tidak ditemukan!', 'default')->withInput($request->all());
        }

//        dd($request->input('is_preview'));

        if($request->input('is_preview') === 'false'){
            $data =[
                'idHeaders'         => $idHeaders,
                'start_date'        => $request->input('start_date'),
                'finish_date'       => $request->input('end_date'),
                'filterDepartment'  => $filterDepartment,
                'filterWarehouse'   => $filterWarehouse,
                'filterMachinery'   => $filterMachinery,
                'filterMachineryId' => $filterMachineryId,
                'item'              => $item,
                'totalQty'          => $totalQty,
                'totalValue'        => number_format($totalValue, 2, ",", ".")
            ];

            $now = Carbon::now('Asia/Jakarta');
            $filename = 'ISSUED_DOCKET_REPORT_' . $now->toDateTimeString();
            $idDocument = 'documents.issued_dockets.issued_docket_pdf';
            if($type === 'bbm'){
                $filename = 'ISSUED_DOCKET_REPORT_BBM_' . $now->toDateTimeString();
                $idDocument = 'documents.issued_dockets.issued_docket_bbm_pdf';
                if($isHo === '1') $idDocument = 'documents.issued_dockets.issued_docket_bbm_with_price_pdf';
            }
            else{
                if($isHo === '1') $idDocument = 'documents.issued_dockets.issued_docket_with_price_pdf';
            }

            $pdf = PDF3::loadView($idDocument, $data)
                ->setOption('footer-right', '[page] of [toPage]');

            return $pdf->download($filename. '.pdf');
        }
        else{
            $data =[
                'idHeaders'         => $idHeaders,
                'start_date'        => $request->input('start_date'),
                'finish_date'       => $request->input('end_date'),
                'filterDepartment'  => $filterDepartment,
                'filterWarehouse'   => $filterWarehouse,
                'filterMachinery'   => $filterMachinery,
                'filterMachineryId' => $filterMachineryId,
                'item'              => $item,
                'totalQty'          => $totalQty,
                'totalValue'        => number_format($totalValue, 2, ",", "."),
                'is_ho'             => $request->input('is_ho'),
                'department'        => $request->input('department'),
                'warehouse'         => $request->input('warehouse'),
                'machinery'         => $request->input('machinery'),
                'type'              => $request->input('type'),
                'item_id'           => $request->input('item')
            ];

            $idDocument = 'documents.issued_dockets.issued_docket_pdf_preview';
            if($type === 'bbm'){
                $idDocument = 'documents.issued_dockets.issued_docket_bbm_pdf_preview';
                if($isHo === '1') $idDocument = 'documents.issued_dockets.issued_docket_bbm_with_price_pdf_preview';
            }
            else{
                if($isHo === '1') $idDocument = 'documents.issued_dockets.issued_docket_with_price_pdf_preview';
            }

            return view($idDocument)->with($data);
        }
    }

    public function downloadReportExcel(Request $request) {
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

        // Validate ID type
        if($request->input('type') === 'bbm' && !$request->filled('item')){
            return redirect()->back()->withErrors('Penggunaan BBM wajib pilih inventory BBM!', 'default')->withInput($request->all());
        }

        $start = $start->addDays(-1);
        $end = $end->addDays(1);

        $now = Carbon::now('Asia/Jakarta');
        $filename = 'ISSUED_DOCKET_REPORT_' . $now->toDateTimeString(). '.xlsx';

        return (new IssuedDocketExport(
            $start->toDateTimeString(),
            $end->toDateTimeString(),
            (int) $request->input('department'),
            (int) $request->input('warehouse'),
            $request->filled('machinery') ? (int) $request->input('machinery') : -1,
            $request->input('type'),
            $request->filled('item') ? (int) $request->input('item') : -1,
            $request->input('is_ho')))
            ->download($filename);
    }

    public function downloadReportCostCode(Request $request) {
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
            $filenameExcel = 'ISSUED_DOCKET_REPORT_' . $nowExcel->toDateTimeString(). '.xlsx';
            if($request->input('type') === 'bbm'){
                $filenameExcel = 'COST_CODE_REPORT_' . $nowExcel->toDateTimeString(). '.xlsx';
            }

            return (new IssuedDocketCostCodeExport(
                $start->toDateTimeString(),
                $end->toDateTimeString(),
                (int) $request->input('account'),
                (int) $request->input('department'),
                (int) $request->input('warehouse'),
                $request->input('is_ho')))
                ->download($filenameExcel);
        }

        $start = $start->addDays(-1);
        $end = $end->addDays(1);

        $costCodes = new Collection();
        $allIdHeaders = new Collection();

        // Filter account
        $filterAccount = 'Semua';
        if($request->filled('account')){
            $account = Account::find($request->input('account'));
            $filterAccount = $account->code;
        }

        // Filter departemen
        $filterDepartment = 'Semua';
        $department = $request->input('department');
        if($department != '0'){
            $filterDepartment = Department::find($department)->name;
        }

        // Filter warehouse
        $filterWarehouse = 'Semua';
        $warehouse = $request->input('warehouse');
        if($warehouse != '-1'){
            $filterWarehouse = Warehouse::find($warehouse)->name;
        }

        $accounts = Account::all();
        foreach($accounts as $account){

            if($account->issued_docket_headers->count() === 0){
                continue;
            }

            if($filterAccount !== 'Semua'){
                if($account->code !== $filterAccount){
                    continue;
                }
            }

            $code = new Account();
            $code->code = $account->code;
            $code->location = $account->location;
            $code->department = $account->department;
            $code->division = $account->division;
            $code->description = $account->description;
            $code->status_id = $account->status_id;
            $code->created_by = $account->created_by;
            $code->created_at = $account->created_at;
            $code->updated_by = $account->updated_by;
            $code->updated_at = $account->updated_at;
            $code->createdBy = $account->createdBy;
            $code->updatedBy = $account->updatedBy;

            $idHeaders = IssuedDocketHeader::whereBetween('date', array($start->toDateTimeString(), $end->toDateTimeString()))
                ->where('account_id', $account->id);

            if($department != '0'){
                $idHeaders = $idHeaders->where('department_id', $department);
            }

            if($warehouse != '-1'){
                $idHeaders = $idHeaders->where('warehouse_id', $warehouse);
            }

            $idHeaders = $idHeaders->orderByDesc('date')
                ->get();

            $code->issued_docket_headers = $idHeaders;
            $costCodes->add($code);

            foreach ($idHeaders as $idHeader){
                $allIdHeaders->add($idHeader);
            }
        }

        $totalValue = 0;
        $isHo = $request->input('is_ho');

        if($isHo === '1'){
            // Check menu permission
            $user = \Auth::user();
            $roleId = $user->roles->pluck('id')[0];

            if(!PermissionMenu::where('role_id', $roleId)->where('menu_id', 42)->first()){
                $isHo = '0';
            }
        }

        foreach ($allIdHeaders as $idHeader){
            foreach ($idHeader->issued_docket_details as $idDetail){
                $value = $idDetail->item->value ?? 0;
                $subTotalValue = $value * $idDetail->quantity;
                $totalValue += $subTotalValue;
            }
        }

        // Validate Data
        if($costCodes->count() == 0){
            return redirect()->back()->withErrors('Data tidak ditemukan!', 'default')->withInput($request->all());
        }

        $data =[
            'costCodes'         => $costCodes,
            'start_date'        => $request->input('start_date'),
            'finish_date'       => $request->input('end_date'),
            'filterAccount'     => $filterAccount,
            'filterDepartment'  => $filterDepartment,
            'filterWarehouse'   => $filterWarehouse,
//            'item'              => $item,
//            'totalQty'          => $totalQty,
            'totalValue'        => number_format($totalValue, 2, ",", ".")
        ];

//        return view('documents.issued_dockets.issued_docket_pdf')->with($data);

        $idDocument = 'documents.issued_dockets.issued_docket_cost_code_pdf';

        $pdf = PDF3::loadView($idDocument, $data)
            ->setOption('footer-right', '[page] of [toPage]');

        $now = Carbon::now('Asia/Jakarta');
        $filename = 'COST_CODE_REPORT_' . $now->toDateTimeString();

        return $pdf->download($filename. '.pdf');
    }

    public function download($id){
        $issuedDocket = IssuedDocketHeader::find($id);
        $issuedDocketDetails = IssuedDocketDetail::where('header_id', $issuedDocket->id)->get();

        $pdf = PDF::loadView('documents.issued_dockets.issued_docket_doc', ['issuedDocket' => $issuedDocket, 'issuedDocketDetails' => $issuedDocketDetails])->setPaper('A4');
        $now = Carbon::now('Asia/Jakarta');
        $filename = $issuedDocket->code. '_' . $now->toDateTimeString();

        return $pdf->download($filename.'.pdf');
    }

    public function getIndex(){
        $purchaseRequests = IssuedDocketHeader::query();
        return DataTables::of($purchaseRequests)
            ->setTransformer(new IssuedDocketTransformer)
            ->make(true);
    }
}
