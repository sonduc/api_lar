<?php
namespace App\Listeners\CheckCoupon;

use App\Events\Check_Usable_Coupon_Event;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\Coupons\CouponLogic;

class UsableCouponListener implements ShouldQueue
{
    protected $coupon;

    public function __construct(CouponLogic $coupon)
    {
        $this->coupon = $coupon;
    }

    public function handle(Check_Usable_Coupon_Event $event)
    {
        $this->coupon->updateUsable($event->name);
    }
}
