<?php
/**
 * Created by PhpStorm.
 * User: hellb
 * Date: 6/24/2018
 * Time: 5:35 PM
 */

namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovalMaterialRequestCreated extends Mailable
{
    use Queueable, SerializesModels;

    protected $materialRequest;
    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
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
            $routeName = 'admin.material_requests.other.show';
        }
        else if($this->materialRequest->type === 2){
            $routeName = 'admin.material_requests.fuel.show';
        }
        else if($this->materialRequest->type === 3){
            $routeName = 'admin.material_requests.oil.show';
        }
        else{
            $routeName = 'admin.material_requests.service.show';
        }

        $mrShowRoute = route($routeName, ['material_request' => $this->materialRequest->id]);

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

        $subject = 'Permintaan Approval Material Request '. $this->materialRequest->code;

        return $this->from($fromMail)
            ->subject($subject)
            ->view('email.approval_material_request_created')
            ->with($data);
    }
}