<?php

namespace App\Repositories\_Customer;

use App\Repositories\BaseLogic;
use App\Repositories\Cities\CityRepository;
use App\Repositories\Cities\CityRepositoryInterface;
use App\Repositories\Coupons\CouponLogicTrait;
use App\Repositories\Coupons\CouponRepository;
use App\Repositories\Coupons\CouponRepositoryInterface;
use App\Repositories\Districts\DistrictRepository;
use App\Repositories\Districts\DistrictRepositoryInterface;
use App\Repositories\Rooms\RoomOptionalPriceRepository;
use App\Repositories\Rooms\RoomOptionalPriceRepositoryInterface;
use App\Repositories\Rooms\RoomRepository;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Rooms\RoomTranslateRepository;
use App\Repositories\Rooms\RoomTranslateRepositoryInterface;

class CouponLogic extends BaseLogic
{
    use CouponLogicTrait;

    protected $model;
    protected $room;
    protected $room_translate;
    protected $city;
    protected $district;
    protected $op;


    /**
     * CouponLogic constructor.
     *
     * @param CouponRepositoryInterface |CouponRepository                      $coupon
     * @param RoomTranslateRepositoryInterface|RoomTranslateRepository         $room_translate
     * @param RoomRepositoryInterface|RoomRepository                           $room
     * @param CityRepositoryInterface|CityRepository                           $city
     * @param DistrictRepositoryInterface|DistrictRepository                   $district
     * @param RoomOptionalPriceRepositoryInterface|RoomOptionalPriceRepository $op
     */
    public function __construct(
        CouponRepositoryInterface $coupon,
        RoomTranslateRepositoryInterface $room_translate,
        RoomRepositoryInterface $room,
        CityRepositoryInterface $city,
        DistrictRepositoryInterface $district,
        RoomOptionalPriceRepositoryInterface $op
    )
    {
        $this->model          = $coupon;
        $this->room           = $room;
        $this->room_translate = $room_translate;
        $this->city           = $city;
        $this->district       = $district;
        $this->op             = $op;
    }

}
