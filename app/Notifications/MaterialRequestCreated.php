<?php

namespace App\Notifications;

use App\Models\MaterialRequestHeader;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MaterialRequestCreated extends Notification implements ShouldQueue
{
    use Queueable;
    protected $materialRequest;
    protected $isInStock;
    protected $isCreator;

    /**
     * Create a new notification instance.
     *
     * @param MaterialRequestHeader $header
     * @param bool $isInStock
     */
    public function __construct(MaterialRequestHeader $header, bool $isInStock, $isCreator)
    {
        $this->materialRequest = $header;
        $this->isInStock = $isInStock;
        $this->isCreator = $isCreator;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable){
        if($this->materialRequest->type == 1){
            $documentType = 'Material Request Part & Non-Part';
        }
        elseif($this->materialRequest->type == 2){
            $documentType = 'Material Request BBM';
        }
        elseif($this->materialRequest->type == 3){
            $documentType = 'Material Request Oli';
        }
        else{
            $documentType = 'Material Request Servis';
        }

//        if(!$this->isInStock){
//            $roleIds = [4,5];
//        }
//        else{
//            $roleIds = [4,6];
//        }

        $roleIds = [4,5,8,12];

        return [
            'document_type'         => $documentType,
            'mr_id'                 => $this->materialRequest->id,
            'mr_code'               => $this->materialRequest->code,
            'mr_type'               => $this->materialRequest->type,
            'status_id'             => $this->materialRequest->status_id,
            'sender_id'             => $this->materialRequest->created_by,
            'sender_name'           => $this->materialRequest->createdBy->name,
            'receiver_id'           => 0,
            'redeiver_role_id'      => $roleIds,
            'receiver_is_creator'   => $this->isCreator
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        if($this->materialRequest->type == 1){
            $documentType = 'Material Request Part & Non-Part';
        }
        elseif($this->materialRequest->type == 2){
            $documentType = 'Material Request BBM';
        }
        elseif($this->materialRequest->type == 3){
            $documentType = 'Material Request Oli';
        }
        else{
            $documentType = 'Material Request Servis';
        }

        if(!$this->isInStock){
            $roleIds = [4,5];
        }
        else{
            $roleIds = [4,6];
        }

        return [
            'id'        => $this->id,
            'read_at'   => null,
            'data'      => [
                'document_type'         => $documentType,
                'mr_id'                 => $this->materialRequest->id,
                'mr_code'               => $this->materialRequest->code,
                'mr_type'               => $this->materialRequest->type,
                'status_id'             => $this->materialRequest->status_id,
                'sender_id'             => $this->materialRequest->created_by,
                'sender_name'           => $this->materialRequest->createdBy->name,
                'receiver_id'           => 0,
                'redeiver_role_id'      => $roleIds,
                'receiver_is_creator'   => $this->isCreator
            ],
        ];
    }
}
