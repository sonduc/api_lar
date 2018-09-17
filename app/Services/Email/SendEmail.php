<?php

namespace App\Services\Email;

use App\Jobs\JobEmail;
use App\Jobs\Traits\DispatchesJobs;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class SendEmail
{
    use DispatchesJobs;

    /**
     * @param Email $email
     * @param string $template
     * @return bool
     * @throws \Exception
     */
    public static function send($email, $template = 'email.blank')
    {
        try {
            $i = 0;
            do {
                Mail::send($template, array('token' => 'test phat cho vui'), function ($message) use ($email) {
                    $message->from('ducchien0612@gmail.com');
                    $message->to($email, 'Visitor')->subject('Khôi phục mật khẩu!');
                });
                $i++;
            } while ($i < 1);

        } catch (\Exception $e) {
            throw $e;
        }

//        return true;
    }

    public function handleEmailType($email)
    {
        $job = (new JobEmail($email['email']))
//                ->onQueue('emails')
                ->delay(5);
        dispatch($job);
    }
}
