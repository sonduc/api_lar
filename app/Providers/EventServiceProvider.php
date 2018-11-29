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
        \App\Events\AverageRoomRating::class => [
            \App\Listeners\AverageRoomRatingListener::class,
        ],

        \App\Events\BookingEvent::class => [
            \App\Listeners\SendMail\sendBookingAdminListener::class,
            \App\Listeners\SendMail\sendBookingCustomerListener::class,
            \App\Listeners\SendMail\sendBookingHostListener::class
        ],

        \App\Events\BookingConfirmEvent::class => [
            \App\Listeners\SendMail\sendBookingConfirmedCustomerListener::class,
        ],

        \App\Events\ConfirmBookingTime::class => [
            \App\Listeners\Logic\ConfirmBookingTimeListener::class,
        ],

        \App\Events\Reset_Password_Event::class => [
            \App\Listeners\SendMail\ResetPasswordListener::class,
        ],

        \App\Events\Booking_Notification_Event::class => [
            \App\Listeners\SendMail\BookingNotificationListener::class,
        ],
        \App\Events\Booking_Reviews_Event::class => [
            \App\Listeners\SendMail\BookingReviewsListener::class,
        ],
    ];
}
