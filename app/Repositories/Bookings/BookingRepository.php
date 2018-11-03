<?php

namespace App\Repositories\Bookings;

use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class BookingRepository extends BaseRepository implements BookingRepositoryInterface
{
    /**
     * BookingRepository constructor.
     *
     * @param Booking $booking
     */
    public function __construct(Booking $booking)
    {
        $this->model = $booking;
    }

    /**
     * Lấy các booking chưa bị hủy và có thời gian checkout lớn hơn hiện tại
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     *
     * @return mixed
     */
    public function getFutureBookingByRoomId($id)
    {
        $dateNow = Carbon::now();
        $data    = $this->model->where([
            ['status', '<>', BookingConstant::BOOKING_CANCEL],
            ['checkout', '>', $dateNow->timestamp],
            ['created_at', '>', $dateNow->copy()->addYears(-1)],
            ['room_id', $id],
        ])->get();
        return $data;
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param string $start
     * @param string $end
     *
     * @return Collection
     */
    public function getAllBookingInPeriod($start, $end)
    {
        list ($y_start, $m_start, $d_start) = explode('-', $start);
        list ($y_end, $m_end, $d_end) = explode('-', $end);

        $checkIn  = Carbon::createSafe((int)$y_start, (int)$m_start, (int)$d_start)->endOfDay()->timestamp;
        $checkOut = Carbon::createSafe((int)$y_end, (int)$m_end, (int)$d_end)->endOfDay()->timestamp;

        $data = $this->model->orWhere([
            ['bookings.checkin', '<', $checkIn],
            ['bookings.checkout', '>', $checkIn],
        ])->orWhere([
            ['bookings.checkin', '<', $checkOut],
            ['bookings.checkout', '>', $checkOut],
        ])->orWhere([
            ['bookings.checkin', '>', $checkIn],
            ['bookings.checkout', '<', $checkOut],
        ])->whereNotIn(
            'bookings.status', [BookingConstant::BOOKING_CANCEL, BookingConstant::BOOKING_COMPLETE]
        )->get();

        return $data;
    }

}

