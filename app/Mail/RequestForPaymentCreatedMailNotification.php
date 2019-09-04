<?php


namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequestForPaymentCreatedMailNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $paymentRequest;
    protected $user;

    /**
     * Create a new message instance.
     *
     * @param $paymentRequest
     * @param $user
     */
    public function __construct($paymentRequest, $user)
    {
        $this->paymentRequest = $paymentRequest;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $rfpShowRoute = route('admin.payment_requests.show', ['payment_request' => $this->paymentRequest]);

        $data =[
            'payment_request'       => $this->paymentRequest,
            'user'                  => $this->user,
            'url'                   => route('redirect', ['url' => $rfpShowRoute])
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

        $subject = 'Request for Payment '. $this->paymentRequest->code. ' Telah Dibuat';

        return $this->from($fromMail)
            ->subject($subject)
            ->view('email.request_for_payment_created_mail_notification')
            ->with($data);
    }
}