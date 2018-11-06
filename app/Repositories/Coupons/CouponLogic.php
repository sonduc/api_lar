<?php 

namespace App\Repositories\Coupons;

use App\Repositories\BaseLogic;


class CouponLogic extends BaseLogic
{
	protected $model;
	protected $room;
	protected $city;
	protected $district;

	function __construct(
		CouponRepositoryInterface $coupon,
		RoomRepositoryInterface $room,
		CityRepositoryInterface $city,
		DistrictRepositoryInterface $district,)
	{
		$this->model = $coupon;
		$this->room = $room;
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
		// $data['settings'] = json_encode($data['settings']);
		return $data;
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
		$data_promotion = parent::update($id, $data);
		return $data_promotion;
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
        $data_promotion = parent::update($id, $data);
        return $data_promotion ;
    }

    public function getValueSetting($data){
    	dd($data);
    }
}