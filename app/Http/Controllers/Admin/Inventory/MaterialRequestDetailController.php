<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 21/03/2018
 * Time: 9:47
 */

namespace App\Http\Controllers\Admin\Inventory;


use App\Http\Controllers\Controller;
use App\Models\MaterialRequestDetail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class MaterialRequestDetailController extends Controller
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

            $detail = new MaterialRequestDetail();
            $detail->header_id = $request->input('header_id');
            $detail->item_id = $request->input('item');
            $detail->quantity = $request->input('qty');
            $detail->quantity_issued = 0;
            $detail->quantity_received = 0;
            $detail->quantity_retur = 0;

            if($request->filled('remark')) $detail->remark = $request->input('remark');

            $detail->save();

            $json = MaterialRequestDetail::with('item')->find($detail->id);

            return new JsonResponse($json);
        }
        catch(\Exception $ex){
            error_log($ex);
        }
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

            $detail = MaterialRequestDetail::find($request->input('id'));
            $oldItemId = $detail->item_id;

            $user = Auth::user();

            if($request->filled('item')){
                $detail->item_id = $request->input('item');

                Log::info('User '. $user->email. ': Update material_request_detail ID '. $request->input('id'). ': item ID '. $oldItemId. ' changed to ID '. $request->input('item'));
            }
            else{
                Log::info('User '. $user->email. ': Update material_request_detail ID '. $request->input('id'));
            }

            $detail->quantity = $request->input('qty');
            $detail->remark = $request->input('remark');

            $detail->save();

            $json = MaterialRequestDetail::with('item')->find($detail->id);
        }
        catch (\Exception $ex){
            error_log($ex);
        }

        return new JsonResponse($json);
    }

    public function delete(Request $request){
        try{
            $detail = MaterialRequestDetail::find($request->input('id'));
            $detail->delete();

            $user = Auth::user();
            Log::info('User '. $user->email. ': Delete material_request_detail ID '. $request->input('id'));

            return new JsonResponse($detail);
        }
        catch (\Exception $ex){
            error_log($ex);
        }
    }
}