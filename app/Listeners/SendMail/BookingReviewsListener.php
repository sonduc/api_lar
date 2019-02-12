<?php

namespace App\Listeners\SendMail;

use App\Events\Booking_Reviews_Event;
use App\Services\Email\SendEmail;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookingReviewsListener implements ShouldQueue
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


    public function handle(Booking_Reviews_Event $event)
    {
        // dd($event->name->email);
        $this->email->mailReviewsBooking($event);
    }
}
