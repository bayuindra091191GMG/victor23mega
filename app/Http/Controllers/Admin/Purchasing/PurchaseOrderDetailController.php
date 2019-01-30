<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 28/02/2018
 * Time: 11:02
 */

namespace App\Http\Controllers\Admin\Purchasing;


use App\Http\Controllers\Controller;
use App\Libs\Utilities;
use App\Models\Item;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseOrderHeader;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderDetailController extends Controller
{
    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'item'      => 'required',
                'qty'       => 'required',
                'price'     => 'required',
                'remark'    => 'max:200'
            ]);

            if ($validator->fails()) {
                return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
            }

            $detail = new PurchaseOrderDetail();
            $detail->header_id = $request->input('header_id');
            $detail->item_id = $request->input('item');

            $qty = (double) $request->input('qty');
            $detail->quantity = $qty;

            $price = Utilities::toFloat($request->input('price'));
            $detail->price = $price;

            // Check discount and subtotal
            $finalSubtotal = 0;
            $discountAmount = 0;
            if($request->filled('discount')){
                $discount = Utilities::toFloat($request->input('discount'));
                $detail->discount = $discount;
                $finalSubtotal = ($qty * $price) - $discount;
                $detail->subtotal = $finalSubtotal;
            }
            else{
                $finalSubtotal = ($qty * $price);
                $detail->subtotal = $finalSubtotal;
            }

            if(!empty(Input::get('remark'))) $detail->remark = Input::get('remark');

            $detail->save();

            // Accumulate total price, discount & payment
            $header = PurchaseOrderHeader::find($request->input('header_id'));
            $totalPrice = $header->total_price + ($qty * $price);
            $header->total_price = $totalPrice;

            $totalPayment = $header->total_payment_before_tax + $finalSubtotal;
            $header->total_payment_before_tax = $totalPayment;

            // Get PPN & PPh
            $ppnAmount = 0;
            if(!empty($header->ppn_percent) && $header->ppn_percent > 0){
                $ppnAmount = $totalPayment * (10 / 100);
                $header->ppn_percent = 10;
                $header->ppn_amount = $ppnAmount;
            }
            else{
                $header->ppn_percent = null;
                $header->ppn_amount = null;
            }

            $pphAmount = 0;
            if(!empty($header->pph_amount) && $header->pph_amount > 0){
                $pphAmount = $header->pph_amount;
            }
            else{
                $header->pph_percent = null;
                $header->pph_amount = null;
            }

            $header->total_payment = $totalPayment + $ppnAmount - $pphAmount;
            if($request->filled('discount')){
                $header->total_discount += $discountAmount;
            }
            $header->save();

            $json = PurchaseOrderDetail::with('item')->find($detail->id);
            return new JsonResponse($json);
        }
        catch (\Exception $ex){
            error_log($ex);
        }
    }

    public function update(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'qty'       => 'required',
                'price'     => 'required',
                'remark'    => 'max:200'
            ]);

            if ($validator->fails()) {
                return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
            }

            $detail = PurchaseOrderDetail::find($request->input('id'));
            $oldItemId = $detail->item_id;

            $user = Auth::user();

            // Get old value
            $oldPrice = $detail->price;
            $oldDiscountAmount = 0;
            $oldSubtotal = $detail->subtotal;
            $oldQty = $detail->quantity;

            if($request->filled('item')){
                $detail->item_id = $request->input('item');

                Log::info('User '. $user->email. ': Update purchase_order_detail ID '. $request->input('id'). ': item ID '. $oldItemId. ' changed to ID '. $request->input('item'));
            }
            else{
                Log::info('User '. $user->email. ': Update purchase_order_detail ID '. $request->input('id'));
            }

            $qty = (double) $request->input('qty');

            $detail->quantity = $qty;
            $price = Utilities::toFloat($request->input('price'));
            $detail->price = $price;

            // Check discount and subtotal
            $finalSubtotal = 0;
            $discountAmount = 0;
            $oldDiscountAmount =  $detail->discount;
            if($request->filled('discount') && $request->input('discount') != '0'){
                $discountAmount = Utilities::toFloat($request->input('discount'));
                $detail->discount = $discountAmount;
                $finalSubtotal = ($qty * $price) - $discountAmount;
                $detail->subtotal = $finalSubtotal;
            }
            else{
                $detail->discount = 0;
                $finalSubtotal = ($qty * $price);
                $detail->subtotal = $finalSubtotal;
            }

            if($request->filled('remark')) $detail->remark = $request->input('remark');

            $detail->save();

            // Accumulate total price, discount & payment
            $header = PurchaseOrderHeader::find($detail->header_id);
            $totalPrice = $header->total_price - ($oldQty * $oldPrice) + ($qty * $price);
            $header->total_price = $totalPrice;

            $totalPayment = $header->total_payment_before_tax - $oldSubtotal + $finalSubtotal;
            $header->total_payment_before_tax = $totalPayment;

            $header->total_discount = $header->total_discount - $oldDiscountAmount + $discountAmount;

            // Get PPN & PPh
            $ppnAmount = 0;
            if(!empty($header->ppn_percent) && $header->ppn_percent > 0){
                $ppnAmount = $totalPayment * (10 / 100);
                $header->ppn_percent = 10;
                $header->ppn_amount = $ppnAmount;
            }
            else{
                $header->ppn_percent = null;
                $header->ppn_amount = null;
            }

            $pphAmount = 0;
            if(!empty($header->pph_amount) && $header->pph_amount > 0){
                $pphAmount = $header->pph_amount;
            }
            else{
                $header->pph_percent = null;
                $header->pph_amount = null;
            }

            // Get delivery fee
            $deliveryFee = $header->delivery_fee ?? 0;

            $header->total_payment = $totalPayment + $deliveryFee + $ppnAmount - $pphAmount;

            $now = Carbon::now('Asia/Jakarta');
            $user = Auth::user();
            $header->updated_by = $user->id;
            $header->updated_at = $now->toDateTimeString();
            $header->save();

            // Validate PR relation
            $prHeader = $header->purchase_request_header;
            foreach($prHeader->purchase_request_details as $prDetail){
                if($prDetail->item_id === $detail->item_id){
                    $prDetail->quantity_poed = $detail->quantity;
                    $prDetail->save();
                }
            }

            // Check all poed
            $isAllPoed = true;
            foreach($prHeader->purchase_request_details as $prDetail){
                if($prDetail->quantity_poed < $prDetail->quantity){
                    $isAllPoed = false;
                }
            }

            // Recount stock on order
            $item = Item::find($detail->item_id);
            if($item->stock_on_order >= $oldQty){
                $item->stock_on_order -= $oldQty;
            }
            $item->stock_on_order += $qty;
            $item->save();

            if(!$isAllPoed){
                $prHeader->is_all_poed = 0;
                $prHeader->save();
            }

            $json = PurchaseOrderDetail::with('item')->find($detail->id);
            return new JsonResponse($json);
        }
        catch(\Exception $ex){
            error_log($ex);
        }
    }

    public function delete(Request $request){
        try{

            $details = PurchaseOrderDetail::where('header_id', Input::get('header_id'))->get();
            if($details->count() == 1){
                return Response::json(array('errors' => 'INVALID'));
            }

            $detail = PurchaseOrderDetail::find(Input::get('id'));

            // Get old value
            $oldPrice = $detail->price;
            $oldDiscountAmount = $detail->discount;
            $oldSubtotal = $detail->subtotal;
            $oldQty = $detail->quantity;

            // Minus header total values
            $header = PurchaseOrderHeader::find($detail->header_id);
            $header->total_price = $header->total_price - ($oldQty * $oldPrice);

            $totalPayment = $header->total_payment_before_tax - $oldSubtotal;
            $header->total_payment_before_tax = $totalPayment;

            $header->total_discount = $header->total_discount - $oldDiscountAmount;

            // Get PPN & PPh
            $ppnAmount = 0;
            if(!empty($header->ppn_percent) && $header->ppn_percent > 0){
                $ppnAmount = $totalPayment * (10 / 100);
                $header->ppn_percent = 10;
                $header->ppn_amount = $ppnAmount;
            }
            else{
                $header->ppn_percent = null;
                $header->ppn_amount = null;
            }

            $pphAmount = 0;
            if(!empty($header->pph_amount) && $header->pph_amount > 0){
                $pphAmount = $header->pph_amount;
            }
            else{
                $header->pph_percent = null;
                $header->pph_amount = null;
            }

            // Get delivery fee
            $deliveryFee = $header->delivery_fee ?? 0;

            $header->total_payment = $totalPayment + $deliveryFee + $ppnAmount - $pphAmount;

            $now = Carbon::now('Asia/Jakarta');
            $user = Auth::user();
            $header->updated_by = $user->id;
            $header->updated_at = $now->toDateTimeString();
            $header->save();

            // Validate PR relation
            $prHeader = $header->purchase_request_header;
            foreach($prHeader->purchase_request_details as $prDetail){
                if($prDetail->item_id === $detail->item_id){
                    $prDetail->quantity_poed -= $detail->quantity;
                    $prDetail->save();
                }
            }

            $prHeader->is_all_poed = 0;
            $prHeader->save();

            // Recount stock on order
            $item = Item::find($detail->item_id);
            if($item->stock_on_order >= $detail->quantity){
                $item->stock_on_order -= $detail->quantity;
                $item->save();
            }

            // Delete purchase order detail completely
            $detail->delete();

            $user = Auth::user();
            Log::info('User '. $user->email. ': Delete purchase_order_detail ID '. $request->input('id'));

            return new JsonResponse($detail);
        }
        catch (\Exception $ex){
            error_log($ex);
        }
    }
}