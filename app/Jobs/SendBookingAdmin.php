<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 03/01/2019
 * Time: 09:11
 */

namespace App\Jobs;

use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;


class SendBookingAdmin  implements ShouldQueue
{
    public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;

    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $template = 'email.sendBookingAdmin';
        $email    = env('MAIL_ADMIN');
        try {
            Mail::send($template, ['new_booking' => $this->booking], function ($message) use ($email) {
                $message->from(env('MAIL_TEST'));
                $message->to($email)->subject('Thông tin booking mới');
            });
        } catch (\Exception $e) {
            logs('emails', 'Email cho admin gửi thất bại ' . $email);
            throw $e;
        }
    }
}
