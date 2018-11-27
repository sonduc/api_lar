<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Bookings\BookingConstant;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Events\Booking_Notification_Event;
use App\Events\Booking_Reviews_Event;

use Carbon\Carbon;

class BookingUpdateStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change booking status';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(BookingRepositoryInterface $booking)
    {
        parent::__construct();
        $this->booking    = $booking;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Carbon::setLocale(getLocale());
        $bookings                   = $this->booking->getAllBookingFuture();
        $current_day                = Carbon::now();
        foreach ($bookings as $key => $booking) {
            $checkin                = $booking->checkin;
            $checkout               = $booking->checkout;
            $checkin_timestamp      = Carbon::parse($checkin);
            $checkout_timestamp     = Carbon::parse($checkout);
            if ($booking->email_reminder == 0) {
                if ($current_day->diffInHours($checkin_timestamp) <= 48) {
                    event(new Booking_Notification_Event($booking));
                    $booking->email_reminder = 1;
                    $booking->save();
                }
            }
            if ($checkout_timestamp->diffInSeconds($current_day) <= 0 && $booking->status == BookingConstant::BOOKING_USING) {
                $booking->status = BookingConstant::BOOKING_COMPLETE;
                $booking->save();
            }
            if ($checkin_timestamp->diffInSeconds($current_day) >= 0 && ($booking->status == BookingConstant::BOOKING_CONFIRM || $booking->status == BookingConstant::BOOKING_NEW)) {
                $booking->status = BookingConstant::BOOKING_USING;
                $booking->save();
            }
            if ($checkout_timestamp->diffInHours($current_day) >= 24 && $booking->status == BookingConstant::BOOKING_COMPLETE) {
                if ($bookings->booking_type == BookingConstant::BOOKING_CONFIRM) {
                    $booking->read_timeCheckin  = $checkin->isoFormat('LL');
                    $booking->read_timeCheckout = $checkout->isoFormat('LL');
                    $booking->count_bookingTime = $checkin->diffInDays($checkout) +1;
                    event(new Booking_Reviews_Event($booking));
                }
                if ($bookings->booking_type == BookingConstant::BOOKING_NEW) {
                    $booking->read_timeBooking  = $checkin->isoFormat('LL');
                    $booking->count_bookingTime = $checkin->diffInHours($checkout);
                    event(new Booking_Reviews_Event($booking));
                }
            }
        }
    }
}
