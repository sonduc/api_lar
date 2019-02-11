<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Bookings\BookingConstant;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Events\Booking_Notification_Event;
use App\Events\Booking_Reviews_Event;

use Carbon\Carbon;

class BookingReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Booking Reminder';
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
        $bookings              = $this->booking->getAllBookingFuture();
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
        }
    }
}
