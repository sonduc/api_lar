<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 23/11/2018
 * Time: 01:19
 */

namespace App\Repositories\Bookings;


use App\Repositories\BaseRepository;


class BookingRefundRepository extends BaseRepository implements BookingRefundRepositoryInterface
{
    protected $model;

    /**
     * BookingCancelRepository constructor.
     *
     * @param BookingCancel $booking
     */
    public function __construct(BookingRefund $booking)
    {
        $this->model = $booking;
    }

    /**
     * Tao booking_refund theo theo room_setting tại thời điểm booking
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param array $booking
     * @param array $data
     */

    public function storeBookingRefund($booking = [], $room = [], $booking_refund = [])
    {
        $room_array = json_decode( $room->settings);


        if (!empty($room_array->no_booking_cancel) && $room_array->no_booking_cancel == BookingConstant::BOOKING_CANCEL_UNAVAILABLE)
        {
            $data['no_booking_cancel'] = BookingConstant::BOOKING_CANCEL_UNAVAILABLE;
            $data['booking_id']        = $booking->id;
            return parent::store($data);
        }

        //  Lưu thông tin chính sách hủy khi chủ host có chính sách hủy phòng (no_booking_cancel == 0)
        foreach ($room_array->refund as $value)
        {
           $list['booking_id']              = $booking->id;
           $list['days']                    = $value->days;
           $list['refund']                  = $value->amount;
           $list['no_booking_cancel']       = BookingConstant::BOOKING_CANCEL_AVAILABLE;
           $booking_refund[]                = $list;
        }
        parent::storeArray($booking_refund);

    }

    /**
     * Lấy dữ diệu booking_refund theo booking_id
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $booking_id
     * @return mixed
     */
    public function getBookingRefundByBookingId($booking_id)
    {
        return $this->model->where('booking_id',$booking_id)->orderBy('days')->get()->toArray();
    }


    /**
     * lấy ra mức hoàn tiền khi hủy booking.
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $booking_id
     * @param $day
     * @return mixed
     */
    public function getRefund($booking_id, $day)
    {
        return $this->model->where([
            ['booking_id',$booking_id],
            ['days',$day]
        ])->first();
    }


}
