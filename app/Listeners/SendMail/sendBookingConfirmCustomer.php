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
     * Create the event listener.
     *
     * @return void
     */

    public function __construct(SendEmail $email)
    {
        $this->email = $email;
    }


    public function handle(BookingConfirmEvent $event)
    {
        $this->email->sendBookingConfirmCustomer($event);

    }

}
