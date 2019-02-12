<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Bookings\BookingConstant;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Events\Booking_Notification_Event;
use App\Events\Booking_Reviews_Event;

use Carbon\Carbon;

class UpdateBookingStatus extends Command
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
    protected $description = 'Update booking status';
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
        $dateNow_timestamp     = Carbon::now()->timestamp;
        $bookings              = $this->booking->getAllBookingPast($dateNow_timestamp);
        foreach ($bookings as $key => $booking) {
            $checkin                = $booking->checkin;
            $checkout               = $booking->checkout;
            
            if ($checkout < $dateNow_timestamp && $booking->status == BookingConstant::BOOKING_USING) {
                $booking->status = BookingConstant::BOOKING_COMPLETE;
                // $booking->review_url = 
                $booking->save();
            }
            if ($checkin < $dateNow_timestamp && $booking->status == BookingConstant::BOOKING_CONFIRM) {
                $booking->status = BookingConstant::BOOKING_USING;
                $booking->save();
            }
        }
    }
}
