<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 08/02/2018
 * Time: 15:05
 */

namespace App\Http\Controllers\Admin\Purchasing;


use App\Http\Controllers\Controller;
use App\Models\PurchaseRequestDetail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class PurchaseRequestDetailController extends Controller
{
    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'item'      => 'required',
                'qty'       => 'required',
                'remark'    => 'max:200'
            ]);

            if ($validator->fails()) {
                return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
            }

            $detail = new PurchaseRequestDetail();
            $detail->header_id = Input::get('header_id');
            $detail->item_id = Input::get('item');
            $detail->quantity = Input::get('qty');

            if(!empty(Input::get('remark'))) $detail->remark = Input::get('remark');

            $detail->save();

            $json = PurchaseRequestDetail::with('item')->find($detail->id);
        }
        catch(\Exception $ex){
            error_log($ex);
        }

        return new JsonResponse($json);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'qty'       => 'required',
                'remark'    => 'max:200'
            ]);

            if ($validator->fails()) {
                return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
            }

            $detail = PurchaseRequestDetail::find(Input::get('id'));
            $oldItemId = $detail->item_id;

            $user = Auth::user();

            if(!empty(Input::get('item'))){
                $detail->item_id = Input::get('item');

                Log::info('User '. $user->email. ': Update purchase_request_detail ID '. $request->input('id'). ': item ID '. $oldItemId. ' changed to ID '. $request->input('item'));
            }
            else{
                Log::info('User '. $user->email. ': Update purchase_request_detail ID '. $request->input('id'));
            }

            $detail->quantity = Input::get('qty');
            $detail->remark = Input::get('remark');

            if(!empty(Input::get('date'))){
                $date = Carbon::createFromFormat('d M Y', Input::get('date'), 'Asia/Jakarta');
                $detail->delivery_date = $date;
            }

            $detail->save();

            $json = PurchaseRequestDetail::with('item')->find($detail->id);
        }
        catch (\Exception $ex){
            error_log($ex);
        }

        return new JsonResponse($json);
    }

    public function delete(Request $request){
        try{

            $detail = PurchaseRequestDetail::find(Input::get('id'));

            // Validate detail count
            $details = PurchaseRequestDetail::where('header_id', $detail->header_id)->get();
            if($details->count() == 1){
                return Response::json(array('errors' => 'INVALID'));
            }

            $detail->delete();

            $user = Auth::user();
            Log::info('User '. $user->email. ': Delete purchase_request_detail ID '. $request->input('id'));

            return new JsonResponse($detail);
        }
        catch (\Exception $ex){
            error_log($ex);
        }
    }
}