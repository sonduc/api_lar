<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 15/11/2018
 * Time: 06:58
 */

namespace App\Listeners\SendMail;

use App\Events\BookingEvent;
use App\Services\Email\SendEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
class MailInfoBookingListener
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


    public function handle(BookingEvent $event)
    {
        dd($event);
        $this->email->mailInfo($event);

    }


}
