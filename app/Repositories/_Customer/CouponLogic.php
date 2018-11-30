<?php

namespace App\Repositories\_Customer;

use App\Repositories\BaseLogic;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Rooms\RoomTranslateRepositoryInterface;
use App\Repositories\Rooms\RoomOptionalPriceRepositoryInterface;
use App\Repositories\Cities\CityRepositoryInterface;
use App\Repositories\Districts\DistrictRepositoryInterface;
use App\Repositories\Coupons\CouponRepositoryInterface;
use App\Repositories\Coupons\Coupon;
use App\Repositories\Bookings\BookingConstant;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CouponLogic extends BaseLogic
{
    protected $model;
    protected $room;
    protected $room_translate;
    protected $city;
    protected $district;

    public function __construct(
        CouponRepositoryInterface $coupon,
        RoomTranslateRepositoryInterface $room_translate,
        RoomRepositoryInterface $room,
        CityRepositoryInterface $city,
        DistrictRepositoryInterface $district,
        RoomOptionalPriceRepositoryInterface $op
    ) {
        $this->model 			= $coupon;
        $this->room 			= $room;
        $this->room_translate 	= $room_translate;
        $this->city 			= $city;
        $this->district 		= $district;
        $this->op 		        = $op;
    }

    /**
     * Chuyển đổi json setting sang mảng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function transformCoupon($data)
    {
        $settings               = json_decode($data['settings']);
        $arrRoom                = !empty($settings->rooms) ? $this->room_translate->getRoomByListId($settings->rooms) : [];
        $arrCity 		        = !empty($settings->cities) ? $this->city->getCityByListId($settings->cities) : [];
        $arrDistrict 	        = !empty($settings->districts) ? $this->district->getDistrictByListId($settings->districts) : [];
        $arrDay 		        = !empty($settings->days) ? $settings->days : [];
        $arrBookingType         = !empty($settings->booking_type) ? $settings->booking_type : [];
       
        if (!empty($settings->booking_stay)) {
            $dataBookingStay        = $settings->booking_stay;

            $start_stay         = Carbon::parse($dataBookingStay[0]);
            $end_stay           = Carbon::parse($dataBookingStay[1]);
            $period_stay        = CarbonPeriod::between($start_stay, $end_stay);

            foreach ($period_stay as $day) {
                $list_stay[] = $day->format('Y-m-d');
            }
        } else {
            $list_stay = [];
        }

        if (!empty($settings->booking_create)) {
            $dataBookingCreate      = $settings->booking_create;

            $start_create         = Carbon::parse($dataBookingCreate[0]);
            $end_create           = Carbon::parse($dataBookingCreate[1]);
            $period_create        = CarbonPeriod::between($start_create, $end_create);

            foreach ($period_discount as $day) {
                $list_create[] = $day->format('Y-m-d');
            }
        } else {
            $list_create = [];
        }
        $arrBookingCreate   = $list_create;
        $arrBookingStay     = $list_stay;

        $arrMerchants 	        = !empty($settings->merchants) ? $settings->merchants : [];
        $arrUsers		        = !empty($settings->users) ? $settings->users : [];
        $arrDayOfWeek		    = !empty($settings->days_of_week) ? $settings->days_of_week : [];

        $arrayTransformSetting = [
            'rooms' 	        => $arrRoom,
            'cities' 	        => $arrCity,
            'districts'         => $arrDistrict,
            'days' 		        => $arrDay,
            'bind'              => $bind,
            'rooms' 	        => $arrRoom,
            'cities' 	        => $arrCity,
            'districts'         => $arrDistrict,
            'days' 		        => $arrDay,
            'booking_type' 	    => $arrBookingType,
            'booking_create' 	=> $arrBookingCreate,
            'booking_stay'      => $arrBookingStay,
            'merchants' 		=> $arrMerchants,
            'users' 	        => $arrUsers,
            'days_of_week' 	    => $arrDayOfWeek,
            'room_type'         => $arrRoomType,
            'min_price'         => $arrMinPrice,
        ];
        $data['settings'] 	= $arrayTransformSetting;
        return $data;
    }

    /**
     * Cập nhật số lần dùng cho 1 coupon
     * @author sonduc <ndson1998@gmail.com>
     */
    public function updateUsable($code)
    {
        $coupon 	= $this->getCouponByCode(strtoupper($code));
        if ($coupon) {
            $id 		= $coupon['id'];
            $coupon 	= (array) json_decode($coupon);
            
            if ($coupon['usable'] > 0) {
                $coupon['usable'] 	= $coupon['usable']-1;
                $coupon['used'] 	= $coupon['used']+1;
                $data_coupon 		= parent::update($id, $coupon);
                return $data_coupon;
            } else {
                throw new \Exception('Mã khuyến mãi đã hết số lần sử dụng');
            }
        }
    }

    /**
     * Lấy dữ liệu theo mã code
     *
     * @author sonduc <ndson1998@gmail.com>
     */
    public function getCouponByCode($code)
    {
        $coupon = $this->model->getCouponByCode($code);
        return $coupon;
    }

    
    /**
     * Kiểm tra điều kiện khuyến mãi của 1 booking dựa theo coupon
     *
     * @author sonduc <ndson1998@gmail.com>
     */
    public function checkSettingDiscount($coupon, $data)
    {
        if ($coupon) {
            $data_allDay        = $coupon->all_day;
            $data_status        = $coupon->status;
            $start_date         = new Carbon($coupon->Promotions->date_start);
            $end_date           = new Carbon($coupon->Promotions->date_end);
            $current_date       = Carbon::now();

            if ($data_status == Coupon::AVAILABLE) {
                if ($start_date <= $current_date && $end_date >= $current_date) {
                    if ($data_allDay == Coupon::AVAILABLE) {
                        return $this->calculateDiscount($coupon, $data);
                    } else {
                        $data_settings  = json_decode($coupon->settings);

                        $discount = $this->couponSettingsValidate($data_settings, $data, $coupon);

                        return $discount;
                    }
                } else {
                    throw new \Exception('Mã khuyến mãi không hợp lệ hoặc đã hết hạn');
                }
            } else {
                throw new \Exception('Mã khuyến mãi không hợp lệ hoặc đã hết hạn');
            }
        } else {
            throw new \Exception('Mã khuyến mãi không tồn tại');
        }
    }


    public function couponSettingsValidate($data_settings, $data, $coupon)
    {
        $current_date       = Carbon::now();
        $day_of_week        = date('N', strtotime(date("l")));
  
        if ($data_settings->bind) {
            $conditions = $data_settings->bind;
        }
        $flag                   = 0;
        $booking_stay_condition = false;
        $flag_bind              = 0;

        if ($data_settings->rooms && !empty($data['room_id']) && in_array($data['room_id'], $data_settings->rooms)) {
            if (in_array("rooms", $data_settings->bind)) {
                $flag_bind++;
            }
            $flag++;
        }

        if ($data_settings->cities && !empty($data['city_id']) && in_array($data['city_id'], $data_settings->cities)) {
            if (in_array("cities", $data_settings->bind)) {
                $flag_bind++;
            }
            $flag++;
        }

        if ($data_settings->districts && !empty($data['district_id']) && in_array($data['district_id'], $data_settings->districts)) {
            if (in_array("districts", $data_settings->bind)) {
                $flag_bind++;
            }
            $flag++;
        }

        if ($data_settings->days && !empty($data['day']) && in_array($data['day'], $data_settings->days)) {
            if (in_array("days", $data_settings->bind)) {
                $flag_bind++;
            }
            $flag++;
        }

        if ($data_settings->booking_type && !empty($data['booking_type']) && in_array($data['booking_type'], [$data_settings->booking_type])) {
            if (in_array("booking_type", $data_settings->bind)) {
                $flag_bind++;
            }
            $flag++;
        }

        if ($data_settings->booking_create) {
            $dataBookingCreate  = $data_settings->booking_create;
            $start_date         = Carbon::parse($dataBookingCreate[0]);
            $end_date           = Carbon::parse($dataBookingCreate[1]);
            if ($current_date->between($start_date, $end_date, false)) {
                if (in_array("booking_create", $data_settings->bind)) {
                    $flag_bind++;
                }
                $flag++;
            }
        }

        if ($data_settings->booking_stay) {
            $dataBookingCreate      = $data_settings->booking_stay;

            $start_discount         = Carbon::parse($dataBookingCreate[0]);
            $end_discount           = Carbon::parse($dataBookingCreate[1]);
            $period_discount        = CarbonPeriod::between($start_discount, $end_discount);
            
            foreach ($period_discount as $day) {
                $list_discount[] = $day;
            }

            $checkin        = $data['checkin'];
            $checkout       = $data['checkout'];

            $period_stay    = CarbonPeriod::between(Carbon::parse($checkin)->startOfDay(), Carbon::parse($checkout)->subDay()->startOfDay());

            foreach ($period_stay as $day) {
                $list_stay[] = $day;
            }
            $discountable = array_intersect($list_discount, $list_stay);
            $non_discount = array_diff($list_stay, $list_discount);
            if ($discountable) {
                if (in_array("booking_stay", $data_settings->bind)) {
                    $flag_bind++;
                    $booking_stay_condition = true;
                }
                $flag++;
            }
        }

        if ($data_settings->merchants && !empty($data['merchant_id']) && in_array($data['merchant_id'], $data_settings->merchants)) {
            if (in_array("merchants", $data_settings->bind)) {
                $flag_bind++;
            }
            $flag++;
        }

        if ($data_settings->users && !empty($data['user_id']) && in_array($data['user_id'], $data_settings->users)) {
            if (in_array("users", $data_settings->bind)) {
                $flag_bind++;
            }
            $flag++;
        }

        if ($data_settings->days_of_week && in_array($day_of_week, $data_settings->days_of_week)) {
            if (in_array("days_of_week", $data_settings->bind)) {
                $flag_bind++;
            }
            $flag++;
        }

        if ($data_settings->room_type && !empty($data['room_type']) && in_array($data['room_type'], $data_settings->room_type)) {
            if (in_array("room_type", $data_settings->bind)) {
                $flag_bind++;
            }
            $flag++;
        }
        if ($booking_stay_condition == false) {
            if (sizeof($data_settings->bind) > 0 && $flag_bind >= sizeof($data_settings->bind)) {
                $discount =  $this->calculateDiscount($coupon, $data);
            } elseif (sizeof($data_settings->bind) == 0 && $flag > sizeof($data_settings->bind)) {
                $discount =  $this->calculateDiscount($coupon, $data);
            } else {
                $discount = [
                    'message'        => 'Mã giảm giá không thể áp dụng cho đơn đặt phòng này',
                    'price_discount' => 0
                ];
            }
        } else {
            if (sizeof($data_settings->bind) > 0 && $flag_bind >= sizeof($data_settings->bind)) {
                $discount =  $this->calculateDiscountByDateStay($coupon, $data, $discountable, $non_discount);
            } elseif ($flag_bind > sizeof($data_settings->bind)) {
                $discount =  $this->calculateDiscountByDateStay($coupon, $data, $discountable, $non_discount);
            } else {
                $discount = [
                    'message'        => 'Mã giảm giá không thể áp dụng cho đơn đặt phòng này',
                    'price_discount' => 0
                ];
            }
        }
        return $discount;
    }

    /**
     * Tính khuyến mãi của 1 booking dựa theo coupon
     *
     * @author sonduc <ndson1998@gmail.com>
     */
    public function calculateDiscount($coupon, $data)
    {
        $price_original = !empty($data['price_original']) ? $data['price_original'] : 0;
        $price_discount = ($coupon->discount * $price_original)/100;

        if ($price_discount > $coupon->max_discount) {
            $price_discount = $price_original - $coupon->max_discount;
        }

        $price_remain = $price_original -  $price_discount;

        $dataDiscount = [
            'message'        => "Mã giảm giá được áp dụng thành công",
            'price_discount' => $price_discount,
            'price_remain'   => $price_remain
        ];
        return $dataDiscount;
    }

    /**
     * Tính phần tiền được giảm giá dựa trên ngày ở của khách
     *
     * @author sonduc <ndson1998@gmail.com>
     */
    public function calculateDiscountByDateStay($coupon, $data, $discount_date, $non_discount)
    {
        $room               = $this->room->getById($data['room_id']);
        $room_op            = $this->op->getOptionalPriceByRoomId($data['room_id']);
        $charge_guest       = $data["number_of_guest"] - $room["max_guest"];
        $total_non_discount = $room->price_day * sizeof($non_discount);
        $total_discountable = 0;

        foreach ($discount_date as $k => $val) {
            $weekday = Carbon::parse($val)->weekday() + 1;
            $day = $val->format('Y-m-d');

            $response = $this->op->getPriceByDay($data['room_id'], ["weekday" => $weekday,"day" => $day]);

            if ($response) {
                $total_discountable += $response->price_day;
                if ($charge_guest > 0) {
                    $total_discountable += $response->price_charge_guest * $charge_guest;
                }
            }
        }
        $total_discount = ($coupon->discount * $total_discountable)/100;
        if ($total_discount > $coupon->max_discount) {
            $price_discount = $coupon->max_discount;
        } else {
            $price_discount = $total_discount;
        }
        
        $price_discount_remain = $total_discountable -  $price_discount;

        $price_remain = $total_non_discount + $price_discount_remain;

        $dataDiscount = [
            'message'        => "Mã giảm giá được áp dụng thành công hahaha",
            'price_discount' => $price_discount,
            'price_remain'   => $price_remain
        ];
        return $dataDiscount;
    }
}
