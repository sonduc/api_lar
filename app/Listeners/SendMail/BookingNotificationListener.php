<?php

namespace App\Listeners\SendMail;


use App\Events\Booking_Notification_Event;
use App\Services\Email\SendEmail;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookingNotificationListener implements ShouldQueue
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


    public function handle(Booking_Notification_Event $event)
    {
        $this->email->mailNotificationBooking($event);
    }

}
