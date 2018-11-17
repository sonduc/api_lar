<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 17/11/2018
 * Time: 09:48
 */

namespace App\Listeners\Logic;

use App\Events\ConfirmBookingTime;
use App\Repositories\Bookings\BookingRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
class ConfirmBookingTimeListenter implements ShouldQueue
{
    protected $booking;
    /**
     * Create the event listener.
     *
     * @return void
     */

    public function __construct(BookingRepository $booking)
    {
        $this->booking = $booking;
    }


    public function handle(ConfirmBookingTime $event)
    {
        $this->booking->updatStatusBooking($event);
    }

}
