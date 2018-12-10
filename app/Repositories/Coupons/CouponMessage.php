<?php
/**
 * Created by PhpStorm.
 * User: Hariki
 * Date: 12/7/2018
 * Time: 15:29
 */

namespace App\Repositories\Coupons;


final class CouponMessage
{
    public const SUCCESS = 'coupon/index.success';

    public const ERR_INVALID_COUPON  = 'coupon/error.invalid';
    public const ERR_OUTDATED_COUPON = 'coupon/error.outdated';
    public const ERR_CANNOT_APPLY_COUPON = 'coupon/error.cannot_apply';
}