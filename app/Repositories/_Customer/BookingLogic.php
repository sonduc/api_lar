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
use App\Repositories\Rooms\RoomOptionalPriceRepository;
use App\Repositories\Rooms\RoomOptionalPriceRepositoryInterface;
use App\Repositories\Rooms\RoomRepository;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Rooms\RoomTimeBlockRepository;
use App\Repositories\Rooms\RoomTimeBlockRepositoryInterface;
use App\Repositories\Users\UserRepositoryInterface;
use App\Repositories\_Customer\CouponLogic;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Events\Customer_Register_TypeBooking_Event;
use Illuminate\Support\Facades\Hash;

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
     * @param CouponLogic                                                      $cp
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
        CouponLogic $cp
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
        $data['customer_id'] = Auth::check()? Auth::user()->id : $this->checkUserExist($data);
        $data['merchant_id'] = $room->merchant_id;
        $data['settings']    = $room->settings;
        $data_booking        = parent::store($data);
        $this->status->storeBookingStatus($data_booking, $data);
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
            // Cập nhâp token  cho user vừa tạo
            $data['token']    = Hash::make(str_random(60));
            $data['type_create'] = User::BOOKING;
            $user             = $this->user->store($data);
            event(new Customer_Register_TypeBooking_Event($user));
            return $user->id;
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
    public function getBooking($id, $pageSize)
    {
        $booking = $this->booking->getBookingById($id, $pageSize);
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


    /**
     *  Kiểm tra xem có đủ điều kiện để  chỉnh sửa thông tin booking không
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $booking
     * @param $request
     * @throws \Exception
     */
    public function checkValidBookingCancel($id)
    {
        if (!Auth::check()) {
            throw new \Exception('Vui lòng đăng nhập để thực hiện chức năng này');
        }

        $booking=$this->model->getById($id);
        if (Auth::user()->id != $booking->customer_id) {
            throw new \Exception('Bạn phaỉ là người đặt phòng này mới có quyền hủy');
        }
    }

}
