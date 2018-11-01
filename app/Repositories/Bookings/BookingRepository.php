<?php

namespace App\Repositories\Bookings;

use App\Repositories\BaseRepository;
use Carbon\Carbon;

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
            ['room_id', $id]
        ])->get();
        return $data;
    }

    /**
     * Check xem người này đã hoàn thành booking và checkout chưa
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @return mixed
     * @throws \Exception
     */

    public function getBookingByCheckout($id)
    {
        $dateNow = Carbon::now();
        $data    = $this->model->where([
            ['id',$id],
            ['checkout', '<', $dateNow->timestamp],
            ['status','=',BookingConstant::BOOKING_COMPLETE],
        ])->first();
        if (empty($data)) throw new \Exception('Bạn chưa hoàn thành booking này nên chưa có quyền đánh giá');
        return $data;
    }




}

