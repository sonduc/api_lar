<?php

namespace App\Repositories\Coupons;

use App\Repositories\BaseRepository;

class CouponRepository extends BaseRepository implements CouponRepositoryInterface
{
    /**
     * Coupon model.
     * @param Coupon $coupon
     */
    public function __construct(Coupon $coupon)
    {
        $this->model = $coupon;
    }


}
