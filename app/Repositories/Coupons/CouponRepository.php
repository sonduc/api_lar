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

    /**
     * Lấy dữ liệu theo mã code
     *
     * @author sonduc <ndson1998@gmail.com>
     */
    public function getCouponByCode($code)
    {
        $coupon = $this->model->where('code', $code)->with('Promotions')->first();
        return $coupon;
    }
}
