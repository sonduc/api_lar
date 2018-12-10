<?php
/**
 * Created by PhpStorm.
 * User: Hariki
 * Date: 12/7/2018
 * Time: 11:44
 */

namespace App\Repositories\Coupons;

use App\Helpers\ResponseCode;
use App\Repositories\Bookings\BookingConstant;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

trait CouponLogicTrait
{
    protected $cp;

    private $flag      = 0;
    private $flag_bind = 0;

    /**
     * Kiểm tra điều kiện khuyến mãi của 1 booking dựa theo coupon
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $coupon
     * @param $data
     *
     * @return array
     * @throws \Exception
     */
    public function checkSettingDiscount($coupon, $data)
    {
        if ($coupon) {
            $data_allDay  = $coupon->all_day;
            $data_status  = $coupon->status;
            $start_date   = Carbon::parse($coupon->Promotions->date_start);
            $end_date     = Carbon::parse($coupon->Promotions->date_end);
            $current_date = Carbon::now();
            if ($data_status == Coupon::AVAILABLE && $start_date <= $current_date && $end_date >= $current_date) {
                if ($data_allDay == Coupon::AVAILABLE) {
                    return $this->calculateDiscount($coupon, $data);
                }
                $data_settings = json_decode($coupon->settings);
                $discount      = $this->couponSettingsValidate($data_settings, $data, $coupon);
                return $discount;
            }
            throw new \Exception(trans2(CouponMessage::ERR_OUTDATED_COUPON));
        }

        throw new \Exception(trans2(CouponMessage::ERR_INVALID_COUPON));
    }

    /**
     * Tính khuyến mãi của 1 booking dựa theo coupon
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $coupon
     * @param $data
     *
     * @return array
     */
    public function calculateDiscount($coupon, $data)
    {
        $price_original = !empty($data['price_original']) ? $data['price_original'] : 0;
        $price_discount = ($coupon->discount * $price_original) / 100;

        if ($price_discount > $coupon->max_discount) {
            $price_discount = $coupon->max_discount;
        }

        $price_remain = $price_original - $price_discount;

        $dataDiscount = [
            'code'           => ResponseCode::OK,
            'message'        => trans2(CouponMessage::SUCCESS),
            'price_discount' => $price_discount,
            'price_remain'   => $price_remain,
        ];
        return $dataDiscount;
    }

    /**
     * Validate coupon settings
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $data_settings
     * @param $data
     * @param $coupon
     *
     * @return array|int
     */
    public function couponSettingsValidate($data_settings, $data, $coupon)
    {
        $current_date           = Carbon::now();
        $day_of_week            = Carbon::now()->dayOfWeek;
        $discountable           = $non_discount = [];
        $booking_stay_condition = false;

        $arrBind = [
            'rooms'        => 'room_id',
            'cities'       => 'city_id',
            'districts'    => 'district_id',
            'days'         => 'day',
            'booking_type' => 'booking_type',
            'room_type'    => 'room_type',
            'users'        => 'user_id',
            'merchants'    => 'merchant_id',
        ];

        $this->checkCouponBinding($data, $data_settings, $arrBind);

        // Check if coupon settings have booking_create option
        if ($data_settings->booking_create) {
            $dataBookingCreate = $data_settings->booking_create;
            $start_date        = Carbon::parse($dataBookingCreate[0]);
            $end_date          = Carbon::parse($dataBookingCreate[1]);

            if ($current_date->between($start_date, $end_date, false)) {
                $this->checkBindingProp('booking_create', $data_settings);
            }
        }

        // Check if the price original larger than min_price
        if ($data_settings->min_price && !empty($data['price_original']) && $data['price_original'] >= $data_settings->min_price) {
            $this->checkBindingProp('min_price', $data_settings);
        }


        if ($data_settings->booking_stay) {
            $checkin  = array_key_exists('checkin', $data) ? Carbon::parse($data['checkin'])->startOfDay() : null;
            $checkout = array_key_exists('checkout', $data) ? Carbon::parse($data['checkout'])->subDay()->startOfDay() : null;

            if ($checkin && $checkout) {
                $dataBookingCreate = $data_settings->booking_stay;
                $start_discount    = Carbon::parse($dataBookingCreate[0]);
                $end_discount      = Carbon::parse($dataBookingCreate[1]);
                $period_discount   = CarbonPeriod::between($start_discount, $end_discount);
                $list_discount     = $list_stay = [];

                foreach ($period_discount as $day) {
                    $list_discount[] = $day;
                }

                $period_stay = CarbonPeriod::between($checkin, $checkout);

                if (!empty($data['booking_type']) && $data['booking_type'] == BookingConstant::BOOKING_TYPE_DAY) {
                    foreach ($period_stay as $day) {
                        $list_stay[] = $day;
                    }
                    $non_discount = array_diff($list_stay, $list_discount);
                    $discountable = array_intersect($list_discount, $list_stay);
                } else {
                    $discountable = in_array($checkin->toDateString(), $list_discount)
                        ? [$checkin->toDateString()]
                        : [];

                    $non_discount = array_diff([$checkin->toDateString()], $list_discount);
                }

                if ($discountable) {
                    $booking_stay_condition = $this->checkBindingProp('booking_stay', $data_settings);
                }
            }
        }

        if ($data_settings->days_of_week && in_array($day_of_week, $data_settings->days_of_week)) {
            $this->checkBindingProp('days_of_week', $data_settings);
        }

        $bindCount = \count($data_settings->bind);
        $discount  = 0;

        $errorMsg = [
            'code'           => ResponseCode::UNPROCESSABLE_ENTITY,
            'message'        => trans2(CouponMessage::ERR_CANNOT_APPLY_COUPON),
            'price_discount' => 0,
        ];

        if (!$booking_stay_condition) {
            if (($bindCount > 0 && $this->flag_bind >= $bindCount) || ($bindCount == 0 && $this->flag > $bindCount)) {
                $discount = $this->calculateDiscount($coupon, $data);
            }
        } else {
            if (($bindCount > 0 && $this->flag_bind >= $bindCount) || ($this->flag_bind > $bindCount)) {
                $discount = $this->calculateDiscountByDateStay($coupon, $data, $discountable, $non_discount);
            }
        }
        return ($discount > 0) ? $discount : $errorMsg;
    }

    /**
     * Check coupon setting with list binding
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $data
     * @param       $data_settings
     * @param array $bindList
     */
    private function checkCouponBinding($data, $data_settings, array $bindList): void
    {
        foreach ($bindList as $bindingField => $key) {
            $data_binding = is_array($data_settings->$bindingField)
                ? $data_settings->$bindingField
                : [$data_settings->$bindingField];
            if (array_key_exists($key, $data)
                && in_array($data[$key], $data_binding)
            ) {
                $this->checkBindingProp($bindingField, $data_settings);
            }
        }
    }

    /**
     * Check coupon prop in settings
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $bindingField
     * @param $data_settings
     *
     * @return bool
     */
    private function checkBindingProp($bindingField, $data_settings): bool
    {
        $status = in_array($bindingField, $data_settings->bind);

        if ($status) $this->flag_bind++;
        $this->flag++;

        return $status;
    }

    /**
     * Tính phần tiền được giảm giá dựa trên ngày ở của khách
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $coupon
     * @param $data
     * @param $discount_date
     * @param $non_discount
     *
     * @return array
     */
    public function calculateDiscountByDateStay($coupon, $data, $discount_date, $non_discount)
    {
        $room               = $this->room->getById($data['room_id']);
        $charge_guest       = $data['number_of_guest'] - $room['max_guest'];
        $total_non_discount = $room->price_day * \count($non_discount);
        $total_discountable = 0;

        foreach ($discount_date as $k => $val) {
            $val     = Carbon::parse($val);
            $weekday = $val->addDay()->dayOfWeek;
            $day     = $val->toDateString();

            $response = $this->op->getPriceByDay($data['room_id'], ['weekday' => $weekday, 'day' => $day]);

            if ($response) {
                $total_discountable += $response->price_day;
                if ($charge_guest > 0) {
                    $total_discountable += $response->price_charge_guest * $charge_guest;
                }
            }
        }
        $total_discount = ($coupon->discount * $total_discountable) / 100;
        if ($total_discount > $coupon->max_discount) {
            $price_discount = $coupon->max_discount;
        } else {
            $price_discount = $total_discount;
        }

        $price_discount_remain = $total_discountable - $price_discount;

        $price_remain = $total_non_discount + $price_discount_remain;

        $dataDiscount = [
            'message'        => trans2(CouponMessage::SUCCESS),
            'price_discount' => $price_discount,
            'price_remain'   => $price_remain,
        ];
        return $dataDiscount;
    }
}
