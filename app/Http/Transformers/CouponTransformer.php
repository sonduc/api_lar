<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;
use App\Repositories\Coupons\Coupon;
use App\Repositories\Rooms\RoomRepositoryInterface;

use App\Helpers\ErrorCore;
use App\Http\Transformers\Traits\FilterTrait;
use League\Fractal\ParamBag;

class CouponTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'promotion'
    ];

    public function transform(Coupon $coupon = null)
    {
        if (is_null($coupon)) {
            return [];
        }
        
        return [
            'id'                =>  $coupon->id,
            'code'              =>  $coupon->code,
            'discount'          =>  $coupon->discount,
            'max_discount'      =>  $coupon->max_discount,
            'usable'            =>  $coupon->usable,
            'used'              =>  $coupon->used,
            'status'            =>  $coupon->status,
            'status_txt'        =>  $coupon->getCouponStatus(),
            'all_day'           =>  $coupon->all_day,
            'all_day_txt'       =>  $coupon->getCouponAllDay(),
            'settings'          =>  gettype($coupon->settings) === 'array' ? $coupon->settings : json_decode($coupon->settings),
            'promotion_id'      =>  $coupon->promotion_id,

        ];
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Coupon|null $coupon
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includePromotion(Coupon $coupon = null, ParamBag $params = null)
    {
        if (is_null($coupon)) {
            return $this->null();
        }

        return $this->item($coupon->promotion, new PromotionTransformer);
    }
}
