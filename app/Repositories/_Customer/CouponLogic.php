<?php

namespace App\Repositories\_Customer;

use App\Repositories\BaseLogic;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Rooms\RoomTranslateRepositoryInterface;
use App\Repositories\Cities\CityRepositoryInterface;
use App\Repositories\Districts\DistrictRepositoryInterface;
use App\Repositories\Coupons\CouponRepositoryInterface;

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
        DistrictRepositoryInterface $district
    ) {
        $this->model 			= $coupon;
        $this->room 			= $room;
        $this->room_translate 	= $room_translate;
        $this->city 			= $city;
        $this->district 		= $district;
    }

    /**
     * Chuyển đổi json setting sang mảng
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function transformCoupon($data)
    {
        $settings       = json_decode($data['settings']);
        // dd($settings->rooms);
        $arrRoom        = $this->room_translate->getRoomByListId($settings->rooms);
        $arrCity 		= $this->city->getCityByListId($settings->cities);
        $arrDistrict 	= $this->district->getDistrictByListId($settings->districts);
        $arrDay 		= $settings->days;

        $arrayTransformSetting = [
            'rooms' 	=> $arrRoom,
            'cities' 	=> $arrCity,
            'districts' => $arrDistrict,
            'days' 		=> $arrDay,
        ];
        $objectSetting 		= json_encode($arrayTransformSetting);
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
            $data_allDay = $coupon->all_day;
            $data_status    = $coupon->status;

            if ($data_status == 1) {
                $data_settings  = json_decode($coupon->settings);

                if ($data_allDay == 1) {
                    return $this->calculateDiscount($coupon, $data);
                }
                if ($data_allDay == 0) {
                    if ($data['room_id'] != null) {
                        $dataRooms = $data_settings->rooms;
                        foreach ($dataRooms as $key => $value) {
                            if ($data['room_id'] == $value->id) {
                                return $this->calculateDiscount($coupon, $data);
                            }
                        }
                    }
                    if ($data['city_id'] != null) {
                        $dataCities = $data_settings->cities;
                        foreach ($dataCities as $key => $value) {
                            if ($data['city_id'] == $value->id) {
                                return $this->calculateDiscount($coupon, $data);
                            }
                        }
                    }
                    if ($data['district_id'] != null) {
                        $dataDistricts = $data_settings->districts;
                        foreach ($dataDistricts as $key => $value) {
                            if ($data['district_id'] == $value->id) {
                                return $this->calculateDiscount($coupon, $data);
                            }
                        }
                    }
                    if ($data['day'] != null) {
                        $dataDays = $data_settings->days;
                        foreach ($dataDays as $key => $value) {
                            if ($data['day'] == $value->id) {
                                return $this->calculateDiscount($coupon, $data);
                            }
                        }
                    }
                    throw new \Exception('Mã giảm giá không thể áp dụng cho đơn đặt phòng này');
                }
            } else {
                throw new \Exception('Mã khuyến mãi không hợp lệ hoặc đã hết hạn');
            }
        } else {
            throw new \Exception('Mã khuyến mãi không tồn tại');
        }
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
