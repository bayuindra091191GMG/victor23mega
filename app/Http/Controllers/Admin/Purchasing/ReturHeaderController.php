<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 17/05/2018
 * Time: 11:21
 */

namespace App\Http\Controllers\Admin\Purchasing;


use App\Http\Controllers\Controller;
use App\Libs\Utilities;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\NumberingSystem;
use App\Models\PurchaseInvoiceHeader;
use App\Models\ReturDetail;
use App\Models\ReturHeader;
use App\Models\StockCard;
use App\Transformer\Purchasing\ReturHeaderTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ReturHeaderController extends Controller
{
    public function index(){
        return View('admin.purchasing.returs.index');
    }

    public function show(ReturHeader $retur){
        $header = $retur;

        // Get total discount;
        $individualDiscount = $header->total_discount ?? 0;
        $extraDiscount = $header->extra_discount ?? 0;
        $totalDiscount = $individualDiscount + $extraDiscount;
        $totalDiscountStr = number_format($totalDiscount, 0, ",", ".");

        $data = [
            'header'            => $header,
            'totalDiscountStr'  => $totalDiscountStr
        ];

        return View('admin.purchasing.returs.show')->with($data);
    }

    public function beforeCreate(){
        return View('admin.purchasing.returs.before_create');
    }

    public function create(){
        if(empty(request()->pi)){
            return redirect()->route('admin.returs.before_create');
        }

        $purchaseInvoice = PurchaseInvoiceHeader::find(request()->pi);

        // Numbering System
        $user = Auth::user();
        $sysNo = NumberingSystem::where('doc_id', '13')->first();
        $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
        $autoNumber = Utilities::GenerateNumber($docCode, $sysNo->next_no);

        $data = [
            'purchaseInvoice'   => $purchaseInvoice,
            'autoNumber'        => $autoNumber
        ];

        return View('admin.purchasing.returs.create')->with($data);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'retur_code'        => 'required|max:45|regex:/^\S*$/u',
            'date'              => 'required'
        ],[
            'retur_code.regex'  => 'Nomor PO harus tanpa spasi!'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate Retur number
        if(!$request->filled('auto_number') && (!$request->filled('retur_code') || $request->input('retur_code') == "")){
            return redirect()->back()->withErrors('Nomor Retur wajib diisi!', 'default')->withInput($request->all());
        }

        // Validate details
        $items = $request->input('item_value');
        $qtys = $request->input('qty');
        $warehouses = $request->input('warehouse');
        $prices = $request->input('price');
        $discounts = $request->input('discount');
        $valid = true;
        $i = 0;
        foreach($items as $item){
            if(empty($item)) $valid = false;
            if(empty($qtys[$i]) || $qtys[$i] == '0') $valid = false;
            if(empty($prices[$i]) || $prices[$i] == '0') $valid = false;
            if(empty($warehouses[$i]) || $warehouses[$i] == '0') $valid = false;

            // Validate discount
            $priceVad = str_replace('.','', $prices[$i]);
            $discountVad = str_replace('.','', $discounts[$i]);
            if((double) $discountVad > ((double) $priceVad * (double) $qtys[$i])) return redirect()->back()->withErrors('Diskon tidak boleh melebihi harga!', 'default')->withInput($request->all());

            $i++;
        }

        if(!$valid){
            return redirect()->back()->withErrors('Detail kuantitas, gudang pengambilan dan harga wajib diisi!', 'default')->withInput($request->all());
        }

        // Check duplicate inventory
        $valid = Utilities::arrayIsUnique($items);
        if(!$valid){
            return redirect()->back()->withErrors('Detail inventory tidak boleh kembar!', 'default')->withInput($request->all());
        }

        // Get PO id
        $piId = $request->input('pi_id');

        // Validate Invoice relationship
        $validItem = true;
        $validQty = true;
        $i = 0;
        $purchaseInvoice = PurchaseInvoiceHeader::find($piId);
        foreach($items as $item){
            if(!empty($item)){
                $piDetail = $purchaseInvoice->purchase_invoice_details->where('item_id', $item)->first();
                if(empty($piDetail)){
                    $validItem = false;
                    break;
                }
                else{
                    if($qtys[$i] > $piDetail->quantity){
                        dd($i. ' '. $item. ' '. $qtys[$i]. ' '. $piDetail->quantity);
                        $validQty = false;
                        break;
                    }
                }
                $i++;
            }
        }

        if(!$validItem){
            return redirect()->back()->withErrors('Inventory tidak ada dalam Invoice!', 'default')->withInput($request->all());
        }
        if(!$validQty){
            return redirect()->back()->withErrors('Kuantitas inventory melebihi kuantitas inventory pada Invoice!', 'default')->withInput($request->all());
        }

        // Validate stock
        $validStock = true;
        $i = 0;
        foreach($items as $item){
            $itemStock = ItemStock::where('item_id', $item)->where('warehouse_id', $warehouses[$i])->first();
            if(!empty($itemStock)){
                if($itemStock->stock < $qtys[$i]) $validStock = false;
            }
            else{
                $validStock = false;
            }
            $i++;
        }

        if(!$validStock){
            return redirect()->back()->withErrors('Stok inventory tidak mencukupi di gudang pengambilan!', 'default')->withInput($request->all());
        }

        $user = Auth::user();

        // Generate auto number
        $returCode = 'default';
        if($request->filled('auto_number')){
            $sysNo = NumberingSystem::where('doc_id', '13')->first();
            $docCode = $sysNo->document->code. '-'. $user->employee->site->code;
            $returCode = Utilities::GenerateNumber($docCode, $sysNo->next_no);

            // Check existing number
            $temp = PurchaseInvoiceHeader::where('code', $returCode)->first();
            if(!empty($temp)){
                return redirect()->back()->withErrors('Nomor Retur sudah terdaftar!', 'default')->withInput($request->all());
            }

            $sysNo->next_no++;
            $sysNo->save();
        }
        else{
            $returCode = $request->input('retur_code');

            // Check existing number
            $temp = PurchaseInvoiceHeader::where('code', $returCode)->first();
            if(!empty($temp)){
                return redirect()->back()->withErrors('Nomor Retur sudah terdaftar!', 'default')->withInput($request->all());
            }
        }

        $now = Carbon::now('Asia/Jakarta');

        $returHeader = ReturHeader::create([
            'code'                  => $returCode,
            'purchase_invoice_id'   => $piId,
            'status_id'             => 3,
            'created_by'            => $user->id,
            'created_at'            => $now->toDateTimeString(),
            'updated_by'            => $user->id,
            'updated_at'            => $now->toDateTimeString()
        ]);

        $extraDiscount = 0;
        if($request->filled('extra_discount')){
            $extraDiscountInput = str_replace('.','', $request->input('extra_discount'));
            $extraDiscount = (double) $extraDiscountInput;
            $returHeader->extra_discount = $extraDiscount;
        }

        $delivery = 0;
        if($request->filled('delivery_fee') && $request->input('delivery_fee') != '0'){
            $deliveryFee = str_replace('.','', $request->input('delivery_fee'));
            $delivery = (double) $deliveryFee;
            $returHeader->delivery_fee = $delivery;
        }

        $date = Carbon::createFromFormat('d M Y', $request->input('date'), 'Asia/Jakarta');
        $returHeader->date = $date->toDateTimeString();

        if($request->filled('payment_term')){
            $returHeader->payment_term = $request->input('payment_term');
        }

        if($request->filled('special_note')){
            $returHeader->special_note = $request->input('special_note');
        }

        $returHeader->save();

        // Create po detail
        $totalPrice = 0;
        $totalDiscount = 0;
        $totalPayment = 0;
        $remarks = $request->input('remark');
        $idx = 0;

        $piHeader = $returHeader->purchase_invoice_header;
        $poHeader = $piHeader->purchase_order_header;
        $prHeader = $poHeader->purchase_request_header;
        $mrHeader = $prHeader->material_request_header;

        foreach($items as $item){
            if(!empty($item)){
                $priceStr = str_replace('.','', $prices[$idx]);
                $price = (double) $priceStr;
                $qty = (double) $qtys[$idx];
                $returDetail = ReturDetail::create([
                    'header_id'         => $returHeader->id,
                    'item_id'           => $item,
                    'warehouse_id'      => $warehouses[$idx],
                    'quantity'          => $qty,
                    'price'             => $priceStr
                ]);

                // Check discount
                if(!empty($discounts[$idx]) && $discounts[$idx] !== '0'){
                    $discountStr = str_replace('.','', $discounts[$idx]);
                    $returDetail->discount = $discountStr;

                    $discount = (double) $discountStr;
                    $returDetail->subtotal = ($qty * $price) - $discount;

                    // Accumulate total price
                    $totalPrice += $qty * $price;

                    // Accumulate total discount
                    $totalDiscount += $discount;
                }
                else{
                    $returDetail->subtotal = $qty * $price;
                    $totalPrice += $qty * $price;
                }

                if(!empty($remarks[$idx])) $returDetail->remark = $remarks[$idx];
                $returDetail->save();

                // Update warehouse stock
                $itemStockData = ItemStock::where('item_id', $item)
                    ->where('warehouse_id', $warehouses[$idx])
                    ->first();
                $itemStockData->stock = $itemStockData->stock - $qty;
                $itemStockData->save();

                // Get warehouse stock result
                $stockResultWarehouse = $itemStockData->stock;

                // Update total stock & average value
                $itemData = Item::where('id', $item)->first();

                if($itemData->stock - $qty == 0){
                    $itemData->value = 0;
                }
                else{
                    $oldValuation = $itemData->value * $itemData->stock;
                    $returValiation = $qty * $price;
                    $newValuation = ($oldValuation - $returValiation) / ($itemData->stock - $qty);
                    $itemData->value = $newValuation;
                }

                $itemData->stock = $itemData->stock - $qty;
                $itemData->save();

                // Stock Card
                StockCard::create([
                    'item_id'               => $item,
                    'in_qty'                => 0,
                    'out_qty'               => $qty,
                    'result_qty'            => $itemData->stock,
                    'result_qty_warehouse'  => $stockResultWarehouse,
                    'warehouse_id'          => $warehouses[$idx],
                    'created_by'            => $user->id,
                    'created_at'            => $now->toDateTimeString(),
                    'updated_by'            => $user->id,
                    'updated_at'            => $now->toDateTimeString(),
                    'reference'             => 'Retur ' . $returHeader->code
                ]);

                // Accumulate subtotal
                $totalPayment += $returDetail->subtotal;

                // Flagging retur and backtracking
                // Flag PI
                $piDetail = $piHeader->purchase_invoice_details->where('item_id', $item)->first();
                $piDetail->quantity_retur += $qty;
                $piDetail->save();

                // Flag PO
                $poDetail = $poHeader->purchase_order_details->where('item_id', $item)->first();
                $poDetail->quantity_retur += $qty;
                $poDetail->save();

                // Flag PR
                $prDetail = $prHeader->purchase_request_details->where('item_id', $item)->first();
                $prDetail->quantity_retur += $qty;
                $prDetail->save();

                // Flag MR
                $mrDetail = $mrHeader->material_request_details->where('item_id', $item)->first();
                $mrDetail->quantity_retur += $qty;
                $mrDetail->save();
            }
            $idx++;
        }

        // Check PI retur flag
        $isAllPiRetur = true;
        foreach($piHeader->purchase_invoice_details as $piDetail){
            if($piDetail->quantity_retur < $piDetail->quantity){
                $isAllPiRetur = false;
            }
        }

        if($isAllPiRetur){
            $piHeader->is_retur = 2;
        }
        else{
            $piHeader->is_retur = 1;
        }
        $piHeader->save();

        // Check PO retur flag
        $isAllPoRetur = true;
        foreach($poHeader->purchase_order_details as $poDetail){
            if($poDetail->quantity_retur < $poDetail->quantity){
                $isAllPoRetur = false;
            }
        }

        if($isAllPoRetur){
            $poHeader->is_retur = 2;
        }
        else{
            $poHeader->is_retur = 1;
        }
        $poHeader->save();

        // Check PR retur flag
        $isAllPrRetur = true;
        foreach($prHeader->purchase_request_details as $prDetail){
            if($prDetail->quantity_retur < $prDetail->quantity){
                $isAllPrRetur = false;
            }
        }

        if($isAllPrRetur){
            $prHeader->is_retur = 2;
        }
        else{
            $prHeader->is_retur = 1;
        }
        $prHeader->save();

        // Check MR retur flag
        $isAllMrRetur = true;
        foreach($mrHeader->material_request_details as $mrDetail){
            if($mrDetail->quantity_retur < $mrDetail->quantity){
                $isAllMrRetur = false;
            }
        }

        if($isAllMrRetur){
            $mrHeader->is_retur = 2;
        }
        else{
            $mrHeader->is_retur = 1;
        }
        $mrHeader->save();

        if($totalDiscount > 0) $returHeader->total_discount = $totalDiscount;
        $returHeader->total_price = $totalPrice;

        // Save total payment without tax
        $totalPayment -= $extraDiscount;
        $returHeader->total_payment_before_tax = $totalPayment;

        // Get PPN
        $ppnAmount = 0;
        if($request->filled('ppn') && $request->input('ppn') != '0'){
            $ppnAmount = $totalPayment * (10 / 100);
            $returHeader->ppn_percent = 10;
            $returHeader->ppn_amount = $ppnAmount;
        }

        $returHeader->total_payment = $totalPayment + $delivery + $ppnAmount;
        $returHeader->save();

        Session::flash('message', 'Berhasil membuat retur!');

        return redirect()->route('admin.returs.show', ['retur' => $returHeader]);
    }

    public function getIndex(){
        try{
            $quotationHeaders = ReturHeader::all();
            return DataTables::of($quotationHeaders)
                ->setTransformer(new ReturHeaderTransformer)
                ->addIndexColumn()
                ->make(true);
        }
        catch(\Exception $ex){
            error_log($ex);
        }
    }

    public function printDocument($id){
        $retur = ReturHeader::find($id);
        $dateNow = Carbon::now('Asia/Jakarta');
        $now = $dateNow->format('d-M-Y');

        $data = [
            'retur'                 => $retur,
            'now'                   => $now
        ];

        return view('documents.returs.retur_doc')->with($data);
    }
}