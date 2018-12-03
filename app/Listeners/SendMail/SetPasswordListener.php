<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 03/12/2018
 * Time: 17:28
 */

namespace App\Listeners\SendMail;


use App\Events\Customer_Register_TypeBooking_Event;
use App\Services\Email\SendEmail;
use Illuminate\Contracts\Queue\ShouldQueue;

class SetPasswordListener
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


    public function handle(Customer_Register_TypeBooking_Event $event)
    {
        $this->email->setPassword($event);

    }


}
