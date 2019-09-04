<?php


namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseRequestCreatedMailNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $purchaseRequest;
    protected $user;

    /**
     * Create a new message instance.
     *
     * @param $purchaseRequest
     * @param $user
     */
    public function __construct($purchaseRequest, $user)
    {
        $this->purchaseRequest = $purchaseRequest;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $prShowRoute = route('admin.purchase_requests.show', ['purchase_request' => $this->purchaseRequest]);

        $data =[
            'purchase_request'      => $this->purchaseRequest,
            'user'                  => $this->user,
            'url'                   => route('redirect', ['url' => $prShowRoute])
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

        $subject = 'Purchase Request '. $this->purchaseRequest->code. ' Telah Dibuat';

        return $this->from($fromMail)
            ->subject($subject)
            ->view('email.purchase_request_created_mail_notification')
            ->with($data);
    }
}