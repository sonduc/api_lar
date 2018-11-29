<?php

namespace App\Repositories\Coupons;

use App\Repositories\BaseLogic;
use App\Repositories\Rooms\Room;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Rooms\RoomTranslateRepositoryInterface;
use App\Repositories\Cities\CityRepositoryInterface;
use App\Repositories\Districts\DistrictRepositoryInterface;
use App\Repositories\Users\UserRepositoryInterface;
use App\User;
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
    protected $user;

    public function __construct(
        CouponRepositoryInterface $coupon,
        RoomTranslateRepositoryInterface $room_translate,
        RoomRepositoryInterface $room,
        CityRepositoryInterface $city,
        DistrictRepositoryInterface $district,
        UserRepositoryInterface $user
    ) {
        $this->model 			= $coupon;
        $this->room 			= $room;
        $this->room_translate 	= $room_translate;
        $this->city 			= $city;
        $this->district 		= $district;
        $this->user 		    = $user;
    }

    /**
    * Thêm mới dữ liệu vào coupon
    * @author sonduc <ndson1998@gmail.com>
    *
    * @param array $data
    *
    * @return \App\Repositories\Eloquent
    */
    public function store($data)
    {
        $data['code'] 		= strtoupper($data['code']);
        $data['settings'] 	= json_encode($data['settings']);
        $data_coupon 		= parent::store($data);
        return $data_coupon;
    }

    /**
    * Cập nhật dữ liệu cho promotion
    * @author sonduc <ndson1998@gmail.com>
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
        $coupon = $this->model->getById($id);
        if ($coupon->used > 0) {
            throw new \Exception('Không có quyền sửa đổi mục này');
        };
        
        $data['code']       = strtoupper($data['code']);
        $data['settings'] 	= json_encode($data['settings']);
        $data_coupon 		= parent::update($id, $data);
        return $data_coupon;
    }

    /**
    * Cập nhật trường trạng thái status
    * @author sonduc <ndson1998@gmail.com>
    *
    * @param $id
    * @param $data
    *
    * @return \App\Repositories\Eloquent
    */
    public function singleUpdate($id, $data)
    {
        $data_coupon = parent::update($id, $data);
        return $data_coupon;
    }

    /**
     * Chuyển đổi json setting sang mảng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function transformListCoupon($scope, $pageSize, $trash, $coupon)
    {
        $list_room_id       = [];
        $list_city_id       = [];
        $list_district_id   = [];
        $list_merchant_id   = [];
        $list_user_id       = [];
        if (!$coupon) {
            $data = $this->model->getByQuery($scope, $pageSize, $trash);
        } else {
            $data = [$coupon];
        }
        if (sizeOf($data) != 0) {
            foreach ($data as $key => $value) {
                $settings               = json_decode($value->settings);

                $list_room_id           = array_unique(array_merge((!empty($settings->rooms) ? $settings->rooms : []), $list_room_id));

                $list_city_id           = array_unique(array_merge((!empty($settings->cities) ? $settings->cities : []), $list_city_id));

                $list_district_id       = array_unique(array_merge((!empty($settings->districts) ? $settings->districts : []), $list_district_id));

                $list_merchant_id       = array_unique(array_merge((!empty($settings->merchants) ? $settings->merchants : []), $list_merchant_id));

                $list_user_id           = array_unique(array_merge((!empty($settings->user) ? $settings->user : []), $list_user_id));
            }
            $arrData = $this->transformCouponIndex($list_room_id, $list_city_id, $list_district_id, (!empty($settings->days) ? $settings->days : []), $list_merchant_id, $list_user_id, $data);
            return sizeOf($data) == 1 ? $arrData[0] : $arrData ;
        } else {
            return $data;
        }
    }

    /**
     * Chuyển đổi json setting sang mảng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function transformCouponIndex($rooms = [], $cities = [], $districts = [], $days = [], $merchants = [], $users = [], $coupons = [])
    {
        $arrRoom        = $this->room_translate->getRoomByListIdIndex($rooms);
        $arrCity 		= $this->city->getCityByListIdIndex($cities);
        $arrDistrict 	= $this->district->getDistrictByListIdIndex($districts);
        $arrBookingType = arrayToObject(BookingConstant::BOOKING_TYPE);
        $arrMerchants 	= $this->user->getUserByListIdIndex($merchants, User::IS_OWNER);
        $arrUsers 	    = $this->user->getUserByListIdIndex($users, User::NOT_OWNER);
        $arrRoomType    = arrayToObject(Room::ROOM_TYPE);
        $arrDay 		= $days;
        $arrDate        = [
            [
                "id"    => Carbon::MONDAY + 1,
                "name"  => "Thứ hai"
            ],
            [
                "id"    => Carbon::TUESDAY + 1,
                "name"  => "Thứ ba"
            ],
            [
                "id"    => Carbon::WEDNESDAY + 1,
                "name"  => "Thứ tư"
            ],
            [
                "id"    => Carbon::THURSDAY + 1,
                "name"  => "Thứ năm"
            ],
            [
                "id"    => Carbon::FRIDAY + 1,
                "name"  => "Thứ sáu"
            ],
            [
                "id"    => Carbon::SATURDAY + 1,
                "name"  => "Thứ bảy"
            ],
            [
                "id"    => Carbon::SUNDAY + 1,
                "name"  => "Chủ nhật"
            ]
        ];
        
        foreach ($coupons as $key => $value) {
            $settings = json_decode($value->settings);
            
            $bind                       = (!empty($settings->bind) ? $settings->bind : []);
            
            $arrRoom_filter             = array_values(
                array_filter($arrRoom, function ($item) use ($settings) {
                    return in_array($item['id'], (!empty($settings->rooms) ? $settings->rooms : []));
                })
            );

            $arrCity_filter             = array_values(
                array_filter($arrCity, function ($item) use ($settings) {
                    return in_array($item['id'], (!empty($settings->cities) ? $settings->cities : []));
                })
            );

            
            $arrDistrict_filter         = array_values(
                array_filter($arrDistrict, function ($item) use ($settings) {
                    return in_array($item['id'], (!empty($settings->districts) ? $settings->districts : []));
                })
            );

            $arrBookingType_filter      = array_values(
                array_filter($arrBookingType, function ($item) use ($settings) {
                    return in_array($item['id'], (!empty($settings->booking_type) ? [$settings->booking_type] : []));
                })
            );
            
            $arrBookingCreate_filter    = !empty($settings->booking_create) ? $settings->booking_create : [];
            
            $arrBookingStay_filter      = !empty($settings->booking_stay) ? $settings->booking_stay : [];
            
            $arrMerchant_filter         = array_values(
                array_filter($arrMerchants, function ($item) use ($settings) {
                    return in_array($item['id'], (!empty($settings->merchants) ? $settings->merchants : []));
                })
            );
            
            $arrUser_filter             = array_values(
                array_filter($arrUsers, function ($item) use ($settings) {
                    return in_array($item['id'], (!empty($settings->users) ? $settings->users : []));
                })
            );
            
            $arrDate_filter             = array_values(
                array_filter($arrUsers, function ($item) use ($settings) {
                    return in_array($item['id'], (!empty($settings->days_of_week) ? $settings->days_of_week : []));
                })
            );
            
            $arrRoomType_filter         = array_values(
                array_filter($arrRoomType, function ($item) use ($settings) {
                    return in_array($item['id'], (!empty($settings->room_type) ? [$settings->room_type] : []));
                })
            );
            
            $arrMinPrice_filter         = !empty($settings->min_price) ? $settings->min_price : [];

            $arrayTransformSetting      = [
                'bind'              => $bind,
                'rooms' 	        => $arrRoom_filter,
                'cities' 	        => $arrCity_filter,
                'districts'         => $arrDistrict_filter,
                'days' 		        => $arrDay,
                'booking_type' 	    => $arrBookingType_filter,
                'booking_create' 	=> $arrBookingCreate_filter,
                'booking_stay'      => $arrBookingStay_filter,
                'merchants' 		=> $arrMerchant_filter,
                'users' 	        => $arrUser_filter,
                'days_of_week' 	    => $arrDate_filter,
                'room_type'         => $arrRoomType_filter,
                'min_price'         => $arrMinPrice_filter,
            ];

            $coupons[$key]->settings    = json_encode($arrayTransformSetting);
        }
        return $coupons;
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
                $coupon['usable'] 	= $coupon['usable'] - 1;
                $coupon['used'] 	= $coupon['used']   + 1;
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
        $flag = 0;

        if ($data_settings->rooms && !empty($data['room_id']) && in_array($data['room_id'], $data_settings->rooms)) {
            $flag++;
        }

        if ($data_settings->cities && !empty($data['city_id']) && in_array($data['city_id'], $data_settings->cities)) {
            $flag++;
        }

        if ($data_settings->districts && !empty($data['district_id']) && in_array($data['district_id'], $data_settings->districts)) {
            $flag++;
        }

        if ($data_settings->days && !empty($data['day']) && in_array($data['day'], $data_settings->days)) {
            $flag++;
        }

        if ($data_settings->booking_type && !empty($data['booking_type']) && in_array($data['booking_type'], [$data_settings->booking_type])) {
            $flag++;
        }

        if ($data_settings->booking_create) {
            $dataBookingCreate  = $data_settings->booking_create;
            $start_date         = Carbon::parse($dataBookingCreate[0]);
            $end_date           = Carbon::parse($dataBookingCreate[1]);
            if ($current_date->between($start_date, $end_date, false)) {
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

            $checkin    = $data['checkin'];
            $checkout   = $data['checkout'];

            $period_stay = CarbonPeriod::between(Carbon::parse($checkin), Carbon::parse($checkout));
            foreach ($period_stay as $day) {
                $list_stay[] = $day;
            }
            // dd(array_intersect($list_discount, $list_stay));
            dd($list_stay, $list_discount);
        }

        if ($data_settings->merchants && !empty($data['merchant_id']) && in_array($data['merchant_id'], $data_settings->merchants)) {
            $flag++;
        }

        if ($data_settings->users && !empty($data['user_id']) && in_array($data['user_id'], $data_settings->users)) {
            $flag++;
        }

        if ($data_settings->days_of_week && in_array($day_of_week, $data_settings->days_of_week)) {
            $flag++;
        }

        if ($data_settings->room_type && !empty($data['room_type']) && in_array($data['room_type'], $data_settings->room_type)) {
            $flag++;
        }
        
        if (sizeof($data_settings->bind) > 0 && $flag >= sizeof($data_settings->bind)) {
            $discount =  $this->calculateDiscount($coupon, $data);
        } elseif ($flag > sizeof($data_settings->bind)) {
            $discount =  $this->calculateDiscount($coupon, $data);
        } else {
            $discount = [
                'message'        => 'Mã giảm giá không thể áp dụng cho đơn đặt phòng này',
                'price_discount' => 0
            ];
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
        $price_discount = ($coupon->discount * $data['price_original'])/100;

        if ($price_discount > $coupon->max_discount) {
            $price_discount = $data['price_original'] - $coupon->max_discount;
        }

        $price_remain = $data['price_original'] -  $price_discount;

        $dataDiscount = [
            'message'        => "Mã giảm giá được áp dụng thành công",
            'price_discount' => $price_discount,
            'price_remain'   => $price_remain
        ];
        return $dataDiscount;
    }
}
