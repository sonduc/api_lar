<?php

namespace App\Listeners;

use App\Events\BookingEvent;
use App\Jobs\SendMail;

class SendMailListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     *
     * @param BookingEvent $event
     */
    public function handle(BookingEvent $event)
    {
        $job = (new SendMail($event->data))
//            ->delay(5)
            ->onQueue('email');
        dispatch($job);
    }
}
