<?php

namespace App\Jobs;

use App\User;
use App\Repositories\Coupons\Coupon;
use Illuminate\Support\Facades\Mail;

class SendCouponReferralUser extends Job
{
    public $tries = 5;
    public $user;
    public $coupon;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Coupon $coupon)
    {
        $this->user     = $user;
        $this->coupon   = $coupon;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $email = (new SendEmail());
        $dataUser = $this->user;
        $dataCoupon = $this->coupon;
        // $email->userRegisterReferralCoupon($this->user, $this->coupon);
        $email      = $dataUser->email;
        $setting    = json_decode($dataCoupon->settings);
        // dd($setting);
        $date_start = $setting->date_start;
        $date_end   = $setting->date_end;
        $min_price  = $setting->min_price;
        $dataSetting = [
            "date_start" => $date_start,
            "date_end"   => $date_end,
            "min_price"  => $min_price
        ];
        try {
            Mail::send('email.user_referral_coupon.blade', ['data' => $dataUser,'coupon' => $dataCoupon,"setting" => $dataSetting], function ($message) use ($email) {
                $message->from(env('MAIL_TEST'));
                $message->to($email)->subject('Westay có một bất ngờ nhỏ dành cho bạn !');
            });
        } catch (\Exception $e) {
            logs('emails', 'Email gửi thất bại '.$email);
            throw $e;
        }
    }
}
