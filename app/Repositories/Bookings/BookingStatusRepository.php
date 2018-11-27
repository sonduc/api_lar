<?php

namespace App\Repositories\Bookings;

use App\Repositories\BaseRepository;

class BookingStatusRepository extends BaseRepository implements BookingStatusRepositoryInterface
{
    /**
     * @var BookingStatus
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

    /**
     * Lưu trạng thái booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $booking
     * @param array $data
     */
    public function storeBookingStatus($booking = [], $data = [])
    {
        $data['booking_id'] = $booking['id'];
        $data['note']       = array_key_exists('staff_note', $data) ? $data['staff_note'] : null;
        parent::store($data);
    }

    /**
     * Cập nhật trạng thái booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $booking
     * @param array $data
     */
    public function updateBookingStatus($booking = [], $data = [])
    {
        $data['booking_id'] = $booking->id;
        $data['note']       = array_key_exists('staff_note', $data) ? $data['staff_note'] : null;
        $bookingStatus      = $this->getBookingStatusByBookingID($booking);

        if ($bookingStatus instanceof BookingStatus) {
            parent::update($bookingStatus->id, $data);
        } else {
            $this->storeBookingStatus($booking->toArray(), $data);
        }
    }

    /**
     * Lấy booking theo id
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $booking
     *
     * @return mixed
     */
    public function getBookingStatusByBookingID($booking = [])
    {
        return $this->model->where('booking_id', $booking->id)->first();
    }
}
