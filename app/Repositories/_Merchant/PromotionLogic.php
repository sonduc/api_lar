<?php

namespace App\Repositories\_Merchant;

use App\Repositories\BaseLogic;
use App\Repositories\Promotions\PromotionRepositoryInterface;
use App\Repositories\Coupons\CouponRepositoryInterface;
use App\Repositories\Rooms\RoomRepositoryInterface;

use Carbon\Carbon;

class PromotionLogic extends BaseLogic
{
    public function __construct(
        PromotionRepositoryInterface $promotion,
    	CouponRepositoryInterface $coupon,
    	RoomRepositoryInterface $room) {
        $this->model          = $promotion;
        $this->coupon 		  = $coupon;
        $this->room 		  = $room;
    }

    public function joinPromotion($data)
    {
    	$current  = Carbon::now();
    	$coupon = $this->coupon->getCouponByCode(strtoupper($data['coupon']));
    	$settings = json_decode($coupon->settings);
    	if(isset($data['promotion_id'])){
    		$promotion = $this->getById($data['promotion_id']);
	    	if($coupon->promotion_id != $data['promotion_id']){
	    		throw new \Exception('Mã khuyến mãi không nằm trong chương trình khuyến mại');
	    	}
	    	$diffInWeeks = $current->diffInWeeks($promotion->date_start);
	    	if($diffInWeeks < 1 ){
	    		throw new \Exception('Phải đăng ký chương trình khuyến mãi trước 1 tuần');
	    	}

	    	if(!empty($data['rooms']) || $data['rooms'] != NULL){
		    	$arrRoom = array_merge($settings->rooms,$data['rooms']);
		    	array_unique($arrRoom);
		    	$settings->rooms = $arrRoom;
	    	} else{
	    		array_push($settings->merchants,$data['promotion_id']);
	    	}
    	} else{
    		$diffInWeeks = $current->diffInWeeks($settings->date_start);
    		if($diffInWeeks < 1 ){
	    		throw new \Exception('Phải đăng ký chương trình khuyến mãi trước 1 tuần');
	    	}
    		if(!empty($data['rooms']) || $data['rooms'] != NULL){
		    	dd($settings->rooms);
		    	$arrRoom = array_merge($settings->rooms,$data['rooms']);
		    	array_unique($arrRoom);
		    	$settings->rooms = $arrRoom;
	    	} else {
                throw new \Exception('Không có phòng để tham gia chương trình khuyến mãi');
            }
    	}
    	$coupon->settings = json_encode($settings);
	    $this->coupon->update($coupon->id,$coupon->toArray());
	    $dataReturn = [
            'message'        => "Cập nhật thành công",
        ];
        return $dataReturn;
    }
}
