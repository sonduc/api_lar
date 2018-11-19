<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 16/11/2018
 * Time: 13:21
 */

namespace App\Listeners\SendMail;

use App\Events\BookingConfirmEvent;
use App\Services\Email\SendEmail;
use Illuminate\Contracts\Queue\ShouldQueue;

class sendBookingConfirmCustomer implements ShouldQueue
{
    protected $email;

    /**
     * sendBookingConfirmCustomer constructor.
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
     * @param BookingConfirmEvent $event
     *
     * @throws \Exception
     */
    public function handle(BookingConfirmEvent $event)
    {
        $this->email->sendBookingConfirmedCustomer($event);

    }

}
