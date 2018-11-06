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
    	$data_coupon = $data->all();
    	// dd($data_coupon);
    	$arrRoom = [];
    	foreach ($data_coupon as $key => $value) {
    		// dd($value);
    		$settings = json_decode($value->settings);
    		$list_room_id = $settings->rooms;
    		// dd($list_room_id);
    		foreach ($list_room_id as $k => $val) {
    			$arrName = $this->room_translate->getRoomByListId($val);
    			$a = [
    				"id" => $arrName->room_id,
    				"name" => $arrName->name,
    			];
    			// $arrRoom[$arrName->room_id] = $arrName->name;
    		
    			array_push($arrRoom,$arrName->room_id);
    			
    		}
    		// dd($arrRoom);
    		// foreach ($list_room_id as $k => $val) {
    		// 	$arrName =  $this->room_translate->getRoomByListId($val);
    		// 	array_push($arrRoom,$arrName->room_id,$arrName->name)
    		// }
    		// $rooms = $this->room->getRoomByListId($list_room_id);
    	}
    	$b = array_unique($arrRoom);
    	dd($b);
    }
}