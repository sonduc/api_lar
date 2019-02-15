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

        // Host xác nhân booking này và chuyển về trạng thái là đơn đã xác nhận
        \App\Events\BookingConfirmEvent::class => [
            \App\Listeners\SendMail\sendBookingConfirmedCustomerListener::class,
        ],

        // nếu quá thời gian cho phép mà host không xács nhận booking này thì chuyển về trạng thái hủy
        \App\Events\ConfirmBookingTime::class => [
            \App\Listeners\Logic\ConfirmBookingTimeListener::class,
        ],

        \App\Events\Reset_Password_Event::class => [
            \App\Listeners\SendMail\ResetPasswordListener::class,
        ],

       // set password cho customer nếu người này tạo booking mà chưa có tài khoản
        \App\Events\Customer_Register_TypeBooking_Event::class => [
            \App\Listeners\SendMail\SetPasswordListener::class,
        ],

        \App\Events\Booking_Notification_Event::class => [
            \App\Listeners\SendMail\BookingNotificationListener::class,
        ],

        \App\Events\Booking_Reviews_Event::class => [
            \App\Listeners\SendMail\BookingReviewsListener::class,
        ],

        \App\Events\CreateBookingTransactionEvent::class => [
            \App\Listeners\Transactions\CreateBookingTransactionListener::class,
        ],

        // Sự kiện host review customer
        \App\Events\Host_Reviews_Event::class => [
            \App\Listeners\SendMail\HostReviewListener::class,
        ],

        // tạo link Westay calendar để đồng bộ
        \App\Events\GenerateWestayRoomCalendarEvent::class => [
            \App\Listeners\GenerateWestayRoomCalendarListener::class,
        ],
        // Notification for host
        \App\Events\Booking_Host_Notification_Event::class => [
            \App\Listeners\SendMail\BookingHostNotificationListener::class,
        ],
    ];
}
