<?php


namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MaterialRequestApprovedMailNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $materialRequest;
    protected $user;

    /**
     * Create a new message instance.
     *
     * @param $materialRequest
     * @param $user
     */
    public function __construct($materialRequest, $user)
    {
        $this->materialRequest = $materialRequest;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->materialRequest->type === 1){
            $mrShowRoute = route('admin.material_requests.other.show', ['material_request' => $this->materialRequest]);
        }
        else if($this->materialRequest->type === 2){
            $mrShowRoute = route('admin.material_requests.fuel.show', ['material_request' => $this->materialRequest]);
        }
        else if($this->materialRequest->type === 3){
            $mrShowRoute = route('admin.material_requests.oil.show', ['material_request' => $this->materialRequest]);
        }
        else{
            $mrShowRoute = route('admin.material_requests.service.show', ['material_request' => $this->materialRequest]);
        }

        $data =[
            'material_request'      => $this->materialRequest,
            'user'                  => $this->user,
            'url'                   => route('redirect', ['url' => $mrShowRoute])
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

        $subject = 'Material Request '. $this->materialRequest->code. ' Telah Di-approve';

        return $this->from($fromMail)
            ->subject($subject)
            ->view('email.material_request_approved_mail_notification')
            ->with($data);
    }
}