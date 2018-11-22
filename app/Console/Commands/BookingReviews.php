<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Events\Booking_Notification_Event;
use App\Events\Booking_Reviews_Event;

use Carbon\Carbon;
class BookingReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:review';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send review notification';
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
        $bookings                   = $this->booking->getAllBookingCheckoutOneDay();
        $current_day                = Carbon::now();
        foreach ($bookings as $key => $booking) {
            $checkin                = $booking->checkin;
            $checkout               = $booking->checkout;
            $checkin_timestamp      = Carbon::parse($checkin);
            $checkout_timestamp     = Carbon::parse($checkout);
            // dd($checkout_timestamp->diffInHours($current_day));
            if ($checkout_timestamp->diffInHours($current_day) <= 36 && $booking->status == 4 && $booking->reviews == 0)
            {   
                if($booking->booking_type == 2)
                {
                    $dataTime['read_timeCheckin']  = $checkin_timestamp->isoFormat('LL');
                    $dataTime['read_timeCheckout'] = $checkout_timestamp->isoFormat('LL');
                    $dataTime['count_bookingTime'] = $checkin_timestamp->diffInDays($checkout_timestamp)+1;
                    $booking->reviews = 1;
                    $booking->save();
                    event(new Booking_Reviews_Event($booking,$dataTime));
                }
                if($booking->booking_type == 1)
                {
                    $dataTime['read_timeBooking']  = $checkin_timestamp->isoFormat('LL');
                    $dataTime['count_bookingTime'] = $checkin_timestamp->diffInHours($checkout_timestamp);
                    $booking->reviews = 1;
                    $booking->save();
                    event(new Booking_Reviews_Event($booking,$dataTime));
                }
            }
        }
    }
}
