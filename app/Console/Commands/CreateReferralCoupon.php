<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Coupons\CouponRepositoryInterface;

use Carbon\Carbon;

class CreateReferralCoupon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referral:coupon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create coupon for valid referral user';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CouponRepositoryInterface $coupon)
    {
        parent::__construct();
        $this->coupon    = $coupon;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->coupon->createReferralCoupon();
    }
}
