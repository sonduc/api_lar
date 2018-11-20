<?php

namespace App\Repositories\_Customer;

use App\Repositories\BaseLogic;
use App\Repositories\Bookings\BookingCancelRepository;
use App\Repositories\Bookings\BookingCancelRepositoryInterface;
use App\Repositories\Bookings\BookingConstant;
use App\Repositories\Bookings\BookingLogicTrait;
use App\Repositories\Bookings\BookingMessage;
use App\Repositories\Bookings\BookingRepository;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Repositories\Bookings\BookingStatusRepository;
use App\Repositories\Bookings\BookingStatusRepositoryInterface;
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
use Illuminate\Support\Facades\Auth;

class BookingLogic extends BaseLogic
{
    use RoomLogicTrait, BookingLogicTrait;
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
        $data_booking        = parent::store($data);
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
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param     $id
     * @param int $pageSize
     *
     * @return mixed
     */
    public function getBooking($id,$pageSize)
    {
        $booking = $this->booking->getBookingById($id,$pageSize);
        return $booking;
    }


    /**
     * cập nhâp trạng thái đơn xác nhận và đơn hủy
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function updateStatusBooking($data)
    {
        $uuid    = $data['uuid'];
        $booking = $this->model->getBookingByUuid($uuid);
        $booking = parent::update($booking->id, $data);
        return $booking;
    }


    public function checkBookingStatus($uuid)
    {
        return $this->model->getBookingByUuid($uuid)->status;
    }


    /**
     * Kiểm tra thời gian xác nhận booking
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $code
     *
     * @return int
     */
    public function checkTimeConfirm($code)
    {
        $timeNow    = Carbon::now();
        $timeSubmit = base64_decode($code);
        $timeSubmit = Carbon::createFromTimestamp($timeSubmit)->toDateTimeString();
        return $timeNow->diffInMinutes($timeSubmit);
    }

    public function checkValidBookingUpdate($booking,$request)
    {
       if (array_key_exists($booking->status,[BookingConstant::BOOKING_COMPLETE,BookingConstant::BOOKING_CANCEL]))
       {
           throw new \Exception('Bạn không thể cập nhập thông tin cho booking này do đã hoàn thành hoặc đã bị hủy');
       }


    }


}
