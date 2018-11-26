<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 19/11/2018
 * Time: 11:08
 */

namespace App\Repositories\Bookings;

use App\Repositories\Rooms\RoomOptionalPrice;
use App\User;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;
use Carbon\CarbonPeriod;
use function Couchbase\defaultDecoder;

trait BookingLogicTrait
{
    protected $cp;
    protected $op;
    protected $room;
    protected $user;
    protected $booking_cancel;

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
        $this->checkValidBookingTime($room, $data);
        $checkin              = Carbon::parse($data['checkin']);
        $checkout             = Carbon::parse($data['checkout']);
        $room_optional_prices = $this->op->getOptionalPriceByRoomId($room->id);
        $coupon               = $this->cp->getCouponByCode($data['coupon']);
        $coupon_discount      = $this->cp->checkSettingDiscount($coupon, $data);
        // Tính tiền dựa theo kiểu booking
        if ($data['booking_type'] == BookingConstant::BOOKING_TYPE_HOUR) {
            $hours         = $checkout->copy()->ceilHours()->diffInHours($checkin);
            $data['hours'] = $hours;

            // Xử lý logic tính giá phòng vào ngày đặc biệt
            $money =
                $this->optionalPriceCalculator($room_optional_prices, $room, $data, BookingConstant::BOOKING_TYPE_HOUR)
                ?? 0;

            if ($money == 0) {
                $money =
                $room->price_hour + ($hours - BookingConstant::TIME_BLOCK) * $room->price_after_hour;
            }
        } else {
            $CI = $checkin->copy()->setTimeFromTimeString($room->checkin);
            $CO = $checkout->copy()->setTimeFromTimeString($room->checkout);

            $days             = $CO->diffInDays($CI) + 1;
            $data['days']     = $days;
            $data['checkin']  = $CI->timestamp;
            $data['checkout'] = $CO->timestamp;

            // Xử lý logic tính giá phòng vào ngày đặc biệt
            list($money, $totalDay) =
                $this->optionalPriceCalculator($room_optional_prices, $room, $data, BookingConstant::BOOKING_TYPE_DAY);
            $money += $room->price_day * ($days - $totalDay);
        }

        // Tính tiền dựa theo số khách
        if (($additional_guest = $data['number_of_guests'] - $room->max_guest) > 0) {
            $money += $additional_guest * $room->price_charge_guest;
        }

        $data['price_original']  = $money;
        $data['service_fee']     = $room->cleaning_fee;
        $data['coupon_discount'] = $coupon_discount; // TODO Làm thêm phần coupon

        $price = $money
            + (array_key_exists('service_fee', $data) ? $data['service_fee'] : 0)
            + (array_key_exists('additional_fee', $data) ? $data['additional_fee'] : 0)
            - (array_key_exists('coupon_discount', $data) ? $data['coupon_discount'] : 0)
            - (array_key_exists('price_discount', $data) ? $data['price_discount'] : 0);

        $data['total_fee'] = $price;

        return $data;
    }

    /**
     * Kiểm tra validate của các trường khi booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
     * @param array $data
     */
    protected function checkValidBookingTime($room, $data = [])
    {
        $checkin  = Carbon::parse($data['checkin']);
        $checkout = Carbon::parse($data['checkout']);

        $hours = $checkout->copy()->ceilHours()->diffInHours($checkin);
        $dayCI = $checkin->copy()->toDateString();
        $dayCO = $checkout->copy()->toDateString();

        // Trả về lỗi nếu đặt theo giờ nhưng ngày không giống nhau
        if ($dayCI !== $dayCO
            && $data['booking_type'] == BookingConstant::BOOKING_TYPE_HOUR
        ) {
            throw new InvalidDateException('validate-hour', trans2(BookingMessage::ERR_BOOKING_HOUR_INVALID));
        }

        // Trả về lỗi nếu đặt theo kiểu ngày nhưng lại trừng ngày
        if ($dayCI === $dayCO && $data['booking_type'] == BookingConstant::BOOKING_TYPE_DAY) {
            throw new InvalidDateException('validate-hour', trans2(BookingMessage::ERR_BOOKING_INVALID_DAY));
        }

        // Khoảng thời gian đặt phòng phải tối thiểu là TIME_BLOCK
        if ($hours < BookingConstant::TIME_BLOCK) {
            throw new InvalidDateException('time-too-short', trans2(BookingMessage::ERR_SHORTER_THAN_TIMEBLOCK));
        }

        // Trả về lỗi nếu thời gian giữa checkin và thời gian checkin mặc định của phòng

        $roomCI = $checkin->copy()->setTimeFromTimeString($room->checkin);

        $minCI = $roomCI->copy()->addMinutes(-BookingConstant::MINUTE_BETWEEN_BOOK);

        if ($checkin->between($minCI, $roomCI, false)) {
            throw new InvalidDateException('booking-between', trans2(BookingMessage::ERR_TIME_BETWEEN_BOOK));
        }

        // Trả về lỗi nếu thời gian đặt bị trùng với các ngày đã có booking hoặc bị khóa
        $blocked_schedule = $this->getBlockedScheduleByRoomId($room->id);
        $period           = CarbonPeriod::between($checkin, $checkout);
        $days             = [];

        foreach ($period as $item) {
            $days[] = $item->format('Y-m-d');
        }

        if (count(array_intersect($blocked_schedule, $days))) {
            throw new InvalidDateException('schedule-block', trans2(BookingMessage::ERR_SCHEDULE_BLOCK));
        }
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $rop
     * @param       $room
     * @param array $data
     * @param int   $type
     *
     * @return array|float|int
     */
    public function optionalPriceCalculator($rop, $room, $data = [], $type = BookingConstant::BOOKING_TYPE_DAY)
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
            $checkin->setTimeFromTimeString($room->checkin);
            $checkout->setTimeFromTimeString($room->checkout);

            $period = CarbonPeriod::between($checkin, $checkout->addDay());
            foreach ($period as $day) {
                if (in_array($day->dayOfWeek + 1, $weekDays)) {
                    $listDays[] = $day->format('Y-m-d');
                }
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
            $hours = $checkout->copy()->ceilHours()->diffInHours($checkin);
            if (in_array($checkin->format('Y-m-d'), $specialDays)) {
                foreach ($optionalDays as $op) {
                    if ($op->day == $checkin->format('Y-m-d')) {
                        $money += $op->price_hour + ($hours - BookingConstant::TIME_BLOCK) * $op->price_after_hour;
                    }
                }
            } elseif (in_array($checkin->dayOfWeek + 1, $weekDays)) {
                foreach ($optionalWeekDays as $op) {
                    if ($op->weekday == $checkin->dayOfWeek + 1) {
                        $money += $op->price_hour + ($hours - BookingConstant::TIME_BLOCK) * $op->price_after_hour;
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
        $data['checkin']  = Carbon::parse($data['checkin'])->timestamp;
        $data['checkout'] = Carbon::parse($data['checkout'])->timestamp;

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
     * Lấy ra mốc thời gian hủy phòng
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $range
     * @param $i
     * @return mixed
     */
    public function getDay($day, $booking_refund_map_days, $range)
    {
        if (in_array($day, $booking_refund_map_days)) {
            return $day;
        } elseif ($day < min($booking_refund_map_days)) {
            return min($booking_refund_map_days);
        } elseif ($day > max($booking_refund_map_days)) {
            return max($booking_refund_map_days);
        }

        // check mốc theo theo khoảng
        foreach ($range as $value) {
            if (in_array($day, $value)) {
                return max($value);
                break;
            }
        }
    }

    /**
     *  Tao khoảng loc để lọc theo ngày mà  khách hủy.
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $booking_refund_map_days
     * @return array
     */
    public function filter_range_day($booking_refund_map_days)
    {
        $count = count($booking_refund_map_days)-1;
        $range = [];
        for ($i = 0; $i < $count; $i++) {
            $range[] = range($booking_refund_map_days[$i], $booking_refund_map_days[$i+1]);
        }
        return $range;
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
}
