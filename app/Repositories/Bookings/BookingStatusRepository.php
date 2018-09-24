<?php

namespace App\Repositories\Bookings;

use App\Repositories\BaseRepository;

class BookingStatusRepository extends BaseRepository
{
    /**
     * BookingStatus model.
     * @var Model
     */
    protected $model;

    /**
     * BookingStatusRepository constructor.
     *
     * @param BookingStatus $bookingstatus
     */
    public function __construct(BookingStatus $bookingstatus)
    {
        $this->model = $bookingstatus;
    }

    public function storeBookingStatus($booking = [], $data = [])
    {
        $data['booking_id'] = $booking['id'];
        $data['note']       = $data['staff_note'];
        parent::store($data);
    }

    public function updateBookingStatus($booking = [], $data = [])
    {
        $data['booking_id'] = $booking->id;
        $data['note']       = $data['staff_note'];
        $bookingStatus      = $this->getBookingStatusByBookingID($booking);
        parent::update($bookingStatus->id, $data);
    }

    public function getBookingStatusByBookingID($booking = [])
    {
        return $this->model->where('booking_id', $booking->id)->first();
    }

}
