<?php

namespace App\Repositories\Bookings;

use App\Repositories\BaseRepository;
use App\Repositories\Payments\PaymentHistoryRepository;
use App\Repositories\Rooms\RoomOptionalPrice;
use App\Repositories\Rooms\RoomOptionalPriceRepository;
use App\Repositories\Rooms\RoomRepository;
use App\Repositories\Users\UserRepository;
use App\User;
use Carbon\CarbonPeriod;
use Carbon\Exceptions\InvalidDateException;
use Illuminate\Support\Carbon;

class BookingRepository extends BaseRepository
{

    protected $model;
    protected $status;
    protected $payment;
    protected $user;
    protected $room;
    protected $op;

    /**
     * BookingRepository constructor.
     *
     * @param Booking                     $booking
     * @param BookingStatusRepository     $status
     * @param PaymentHistoryRepository    $payment
     * @param UserRepository              $user
     * @param RoomRepository              $room
     * @param RoomOptionalPriceRepository $op
     */
    public function __construct(
        Booking $booking, BookingStatusRepository $status, PaymentHistoryRepository $payment, UserRepository $user,
        RoomRepository $room, RoomOptionalPriceRepository $op
    )
    {
        $this->model   = $booking;
        $this->status  = $status;
        $this->payment = $payment;
        $this->user    = $user;
        $this->room    = $room;
        $this->op      = $op;
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
        $data['customer_id'] =
            array_key_exists('customer_id', $data) ? $data['customer_id'] : $this->checkUserExist($data);

        $room                = $this->getRoomById($data);
        $data['merchant_id'] = $room->merchant_id;

        $data = $this->priceCalculator($room, $data);
        $data = $this->dateToTimestamp($data);
        $data = $this->addPriceRange($data);

        $data_booking = parent::store($data);
        $this->status->storeBookingStatus($data_booking, $data);
        $this->payment->storePaymentHistory($data_booking, $data);
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

    public function getRoomById($data = [])
    {
        return $this->room->getById($data['room_id']);
    }

    /**
     * Tính toán giá tiền cho booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $data
     * @param       $room
     *
     * @return array
     */
    public function priceCalculator($room, $data = [])
    {
        $checkin              = Carbon::parse($data['checkin']);
        $checkout             = Carbon::parse($data['checkout']);
        $room_optional_prices = $this->op->getOptionalPriceByRoomId($room->id);

        // Tính tiền dựa theo kiểu booking
        if ($data['booking_type'] == BookingConstant::BOOKING_TYPE_HOUR) {
            $hours         = $checkout->diffInHours($checkin) + 1;
            $data['hours'] = $hours;
            if ($hours > 24) throw new InvalidDateException('validate-hour', 'Khoảng thời gian vượt quá 24h');

            // Xử lý logic tính giá phòng vào ngày đặc biệt
            $money = $this->optionalPriceCalculator($room_optional_prices, $data, BookingConstant::BOOKING_TYPE_HOUR) ?? 0;

            if ($money == 0) $money = $room->price_hour + ($hours - BookingConstant::TIME_BLOCK) * $room->price_after_hour;

        } else {
            $days         = $checkout->diffInDays($checkin) + 1;
            $data['days'] = $days;

            // Xử lý logic tính giá phòng vào ngày đặc biệt
            list ($money, $totalDay) = $this->optionalPriceCalculator($room_optional_prices, $data, BookingConstant::BOOKING_TYPE_DAY);
            $money += $room->price_day * ($days - $totalDay);

        }

        // Tính tiền dựa theo số khách
        if (($additional_guest = $data['number_of_guests'] - $room->max_guest) > 0) {
            $money += $additional_guest * $room->price_charge_guest;
        }

        $data['price_original']  = $money;
        $data['service_fee']     = $room->cleaning_fee;
        $data['coupon_discount'] = 0; // TODO Làm thêm phần coupon

        $price = $money
            + (array_key_exists('service_fee', $data) ? $data['service_fee'] : 0)
            + (array_key_exists('additional_fee', $data) ? $data['additional_fee'] : 0)
            - (array_key_exists('coupon_discount', $data) ? $data['coupon_discount'] : 0)
            - (array_key_exists('price_discount', $data) ? $data['price_discount'] : 0);

        $data['total_fee'] = $price;

        return $data;
    }

    /**
     * Tính giá tiền đặc biệt cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $rop
     * @param array $data
     * @param int   $type
     *
     * @return array|float|int
     */
    public function optionalPriceCalculator($rop, $data = [], $type = BookingConstant::BOOKING_TYPE_DAY)
    {
        $checkin            = Carbon::parse($data['checkin']);
        $checkout           = Carbon::parse($data['checkout']);
        $listDays           = $specialDays = $weekDays = $optionalDays = $optionalWeekDays = [];
        $money              = 0;
        $totalDayOfDiscount = 0;
        // Lấy các ngày đặc biệt được giảm giá
        foreach ($rop as $op) {
            if ($op->day !== null && $op->status == RoomOptionalPrice::AVAILABLE) {
                $specialDays[]  = $op->day;
                $optionalDays[] = $op;
            }

            if ($op->weekday !== null && $op->status == RoomOptionalPrice::AVAILABLE) {
                $weekDays[]         = $op->weekday;
                $optionalWeekDays[] = $op;
            }
        }
        // Logic tính tiền cho kiểu ngày
        if ($type == BookingConstant::BOOKING_TYPE_DAY) {

            // Lấy tất cả các ngày trong khoảng thời gian checkin và checkout
            $period = CarbonPeriod::between($checkin, $checkout->addDay());
            foreach ($period as $day) {
                if (in_array($day->dayOfWeek + 1, $weekDays)) $listDays[] = $day->format('Y-m-d');
            }

            // Lọc tất cả các ngày trong khoảng thời gian checkin và checkout mà không có ngày đặc biệt cụ thể
            $otherDays = array_diff($listDays, $specialDays);

            // Tính tiền cho các ngày đặc biệt cụ thể
            foreach ($optionalDays as $op) {
                if ($op->day !== null) {
                    $day = Carbon::parse($op->day);
                    if ($day->between($checkin, $checkout)) {
                        $money += $op->price_day;
                        $totalDayOfDiscount++;
                    }
                }
            }

            // Tính tiền cho các ngày giảm giá trong tuần;
            foreach ($optionalWeekDays as $op) {
                foreach ($otherDays as $day) {
                    $day = Carbon::parse($day);
                    if ($op->weekday == ($day->dayOfWeek + 1)) {
                        $money += $op->price_day;
                        $totalDayOfDiscount++;
                    }
                }
            }

            return [$money, $totalDayOfDiscount];
        } else {
            // Logic tính tiền kiểu giờ
            if (in_array($checkin->format('Y-m-d'), $specialDays)) {
                foreach ($optionalDays as $op) {
                    if ($op->day == $checkin->format('Y-m-d')) {
                        $money += $op->price_hour + ($checkout->diffInHours($checkin) + 1 - BookingConstant::TIME_BLOCK) * $op->price_after_hour;
                    }
                }
            } else if (in_array($checkin->dayOfWeek + 1, $weekDays)) {
                foreach ($optionalWeekDays as $op) {
                    if ($op->weekday == $checkin->dayOfWeek + 1) {
                        $money += $op->price_hour + ($checkout->diffInHours($checkin) + 1 - BookingConstant::TIME_BLOCK) * $op->price_after_hour;
                    }
                }
            }

            return $money;
        }
    }

    /**
     * Chuyển ngày giờ thành UNIX timestamp
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $data
     *
     * @return array
     */
    public function dateToTimestamp($data = [])
    {
        $data['checkin']  = strtotime($data['checkin']);
        $data['checkout'] = strtotime($data['checkout']);

        return $data;
    }

    /**
     * Thêm khoảng giá
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $data
     *
     * @return array
     */
    public function addPriceRange($data = [])
    {
        $list_range  = BookingConstant::PRICE_RANGE_LIST;
        $money       = $data['price_original'];
        $price_range = array_keys(BookingConstant::PRICE_RANGE)[count(BookingConstant::PRICE_RANGE) - 1];

        foreach ($list_range as $key => $item) {
            if ($money < $item * 1000) {
                $price_range = $key;
                break;
            }
        }

        $data['price_range'] = $price_range;
        return $data;
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

