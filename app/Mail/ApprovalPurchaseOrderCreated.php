<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApprovalPurchaseOrderCreated extends Mailable
{
    use Queueable, SerializesModels;

    protected $purchaseOrder;
    protected $user;

    /**
     * Create a new message instance.
     *
     * @param $purchaseOrder
     * @param $user
     */
    public function __construct($purchaseOrder, $user)
    {
        $this->purchaseOrder = $purchaseOrder;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $poShowRoute = route('admin.purchase_orders.show', ['purchase_order' => $this->purchaseOrder->id]);

        $data =[
            'purchase_request'      => $this->purchaseOrder,
            'user'                  => $this->user,
            'url'                   => route('redirect', ['url' => $poShowRoute])
        ];

        $environment = env('APP_ENV','local');
        if($environment === 'local'){
            $fromMail = env('MAIL_USERNAME_LOCAL', 'hellbardx333@gmail.com');
        }
        elseif($environment === 'dev'){
            $fromMail = env('MAIL_USERNAME_DEV', 'admin@bayu159753.com');
        }
        else{
            $fromMail = env('MAIL_USERNAME_PROD', 'ptvdtm.erp@bayu159753.com');
        }

        $subject = 'Permintaan Approval Purchase Order '. $this->purchaseOrder->code;

        return $this->from($fromMail)
            ->subject($subject)
            ->view('email.approval_purchase_order_created')
            ->with($data);
    }
}
