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
     * Hủy booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     * @param $data
     *
     * @return \App\Repositories\Eloquent
     * @throws \Exception
     */
    public function cancelBooking($id, $data)
    {
        $data_booking = parent::getById($id);

        if ($data_booking->status == BookingConstant::BOOKING_CANCEL) {
            throw new \Exception(trans2(BookingMessage::ERR_BOOKING_CANCEL_ALREADY));
        }

        $booking_update = [
            'status' => BookingConstant::BOOKING_CANCEL,
        ];
        parent::update($id, $booking_update);

        $data['booking_id'] = $id;
        return $this->booking_cancel->store($data);
    }


}
