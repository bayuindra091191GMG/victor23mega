<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 17/07/2018
 * Time: 15:08
 */

namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestingMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     */
    public function __construct()
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $environment = env('APP_ENV','local');
        if($environment === 'local'){
            $fromMail = env('MAIL_USERNAME_LOCAL', 'ptvdtm.erp@bayu159753.com');
        }
        elseif($environment === 'dev'){
            $fromMail = env('MAIL_USERNAME_DEV', 'ptvdtm.erp@bayu159753.com');
        }
        else{
            $fromMail = env('MAIL_USERNAME_PROD', 'ptvdtm.erp@bayu159753.com');
        }

        $subject = 'Testing Mail from ptvdtm.erp';

        return $this->from($fromMail)
            ->subject($subject)
            ->view('email.test');
    }
}