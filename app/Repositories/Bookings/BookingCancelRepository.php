<?php

namespace App\Repositories\Bookings;

use App\Repositories\BaseRepository;

class BookingCancelRepository extends BaseRepository implements BookingcancelRepositoryInterface
{
    protected $model;

    /**
     * BookingCancelRepository constructor.
     *
     * @param BookingCancel $booking
     */
    public function __construct(BookingCancel $booking)
    {
        $this->model = $booking;
    }
}
