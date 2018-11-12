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

}