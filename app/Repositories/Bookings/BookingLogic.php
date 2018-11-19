<?php

namespace App\Repositories\Bookings;

use App\Repositories\BaseLogic;
use App\Repositories\Payments\PaymentHistoryRepository;
use App\Repositories\Payments\PaymentHistoryRepositoryInterface;
use App\Repositories\Rooms\RoomLogicTrait;
use App\Repositories\Rooms\RoomOptionalPrice;
use App\Repositories\Rooms\RoomOptionalPriceRepository;
use App\Repositories\Rooms\RoomOptionalPriceRepositoryInterface;
use App\Repositories\Rooms\RoomRepository;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Rooms\RoomTimeBlockRepository;
use App\Repositories\Rooms\RoomTimeBlockRepositoryInterface;
use App\Repositories\Users\UserRepository;
use App\Repositories\Users\UserRepositoryInterface;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\Exceptions\InvalidDateException;

class BookingLogic extends BaseLogic
{
    use RoomLogicTrait,BookingLogicTrait;
    protected $status;
    protected $payment;
    protected $user;
    protected $room;
    protected $op;
    protected $booking;
    protected $roomTimeBlock;
    protected $booking_cancel;

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
     */
    public function __construct(
        BookingRepositoryInterface $booking,
        BookingStatusRepositoryInterface $status,
        PaymentHistoryRepositoryInterface $payment,
        UserRepositoryInterface $user,
        RoomRepositoryInterface $room,
        RoomOptionalPriceRepositoryInterface $op,
        RoomTimeBlockRepositoryInterface $roomTimeBlock,
        BookingCancelRepositoryInterface $booking_cancel
    )
    {
        $this->model          = $booking;
        $this->booking        = $booking;
        $this->status         = $status;
        $this->payment        = $payment;
        $this->user           = $user;
        $this->room           = $room;
        $this->op             = $op;
        $this->roomTimeBlock  = $roomTimeBlock;
        $this->booking_cancel = $booking_cancel;
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
        $data = $this->dateToTimestamp($data);
        $data = $this->addPriceRange($data);

        $data['customer_id'] =
            array_key_exists('customer_id', $data) ? $data['customer_id'] : $this->checkUserExist($data);
        $data['merchant_id'] = $room->merchant_id;

        $data_booking = parent::store($data);
        $this->status->storeBookingStatus($data_booking, $data);
        $this->payment->storePaymentHistory($data_booking, $data);
        return $data_booking;
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

    /**
     * Cập nhật tiền cho booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     * @param $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function updateBookingMoney($id, $data)
    {
        $booking          = parent::getById($id);
        $data['checkin']  = Carbon::createFromTimestamp($booking->checkin)->toDateTimeString();
        $data['checkout'] = Carbon::createFromTimestamp($booking->checkout)->toDateTimeString();
        $data             = array_merge($booking->toArray(), $data);

        return $this->update($id, $data);
    }

    /**
     * Cập nhật booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param int   $id
     * @param       $data
     * @param array $excepts
     * @param array $only
     *
     * @return \App\Repositories\Eloquent
     */
    public function update($id, $data, $excepts = [], $only = [])
    {
        $room                = $this->room->getById($data['room_id']);
        $data['merchant_id'] = $room->merchant_id;

        $data = $this->priceCalculator($room, $data);
        $data = $this->dateToTimestamp($data);
        $data = $this->addPriceRange($data);

        $data_booking = parent::update($id, $data);

        $this->status->updateBookingStatus($data_booking, $data);
        $this->payment->storePaymentHistory($data_booking, $data);

        return $data_booking;
    }

}
