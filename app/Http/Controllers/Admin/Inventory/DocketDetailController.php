<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\IssuedDocketDetail;
use App\Models\IssuedDocketHeader;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\StockCard;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class DocketDetailController extends Controller
{
    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'item'      => 'required',
                'qty'       => 'required',
                'time'      => 'required',
                'remark'    => 'max:200'
            ]);

            if ($validator->fails()) {
                return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
            }

            //Check and Update Stock
            $item = Item::where('id', Input::get('item'))->first();
            $qty = Input::get('qty');
            if($item->stock < $qty){
                return Response::json(array('errors' => ['Not Enough Stock!']));
            }

            $detail = new IssuedDocketDetail();
            $detail->header_id = Input::get('header_id');
            $detail->item_id = Input::get('item');
            $detail->quantity = Input::get('qty');
            $detail->time = Input::get('time');

            if(!empty(Input::get('remark'))) $detail->remarks = Input::get('remark');

            $detail->save();
            $item->stock = $item->stock - $detail->quantity;
            $item->save();

            error_log($detail->id);

            $json = IssuedDocketDetail::with('item')->find($detail->id);
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
                'time'      => 'required',
                'remark'    => 'max:200'
            ]);

            if ($validator->fails()) {
                return Response::json(array('errors' => $validator->getMessageBag()->toArray()));
            }

            $detail = IssuedDocketDetail::find(Input::get('id'));

            if(!empty(Input::get('item'))){
                $detail->item_id = Input::get('item');
            }

            //Check and Update Stock
            $item = Item::where('id', $detail->item_id)->first();

            $qty = Input::get('qty');
            if($item->stock < $qty){
                return Response::json(array('errors' => ['Not Enough Stock!']));
            }

            $item->stock = $item->stock + $detail->quantity;

            $detail->quantity = Input::get('qty');
            $detail->remarks = Input::get('remark');
            $detail->time = Input::get('time');

            //Update Stock
            $item->stock = $item->stock - $detail->quantity;

            if(!empty(Input::get('date'))){
                $date = Carbon::createFromFormat('d M Y', Input::get('date'), 'Asia/Jakarta');
                $detail->delivery_date = $date;
            }

            $detail->save();
            $item->save();

            $json = IssuedDocketDetail::with('item')->find($detail->id);
        }
        catch (\Exception $ex){
            error_log($ex);
        }

        return new JsonResponse($json);
    }

    public function delete(Request $request){
        try{
            // Check for minimun 1 detail
            $details = IssuedDocketDetail::where('header_id', Input::get('header_id'))->get();
            if($details->count() == 1){
                return Response::json(array('errors' => 'INVALID'));
            }

            $detail = IssuedDocketDetail::find(Input::get('id'));
            $detail->delete();

            return new JsonResponse($detail);
        }
        catch (\Exception $ex){
            error_log($ex);
        }
    }

    public function retur(Request $request){
        $validator = Validator::make($request->all(),[
            'qty_retur'     => 'required',
            'reason'        => 'required|max: 200'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $qtyRetur = (int) $request->input('qty_retur');

        $docketDetail = IssuedDocketDetail::find($request->input('detail_id'));

        // Validate retur amount
        if($docketDetail->quantity_retur === $docketDetail->quantity){
            return redirect()->back()->withErrors('Inventory sudah diretur!', 'default')->withInput($request->all());
        }
        else{
            $qtyLeft = $docketDetail->quantity - $docketDetail->quantity_retur;
            if($qtyRetur > $qtyLeft){
                return redirect()->back()->withErrors('QTY retur tidak boleh melebihi QTY inventory issued docket!', 'default')->withInput($request->all());
            }
        }

        $docketDetail->quantity_retur = $qtyRetur;
        $docketDetail->remark_retur = $request->input('reason');
        $docketDetail->save();

        // Check retur summary of header
        $docketHeader = $docketDetail->issued_docket_header;
        $isAllRetur = true;

        $mrHeader = $docketHeader->material_request_header;
        foreach ($docketHeader->issued_docket_details as $detail){
            if($detail->quantity_retur < $detail->quantity){
                $isAllRetur = false;
            }

            // Restore MR quantity issued
            $mrDetail = $mrHeader->material_request_details->where('item_id', $detail->item_id)->first();
            $mrDetail->quantity_issued -= $detail->quantity;
            $mrDetail->save();
        }

        if($isAllRetur){
            $docketHeader->is_retur = 2;
        }
        else{
            $docketHeader->is_retur = 1;
        }
        $docketHeader->save();

        // Restore MR status
        $mrHeader->status_id = 3;

        // Restore MR issued status
        $isAllReturned = true;
        foreach($mrHeader->material_request_details as $detail){
            if($detail->quantity_issued > 0){
                $isAllReturned = false;
            }
        }

        $mrHeader->is_all_issued = $isAllReturned ? 0 : 1;
        $mrHeader->save();

        // Return selected item
        $item = $docketDetail->item;
        $item->stock += $qtyRetur;
        $item->save();

        $itemStock = ItemStock::where('item_id', $item->id)
            ->where('warehouse_id', $docketHeader->warehouse_id)
            ->first();
        $itemStock->stock += $qtyRetur;
        $itemStock->save();

        // Get warehouse stock result
        $stockResultWarehouse = $itemStock->stock;

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');

        // Create stock card
        StockCard::create([
            'item_id'               => $item->id,
            'in_qty'                => $qtyRetur,
            'out_qty'               => 0,
            'result_qty'            => $itemStock->stock,
            'result_qty_warehouse'  => $stockResultWarehouse,
            'warehouse_id'          => $docketHeader->warehouse_id,
            'created_by'            => $user->id,
            'created_at'            => $now,
            'updated_by'            => $user->id,
            'updated_at'            => $now->toDateTimeString(),
            'reference'             => 'Issued Docket Retur ' . $docketHeader->code
        ]);

        Session::flash('message', 'Berhasil mengembalikan inventory Issued Docket!');

        return redirect()->route('admin.issued_dockets.show', ['issued_docket' => $docketHeader->id]);
    }
}
