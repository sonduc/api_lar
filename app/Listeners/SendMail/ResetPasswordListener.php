<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 28/11/2018
 * Time: 16:02
 */

namespace App\Listeners\SendMail;


use App\Events\Reset_Password_Event;
use App\Services\Email\SendEmail;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPasswordListener implements ShouldQueue
{
    protected $email;
    /**
     * Create the event listener.
     *
     * @return void
     */

    /**
     * sendBookingAdminListener constructor.
     *
     * @param SendEmail $email
     */
    public function __construct(SendEmail $email)
    {
        $this->email = $email;
    }


    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param BookingEvent $event
     *
     * @throws \Exception
     */
    public function handle(Reset_Password_Event $event)
    {
        $this->email->sendMailResetPassword($event);
    }

}
