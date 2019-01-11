<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Promotions\PromotionRepositoryInterface;
use App\Repositories\Coupons\CouponRepositoryInterface;

use Carbon\Carbon;

class ValidateCoupon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupon:validate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change Coupon status everyday';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CouponRepositoryInterface $coupon, PromotionRepositoryInterface $promotion)
    {
        parent::__construct();
        $this->coupon       = $coupon;
        $this->promotion    = $promotion;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $promotions                 = $this->promotion->getAll();
        $current_day                = Carbon::now()->timestamp;
        $tomorrow                   = $current_day + 1;
        $the_day_after_tomorow      = $current_day + 86400;

        foreach ($promotions as $key => $promotion) {
            $date_start             = $promotion->date_start;
            $date_end               = $promotion->date_end;
            $date_start_timestamp   = Carbon::parse($date_start)->timestamp;
            $date_end_timestamp     = Carbon::parse($date_end)->timestamp;
            $coupons                = $promotion->with('coupons');

            if ($date_end_timestamp >= $current_day) {
                foreach ($coupons as $k => $coupon) {
                    $coupon->status = 0;
                    $coupon->save();
                }
                $promotion->status = 0;
                $promotion->save();
            } elseif ($date_start_timestamp >= $tomorrow && $date_start_timestamp <= $the_day_after_tomorow) {
                foreach ($coupons as $k => $coupon) {
                    $coupon->status = 1;
                    $coupon->save();
                }
                $promotion->status = 1;
                $promotion->save();
            }
        }

        $coupons = $this->coupon->getAllExpiredCoupon();

        foreach ($coupons as $key => $coupon) {
            $coupon_setting = json_decode($coupon->settings);
            $date_start     = Carbon::parse($coupon_setting->date_start);
            $date_end       = Carbon::parse($coupon_setting->date_end);

            if ($date_end >= $current_day) {
                $coupon->status = 0;
                $coupon->save();
            } elseif ($date_start >= $tomorrow && $date_start <= $the_day_after_tomorow) {
                $coupon->status = 1;
                $coupon->save();
            }
        }
    }
}
