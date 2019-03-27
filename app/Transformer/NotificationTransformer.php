<?php
/**
 * Created by PhpStorm.
 * User: hellb
 * Date: 4/9/2018
 * Time: 10:18 AM
 */

namespace App\Transformer;

use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
    public function transform(DatabaseNotification $notif){
        $user = Auth::user();
        $dateCarbon = Carbon::parse($notif->created_at);

        $notification = "";
        if($notif->type === 'App\Notifications\MaterialRequestCreated'){
            if($notif->data['mr_type'] === 1){
                $mrType = 'other';
            }
            elseif($notif->data['mr_type'] === 2){
                $mrType = 'fuel';
            }
            elseif($notif->data['mr_type'] === 3){
                $mrType = 'oil';
            }
            else{
                $mrType = 'service';
            }
            $mrRouteStr = 'admin.material_requests.'. $mrType. '.show';
            $mrRoute = route($mrRouteStr, ['material_request' => $notif->data['mr_id']]);

            if($notif->data['receiver_is_creator'] === 'true'){
                if($notif->data['status_id'] === 13){
                    $notification .= "<span>MR </span><a style='text-decoration: underline;' href='". $mrRoute. "'>". $notif->data['mr_code']. "</a> anda telah ditolak";
                }
                else{
                    $notification .= "<span>MR </span><a style='text-decoration: underline;' href='". $mrRoute. "'>". $notif->data['mr_code']. "</a> anda telah disetujui";
                }
            }
            else{
                $notification .= "<span>MR </span><a style='text-decoration: underline;' href='". $mrRoute. "'>". $notif->data['mr_code']. "</a> telah dibuat, mohon buat PR";
            }
        }
        elseif($notif->type === 'App\Notifications\PurchaseRequestCreated'){
            if($user->roles->pluck('id')[0] === 13 || $user->roles->pluck('id')[0] === 14 || $user->roles->pluck('id')[0] === 15){
                $route = route('admin.purchase_requests.show', ['purchase_request' => $notif->data['pr_id']]);
                $notification .= "<span>PR </span><a style='text-decoration: underline;' href='". $route. "'>". $notif->data['code']. "</a> telah dibuat";
            }
            else{
                if($notif->data['receiver_is_creator'] === 'true'){
                    $mrType = 'default';
                    if($notif->data['mr_type'] === 1){
                        $mrType = 'other';
                    }
                    elseif($notif->data['mr_type'] === 2){
                        $mrType = 'fuel';
                    }
                    elseif($notif->data['mr_type'] === 3){
                        $mrType = 'oil';
                    }
                    else{
                        $mrType = 'service';
                    }
                    $mrRouteStr = 'admin.material_requests.'. $mrType. '.show';
                    $mrRoute = route($mrRouteStr, ['material_request' => $notif->data['mr_id']]);
                    $notification .= "<span>MR </span><a style='text-decoration: underline;' href='". $mrRoute. "'>". $notif->data['mr_code']. "</a> anda telah diproses ke PR";
                }
                else{
                    $route = route('admin.purchase_requests.show', ['purchase_request' => $notif->data['pr_id']]);
                    $notification .= "<span>PR </span><a style='text-decoration: underline;' href='". $route. "'>". $notif->data['code']. "</a> telah dibuat, mohon buat PO";
                }
            }
        }
        elseif($notif->type === 'App\Notifications\PurchaseOrderCreated'){
            $route = route('admin.purchase_orders.show', ['purchase_order' => $notif->data['po_id']]);
            if($user->roles->pluck('id')[0] === 13 || $user->roles->pluck('id')[0] === 14 || $user->roles->pluck('id')[0] === 15){
                $notification .= "<span>PO </span><a style='text-decoration: underline;' href='". $route. "'>". $notif->data['code']. "</a> telah dibuat";
            }
            else{
                if($notif->data['receiver_is_mr_creator'] === 'true'){
                    $mrType = 'default';
                    if($notif->data['mr_type'] === 1){
                        $mrType = 'other';
                    }
                    elseif($notif->data['mr_type'] === 2){
                        $mrType = 'fuel';
                    }
                    elseif($notif->data['mr_type'] === 3){
                        $mrType = 'oil';
                    }
                    else{
                        $mrType = 'service';
                    }
                    $mrRouteStr = 'admin.material_requests.'. $mrType. '.show';
                    $mrRoute = route($mrRouteStr, ['material_request' => $notif->data['mr_id']]);
                    $notification .= "<span>MR </span><a style='text-decoration: underline;' href='". $mrRoute. "'>". $notif->data['mr_code']. "</a> anda telah diproses ke PO";
                }
                else{
                    if($notif->data['receiver_is_creator'] === 'true'){
                        if($notif->data['status_id'] === 13){
                            $notification .= "<span>PO </span><a style='text-decoration: underline;' href='". $route. "'>". $notif->data['code']. "</a> anda telah ditolak";
                        }
                        else{
                            $notification .= "<span>PO </span><a style='text-decoration: underline;' href='". $route. "'>". $notif->data['code']. "</a> anda telah disetujui";
                        }
                    }
                }
            }
        }
        elseif($notif->type === 'App\Notifications\GoodsReceiptCreated'){
            if($user->roles->pluck('id')[0] === 13 ||  $user->roles->pluck('id')[0] === 14 || $user->roles->pluck('id')[0] === 15){
                $route = route('admin.item_receipts.show', ['item_receipt' => $notif->data['gr_id']]);
                $notification .= "<span>GR </span><a style='text-decoration: underline;' href='". $route. "'>". $notif->data['code']. "</a> telah dibuat";
            }
            else{
                if($notif->data['receiver_is_mr_creator'] === 'true'){
                    $mrType = 'default';
                    if($notif->data['mr_type'] === 1){
                        $mrType = 'other';
                    }
                    elseif($notif->data['mr_type'] === 2){
                        $mrType = 'fuel';
                    }
                    elseif($notif->data['mr_type'] === 3){
                        $mrType = 'oil';
                    }
                    else{
                        $mrType = 'service';
                    }
                    $mrRouteStr = 'admin.material_requests.'. $mrType. '.show';
                    $mrRoute = route($mrRouteStr, ['material_request' => $notif->data['mr_id']]);
                    $notification .= "<span>MR </span><a style='text-decoration: underline;' href='". $mrRoute. "'>". $notif->data['mr_code']. "</a> anda telah diproses ke GR";
                }
                elseif($notif->data['receiver_is_pr_creator'] === 'true'){
                    $prRoute = route('admin.purchase_requests.show', ['purchase_request' => $notif->data['pr_id']]);
                    $notification .= "<span>PR </span><a style='text-decoration: underline;' href='". $prRoute. "'>". $notif->data['pr_code']. "</a> anda telah diproses ke GR";
                }
            }
        }

        return[
            'document'      => $notif->data['document_type'],
            'notification'  => $notification,
            'sender'        => $notif->data['sender_name'],
            'created_at'    => $dateCarbon->toIso8601String()
        ];
    }
}