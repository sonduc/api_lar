<?php 

namespace App\Repositories\Coupons;

use App\Repositories\BaseLogic;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Rooms\RoomTranslateRepositoryInterface;
use App\Repositories\Cities\CityRepositoryInterface;
use App\Repositories\Districts\DistrictRepositoryInterface;


class CouponLogic extends BaseLogic
{
	protected $model;
	protected $room;
	protected $room_translate;
	protected $city;
	protected $district;

	function __construct(
		CouponRepositoryInterface $coupon,
		RoomTranslateRepositoryInterface $room_translate,
		RoomRepositoryInterface $room,
		CityRepositoryInterface $city,
		DistrictRepositoryInterface $district)
	{
		$this->model = $coupon;
		$this->room = $room;
		$this->room_translate = $room_translate;
		$this->city = $city;
		$this->district = $district;
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
		$data['code'] = strtoupper($data['code']);
		$data['settings'] = json_encode($data['settings']);
		$data_coupon = parent::store($data);
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
		if($coupon->used > 0){
			throw new \Exception('Không có quyền sửa đổi mục này');
		};
		
		$data['settings'] = json_encode($data['settings']);
		$data_coupon = parent::update($id, $data);
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
	public function transformCoupon($data)
	{
		$settings = $data['settings'];
		
		$arrRoom = $this->room_translate->getRoomByListId($settings['rooms']);
		$arrCity = $this->city->getCityByListId($settings['cities']);
		$arrDistrict = $this->district->getDistrictByListId($settings['districts']);
		$arrDay = $settings['days'];

		$arrayTransformSetting = [
			'rooms' => $arrRoom,
			'cities' => $arrCity,
			'districts' => $arrDistrict,
			'days' => $arrDay,
		];
		$objectSetting = json_encode($arrayTransformSetting);
		$data['settings'] = $arrayTransformSetting;
		return $data;
	}

	/**
	 * Cập nhật số lần dùng cho 1 coupon
	 * @author sonduc <ndson1998@gmail.com>
	 */
	public function updateUsable($code)
	{
		$coupon = $this->getCouponByCode(strtoupper($code));
		$id = $coupon['id'];
		$coupon = (array) json_decode($coupon);
		if($coupon['usable'] > 0){
			$coupon['usable'] = $coupon['usable']-1;
			$coupon['used'] = $coupon['used']+1;
			$data_coupon = parent::update($id, $coupon);
			return $data_coupon;
		}else{
			throw new \Exception('Mã khuyến mãi đã hết số lần sử dụng');
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
    public function checkSettingDiscount($coupon,$data)
    {
        $data_settings = json_decode($coupon->settings);
        $data_status = $coupon->status;
        if($data_status == 1){
            if($data['room_id'] != null){
                $dataRooms = $data_settings->rooms;
                foreach ($dataRooms as $key => $value) {
                    if($data['room_id'] == $value->id){
                        return $this->caculateDiscount($coupon,$data);
                    }
                }
            }
            if($data['city_id'] != null){
                $dataCities = $data_settings->cities;
                foreach ($dataCities as $key => $value) {
                    if($data['city_id'] == $value->id){
                        return $this->caculateDiscount($coupon,$data);
                    }
                }
            }
            if($data['district_id'] != null){
                $dataDistricts = $data_settings->districts;
                foreach ($dataDistricts as $key => $value) {
                    if($data['district_id'] == $value->id){
                        return $this->caculateDiscount($coupon,$data);
                    }
                }
            }
            if($data['day'] != null){
                $dataDays = $data_settings->days;
                foreach ($dataDays as $key => $value) {
                    if($data['day'] == $value){
                        return $this->caculateDiscount($coupon,$data);
                    }
                }
            }
            throw new \Exception('Mã giảm giá không thể áp dụng cho đơn đặt phòng này');
        }else{
            throw new \Exception('Mã khuyến mãi không hợp lệ hoặc đã hết hạn');
        }
    }

    /**
     * Tính khuyến mãi của 1 booking dựa theo coupon 
     *
     * @author sonduc <ndson1998@gmail.com>
     */
    public function caculateDiscount($coupon,$data)
    {
        $price_discount = ($coupon->discount * $data['price_original'])/100;

        if ($price_discount > $coupon->max_discount){
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