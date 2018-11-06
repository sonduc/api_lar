<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;
use App\Repositories\Coupons\Coupon;
use App\Repositories\Rooms\RoomRepositoryInterface;

class CouponTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        "room_setting"
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
            'settings'          =>  json_decode($coupon->settings),
            'promotion_id'      =>  $coupon->promotion_id,

        ];
    }

    public function includeRoomSetting(Coupon $coupon)
    {
        if (is_null($coupon)) {
            return $this->null();
        }
        $settings = collect(json_decode($coupon->settings));
        $rooms = $settings->get('rooms');
        dd($rooms);

    }

}
