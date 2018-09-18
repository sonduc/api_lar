<?php

namespace App\Listeners;

use App\Events\BookingEvent;
use App\Events\ExampleEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMail
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
     * Handle the event.
     *
     * @param  ExampleEvent  $event
     * @return void
     */
    public function handle(BookingEvent $event)
    {
//        dd($event->data);
        $job = (new JobEmail($event->data))
            ->onQueue('email');
        //              ->delay(5);
        dispatch($job);

    }
}
