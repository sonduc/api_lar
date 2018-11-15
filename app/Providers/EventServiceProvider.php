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

        \App\Events\AmazonS3_Upload_Event::class => [
            \App\Listeners\AWS\S3UploadListener::class,
        ],
        \App\Events\Customer_Register_Event::class => [
            \App\Listeners\SendMail\MailConfirmListener::class,
        ],
        \App\Events\Check_Usable_Coupon_Event::class => [
            \App\Listeners\CheckCoupon\UsableCouponListener::class,
        ],
    ];
}
