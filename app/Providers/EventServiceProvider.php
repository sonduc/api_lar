<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\BookingEvent::class          => [
            \App\Listeners\SendMailListener::class,
        ],
        \App\Events\AmazonS3_Upload_Event::class => [
            \App\Listeners\AWS\S3UploadListener::class,
        ],
    ];
}
