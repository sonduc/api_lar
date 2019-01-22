<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/22/2019
 * Time: 10:11 AM
 */

namespace App\Repositories\_Merchant;


use App\Repositories\BaseLogic;
use App\Repositories\Bookings\BookingCancelRepositoryInterface;
use App\Repositories\Bookings\BookingLogicTrait;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Repositories\Bookings\BookingStatusRepositoryInterface;
use App\Repositories\Coupons\CouponLogicTrait;
use App\Repositories\Coupons\CouponRepositoryInterface;
use App\Repositories\Payments\PaymentHistoryRepositoryInterface;
use App\Repositories\Roomcalendars\RoomCalendarRepositoryInterface;
use App\Repositories\Rooms\RoomLogicTrait;
use App\Repositories\Rooms\RoomOptionalPriceRepositoryInterface;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Rooms\RoomTimeBlockRepositoryInterface;
use App\Repositories\Users\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class BookingLogic extends BaseLogic
{
    use RoomLogicTrait, BookingLogicTrait, CouponLogicTrait;
    protected $status;
    protected $payment;
    protected $room_calendar;

    /**
     * BookingLogic constructor.
     *
     * @param BookingRepositoryInterface|BookingRepository                     $booking
     * @param BookingStatusRepositoryInterface|BookingStatusRepository         $status
     * @param PaymentHistoryRepositoryInterface|PaymentHistoryRepository       $payment
     * @param UserRepositoryInterface|UserRepository                           $user
     * @param RoomRepositoryInterface|RoomRepository                           $room
     * @param RoomOptionalPriceRepositoryInterface|RoomOptionalPriceRepository $op
     * @param RoomTimeBlockRepositoryInterface|RoomTimeBlockRepository         $roomTimeBlock
     * @param BookingCancelRepositoryInterface|BookingCancelRepository         $booking_cancel
     * @param CouponRepositoryInterface|CouponRepository                       $cp
     * @param RoomCalendarRepositoryInterface|RoomCalendarRepository           $room_calendar
     */
    public function __construct(
        BookingRepositoryInterface $booking,
        BookingStatusRepositoryInterface $status,
        PaymentHistoryRepositoryInterface $payment,
        UserRepositoryInterface $user,
        RoomRepositoryInterface $room,
        RoomOptionalPriceRepositoryInterface $op,
        RoomTimeBlockRepositoryInterface $roomTimeBlock,
        BookingCancelRepositoryInterface $booking_cancel,
        CouponRepositoryInterface $cp,
        RoomCalendarRepositoryInterface $room_calendar
    ) {
        $this->model          = $booking;
        $this->booking        = $booking;
        $this->status         = $status;
        $this->payment        = $payment;
        $this->user           = $user;
        $this->room           = $room;
        $this->op             = $op;
        $this->roomTimeBlock  = $roomTimeBlock;
        $this->booking_cancel = $booking_cancel;
        $this->cp             = $cp;
        $this->room_calendar  = $room_calendar;
    }


    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param     $id
     * @param int $pageSize
     *
     * @return mixed
     */
    public function getBooking($id, $params, $pageSize)
    {
        $booking = $this->booking->getBookingByMerchantId($id, $params, $pageSize);
        return $booking;
    }

    /**
     * Kiểm tra xem booking này có thuộc hủ host không
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     */
    public function checkOwnerBooking($id)
    {
        $booking = $this->booking->getBookingById($id);
        if ($booking->merchant_id != Auth::user()->id)
        {
            throw new \Exception('Bạn không có quyền vào mục này');
        }

    }

    /**
     * Chủ host update trạng thái booking
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param $data
     * @return \App\Repositories\Eloquent
     */
    public function updateBookingStatus($id,$data)
    {
        $data_booking = parent::update($id, $data);
        return $data_booking;
    }

}