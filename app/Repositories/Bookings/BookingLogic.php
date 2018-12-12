<?php

namespace App\Repositories\Bookings;

use App\Repositories\BaseLogic;
use App\Repositories\Coupons\CouponLogicTrait;
use App\Repositories\Coupons\CouponRepository;
use App\Repositories\Coupons\CouponRepositoryInterface;
use App\Repositories\Payments\PaymentHistoryRepository;
use App\Repositories\Payments\PaymentHistoryRepositoryInterface;
use App\Repositories\Rooms\RoomLogicTrait;
use App\Repositories\Rooms\RoomOptionalPriceRepository;
use App\Repositories\Rooms\RoomOptionalPriceRepositoryInterface;
use App\Repositories\Rooms\RoomRepository;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Rooms\RoomTimeBlockRepository;
use App\Repositories\Rooms\RoomTimeBlockRepositoryInterface;
use App\Repositories\Roomcalendars\RoomCalendarRepositoryInterface;
use App\Repositories\Users\UserRepository;
use App\Repositories\Users\UserRepositoryInterface;
use App\User;
use Carbon\Carbon;

class BookingLogic extends BaseLogic
{
    use RoomLogicTrait, BookingLogicTrait, CouponLogicTrait;
    protected $status;
    protected $payment;
    protected $user;
    protected $room;
    protected $op;
    protected $booking;
    protected $roomTimeBlock;
    protected $booking_cancel;
    protected $booking_refund;
    protected $cp;

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
     * @param BookingRefundRepositoryInterface|BookingRefundRepository         $booking_refund
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
     * Thêm booking mới
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function store($data = [])
    {
        $room = $this->room->getById($data['room_id']);
        $data = $this->priceCalculator($room, $data);
        // dd($data);
        $data = $this->dateToTimestamp($data);
        $data = $this->addPriceRange($data);

        $data['customer_id'] =
            array_key_exists('customer_id', $data) ? $data['customer_id'] : $this->checkUserExist($data);
        $data['merchant_id'] = $room->merchant_id;

        $data['settings']    = $room->settings;
        // return json_encode($refund);

        $data_booking = parent::store($data);
        $this->status->storeBookingStatus($data_booking, $data);
        $this->payment->storePaymentHistory($data_booking, $data);
        $this->room_calendar->storeRoomCalendar($data_booking, $data);
        return $data_booking;
    }

    /**
     * Kiểm tra xem có user tồn tại
     * Nếu không tồn tại thì tự động thêm user mới
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $data
     *
     * @return mixed
     */
    private function checkUserExist($data = [])
    {
        $user = $this->user->getUserByEmailOrPhone($data);

        if (!$user) {
            $data['password'] = $data['phone'];
            $data['type']     = User::USER;
            $data['owner']    = User::NOT_OWNER;
            $data['status']   = User::DISABLE;
            $user             = $this->user->store($data);
        }

        return $user->id;
    }

    /**
     * Cập nhật một số trường trạng thái
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     * @param $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function minorUpdate($id, $data)
    {
        $data_booking = parent::update($id, $data);
        return $data_booking;
    }
}
