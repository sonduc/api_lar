<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 19/11/2018
 * Time: 11:08
 */

namespace App\Repositories\Bookings;

use App\Repositories\Coupons\CouponRepository;
use App\Repositories\Rooms\RoomOptionalPrice;
use App\Repositories\Rooms\RoomOptionalPriceRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\Exceptions\InvalidDateException;
use App\User;
use phpDocumentor\Reflection\DocBlock\Description;

trait BookingLogicTrait
{
    /** @var CouponRepository $cp */
    protected $cp;
    /** @var RoomOptionalPriceRepository $op */
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
        
        // Kiểm tra thời gian booking theo giờ  xem có trùng với thời gian checkin, checkout mặc định của phòng không
        //Nếu trùng trong khoảng chechin , checkout thì tính giá 2 ngày
        // Tính tiền dựa theo kiểu booking
        if ($data['booking_type'] == BookingConstant::BOOKING_TYPE_DAY) {
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
        
        if ($data['booking_type'] == BookingConstant::BOOKING_TYPE_HOUR) {
            if ($this->checkBookTimeByCheckInOrCheckOut($room, $data, $checkin, $checkout) == true) {
                $days             = BookingConstant::PRICE_TYPE_HOUR_SPECICAL;
                $data['checkin']  = $checkin->timestamp;
                $data['checkout'] = $checkout->timestamp;
                // Xử lý logic tính giá phòng vào ngày đặc biệt
                list($money, $totalDay) =
                   $this->optionalPriceCalculator($room_optional_prices, $room, $data, BookingConstant::BOOKING_TYPE_DAY);
                $money += $room->price_day * ($days - $totalDay);
            } else {
                $hours         = $checkout->copy()->ceilHours()->diffInHours($checkin);
                $data['hours'] = $hours;

                $data['checkin']  = $checkin->timestamp;
                $data['checkout'] = $checkout->timestamp;
                // Xử lý logic tính giá phòng vào ngày đặc biệt
                $money =
                   $this->optionalPriceCalculator($room_optional_prices, $room, $data, BookingConstant::BOOKING_TYPE_HOUR)
                   ?? 0;
                if ($money == 0) {
                    $money =
                       $room->price_hour + ($hours - BookingConstant::TIME_BLOCK) * $room->price_after_hour;
                }
            }
        }

        // Tính tiền dựa theo số khách
        if (($additional_guest = $data['number_of_guests'] - $room->max_guest) > 0) {
            $money += $additional_guest * $room->price_charge_guest;
        }

        $data['price_original'] = $money;
        $data['service_fee']    = $room->cleaning_fee;
        if (!empty($data['coupon'])) {
            $coupon              = $this->cp->getCouponByCode($data['coupon']);
            $data['city_id']     = $room->city_id;
            $data['district_id'] = $room->district_id;
            
            $coupon_discount     = $this->checkSettingDiscount($coupon, $data);
            $data['coupon_discount'] = $coupon_discount['price_discount'];
        }
        
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

        $hours = $checkout->copy()->ceilMinute()->diffInHours($checkin);
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

        // Trả về lỗi nếu thời gian đặt bị trùng với các ngày  bị khóa chủ động va khóa dựa theo những booking đặtk phòng này theo ngày.
        if ($data['booking_type'] == BookingConstant::BOOKING_TYPE_HOUR) {
            $blocked_schedule = $this->getBlockedScheduleByRoomId($room->id);
            // dd($blocked_schedule);
        } else {
            $blocked_schedule = $this->getBlockedScheduleDayByRoomId($room->id);
        }

//        dd($blocked_schedule);

        $period           = CarbonPeriod::between($checkin, $checkout);
        $days             = [];
        foreach ($period as $item) {
            $days[] = $item->format('Y-m-d');
        }

        if (array_intersect($blocked_schedule, $days)) {
            throw new InvalidDateException('schedule-block', trans2(BookingMessage::ERR_SCHEDULE_BLOCK));
        }

        // Kiểm tra tính hợp lệ của thời gian đặt phòng theo kiểu giờ

        if ($data['booking_type'] == BookingConstant::BOOKING_TYPE_HOUR) {
            $this->checkValidBookingTypeHour($room->id, $checkin, $checkout);
        }

        // Kiểm tra tính hợp lệ của thời gian đặt phòng theo kiểu ngày
        if ($data['booking_type'] == BookingConstant::BOOKING_TYPE_DAY) {
            // Trả về lỗi nếu thời gian giữa checkin và thời gian checkin mặc định của phòng
            $roomCI = $checkin->copy()->setTimeFromTimeString($room->checkin);

            $minCI = $roomCI->copy()->addMinutes(-BookingConstant::MINUTE_BETWEEN_BOOK);


            if ($checkin->between($minCI, $roomCI, false)) {
                throw new InvalidDateException('booking-between', trans2(BookingMessage::ERR_TIME_BETWEEN_BOOK));
            }

            // Trả về lỗi nếu thời gian booking bị trùng so với thời gian khóa của phòng này
            $this->checkValidBookingTypeDay($room, $checkin, $checkout);
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
        $checkin            = Carbon::createFromTimestamp($data['checkin']);
        $checkout           = Carbon::createFromTimestamp($data['checkout']);
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
            $period = CarbonPeriod::between($checkin, $checkout);
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
                    $day = Carbon::parse($op->day)->addHours(15);
                    if ($day->between($checkin, $checkout)) {
                        $money += $op->price_day;
                        $totalDayOfDiscount++;
                    }
                }
            }


            // Tính tiền cho các ngày giảm giá trong tuần;
            foreach ($optionalWeekDays as $op) {
                foreach ($otherDays as $day) {
                    $day = Carbon::parse($day)->addHours(15);
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
                // dd($money);
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
//        $data['checkin']  = Carbon::createFromTimestamp($booking->checkin)->toDateTimeString();
//        $data['checkout'] = Carbon::createFromTimestamp($booking->checkout)->toDateTimeString();
        $data             = array_merge($booking->toArray(), $data);
        return $this->update($id, $data);
    }


    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
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

        $booking_settings = json_decode($data_booking->settings);

        // Nếu book này có settigns == null hoặc không có chính sách hủy phòng
        if (empty($booking_settings) || $booking_settings->no_booking_cancel == 1) {
            $total_refund   = ($data_booking->total_fee * 0) / 100;
            $booking_update = [
                'status'       => BookingConstant::BOOKING_CANCEL,
                'total_refund' => $total_refund,
            ];

            parent::update($id, $booking_update);
            $data['booking_id'] = $id;
            return $this->booking_cancel->store($data);
        }

        // thời gian check_in booking
        $checkin = Carbon::createFromTimestamp($data_booking['checkin']);

        // thời gian hủy phòng
        $timeNow = Carbon::now();
        $seconds = $checkin->diffInSeconds($timeNow);
        if ($seconds >= $booking_settings->refunds[0]->days * 24 * 3600) {
            // Nếu thời gian huỷ lớn hơn  hoặc thời gian cho phép thì hòan lại 100% tiền
            $total_refund   = ($data_booking->total_fee * 100) / 100;
            $booking_update = [
                'status'       => BookingConstant::BOOKING_CANCEL,
                'total_refund' => $total_refund,
            ];

            parent::update($id, $booking_update);
            $data['booking_id'] = $id;
            return $this->booking_cancel->store($data);
        }
        // Nếu thời gian huỷ lớn hơn  hoặc thời gian cho phép thì hòan lại 100 tiền
        $total_refund   = ($data_booking->total_fee * 0) / 100;
        $booking_update = [
            'status'       => BookingConstant::BOOKING_CANCEL,
            'total_refund' => $total_refund,
        ];
        
        parent::update($id, $booking_update);
        $data['booking_id'] = $id;
        return $this->booking_cancel->store($data);
    }

    /**
     * Thêm khoảng tuổi
     * @author sonduc <ndson1998@gmail.com>
     *
     * @param array $data
     *
     * @return array
     */
    public function addAgeRange($data = [])
    {
        $list_range = User::AGE_RANGE_LIST;
        $age = Carbon::parse($data['birthday'])->age;
        $age_range = array_keys(User::AGE_RANGE)[count(User::AGE_RANGE) - 1];

        foreach ($list_range as $key => $item) {
            if ($age <= $item) {
                $age_range = $key;
                break;
            }
        }
        $data['age_range'] = $age_range;
        return $data;
    }


    /**
     *  Kiểm tra tính hợp lệ của thời gian khi booking theo giờ
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param $checkin
     * @param $checkout
     */
    public function checkValidBookingTypeHour($id, $checkin, $checkout)
    {
        $data_booking           = $this->booking->getFutureBookingByRoomId($id);

        // Danh sách các ngày bị block do dựa theo booking
        foreach ($data_booking as $item) {
            $CI     = Carbon::createFromTimestamp($item->checkin)->addMinutes(-BookingConstant::MINUTE_BETWEEN_BOOK_TYPE_HOUR);
            $CO     = Carbon::createFromTimestamp($item->checkout)->addMinutes(+BookingConstant::MINUTE_BETWEEN_BOOK_TYPE_HOUR);
            if ($checkin->between($CI, $CO) || $checkout->between($CI, $CO)) {
                throw new InvalidDateException('schedule-block-type-hour', trans2(BookingMessage::ERR_SCHEDULE_BLOCK_TYPE_HOUR));
            }
        }
    }

    /**
     * Kiểm tra tính hợp lệ của thời gian khi booking theo ngày
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param $checkin
     * @param $checkout
     */

    public function checkValidBookingTypeDay($room, $checkin, $checkout)
    {
        $data_booking           = $this->booking->getFutureBookingByRoomId($room->id);
        $list                   = [];
        $data_booking_type_hour = [];

        foreach ($data_booking as $item) {
            if ($item['booking_type'] == BookingConstant::BOOKING_TYPE_HOUR) {
                $data_booking_type_hour[] = $item;
            }
        }

        // trả lại những ngày bị khóa do booking theo giờ nằm trong khoảng checkin , checkout mặc định của phòng đó
        $roomCI = $checkin->copy()->setTimeFromTimeString($room->checkin);
        $roomCO = $checkin->copy()->setTimeFromTimeString($room->checkout)->addDay(1);


        foreach ($data_booking_type_hour as $value) {
            $CI     = Carbon::createFromTimestamp($value->checkin);
            $CO     = Carbon::createFromTimestamp($value->checkout);



            if ($CI->addMinutes(-30)->between($roomCI, $roomCO) || $CO->between($roomCI, $roomCO)) {
                throw new InvalidDateException('schedule-block', trans2(BookingMessage::ERR_SCHEDULE_BLOCK));
            }
        }
    }


    /**
     * Kiểm tra thời gian booking theo giờ  xem có trùng với thời gian checkin, checkout mặc định của phòng không
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $room
     * @param $data
     * @param $checkin
     * @param $checkout
     * @return bool
     */

    public function checkBookTimeByCheckInOrCheckOut($room, $data, $checkin, $checkout)
    {
        $roomCI = $checkin->copy()->setTimeFromTimeString($room->checkin);
        $roomCO = $checkin->copy()->setTimeFromTimeString($room->checkout);

        if ($data['booking_type'] == BookingConstant::BOOKING_TYPE_HOUR) {
            if ($roomCO->between($checkin, $checkout) || $roomCI->between($checkin, $checkout)) {
                return true;
            }
        }
    }
}
