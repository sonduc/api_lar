<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 06/11/2018
 * Time: 12:44
 */

namespace App\Listeners\SendMail;


use App\Events\Customer_Register_Event;
use App\Services\Email\SendEmail;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailConfirmListener implements ShouldQueue
{
    protected $email;
    /**
     * Create the event listener.
     *
     * @return void
     */

    public function __construct(SendEmail $email)
    {
        $this->email = $email;
    }


    public function handle(Customer_Register_Event $event)
    {
        $this->email->mailConfirm($event);
    }

}
