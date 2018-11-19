<?php

namespace App\Repositories\Bookings;

use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;
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
     * Check xem người này đã hoàn thành booking và checkout chưa
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     *
     * @return mixed
     * @throws \Exception
     */

    public function getBookingByCheckout($id)
    {
        $dateNow = Carbon::now();
        $data    = $this->model->where([
            ['id', $id],
            ['checkout', '<', $dateNow->timestamp],
            ['status', '=', BookingConstant::BOOKING_COMPLETE],
        ])->first();
        if (empty($data)) throw new \Exception('Bạn chưa hoàn thành booking này nên chưa có quyền đánh giá');
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
        try {
            list ($y_start, $m_start, $d_start) = explode('-', $start);
            list ($y_end, $m_end, $d_end) = explode('-', $end);

            $checkIn  = Carbon::createSafe((int)$y_start, (int)$m_start, (int)$d_start)->endOfDay()->timestamp;
            $checkOut = Carbon::createSafe((int)$y_end, (int)$m_end, (int)$d_end)->endOfDay()->timestamp;

        } catch (InvalidDateException | \ErrorException $exception) {
            $checkIn  = Carbon::now()->addDay()->endOfDay()->timestamp;
            $checkOut = Carbon::now()->addDay()->addMonth()->endOfDay()->timestamp;
        }

        $data = $this->model
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where([
                    ['bookings.checkin', '<', $checkIn],
                    ['bookings.checkout', '>', $checkIn],
                ])->orWhere([
                    ['bookings.checkin', '<', $checkOut],
                    ['bookings.checkout', '>', $checkOut],
                ])->orWhere([
                    ['bookings.checkin', '>', $checkIn],
                    ['bookings.checkout', '<', $checkOut],
                ]);
            })
            ->whereNotIn(
                'bookings.status', [BookingConstant::BOOKING_CANCEL, BookingConstant::BOOKING_COMPLETE]
            )->get();

        // dd($data);

        return $data;
    }

    /**
     * Lấy tất cả các booking theo id người dùng có phân trang
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param $size
     *
     * @return mixed
     */

    public function getBookingById($id, $size)
    {
        return $this->model
            ->where('bookings.id', $id)
            ->paginate($size);
    }

    public function updatStatusBooking($booking)
    {
        $data    = $booking->data;
        $uuid    = $booking->data['uuid'];
        $booking = $this->getBookingByUuid($uuid);
        parent::update($booking->id, $data);
    }

    public function getBookingByUuid($uuid)
    {
        return $this->model->where('uuid', $uuid)->first();
    }


}

