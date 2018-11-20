<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Events\Booking_Notification_Event;

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
        $bookings                   = $this->booking->getAllBookingFuture();
        $current_day                = Carbon::now()->timestamp;
        $tomorrow                   = $current_day + 1;
        $the_day_after_tomorow      = $current_day + 86400;

        foreach ($bookings as $key => $booking) {
            $checkin                = $booking->checkin;
            $checkout               = $booking->checkout;
            $checkin_timestamp      = Carbon::parse($checkin)->timestamp;
            $checkout_timestamp     = Carbon::parse($checkout)->timestamp;
            if ($booking->email_reminder == 0){
                if ($current_day - $checkin_timestamp <= 172800){
                    event(new Booking_Notification_Event($booking));
                    $booking->email_reminder = 1;
                    $booking->save();
                }
            }
            if ($checkout_timestamp >= $current_day && $booking->status == 3){
                $booking->status = 4;
                $booking->save();
            }
            if ($checkin_timestamp >= $current_day && ($booking->status == 2 || $booking->status == 1)) {
                $booking->status = 3;
                $booking->save();
            }
        }
    }
}
