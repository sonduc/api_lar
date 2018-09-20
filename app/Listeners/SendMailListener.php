<?php

namespace App\Listeners;

use App\Events\BookingEvent;
use App\Jobs\SendMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
