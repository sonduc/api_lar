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

        foreach ($room_array as $value)
        {
           $list['booking_id'] = $booking->id;
           $list['days'] = $value->days;
           $list['refund'] = $value->amount;
           $booking_refund[] = $list;
        }
        parent::storeArray($booking_refund);

    }

    public function getBookingRefundByBookingId($booking_id)
    {
        return $this->model->where('booking_id',$booking_id)->orderBy('days')->get()->toArray();
    }

}
