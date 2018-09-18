<?php

namespace App\Services\Email;

use App\Jobs\JobEmail;
use App\Jobs\Traits\DispatchesJobs;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Events\BookingEvent;

class SendEmail
{
    use DispatchesJobs;

    /**
     * @param Email $email
     * @param string $template
     * @return bool
     * @throws \Exception
     */
    public static function send($data, $template = 'email.blank')
    {
        try {
            $i = 0;
            do {
                Mail::send($template, array('data' => $data), function ($message) use ($data) {
                    $message->from('ducchien0612@gmail.com');
                    $message->to($data['email'], 'Visitor')->subject('Khôi phục mật khẩu!');
                });
                $i++;
            } while ($i < 1);

        } catch (\Exception $e) {
            throw $e;
        }

//        return true;
    }

    public function handle(BookingEvent $event)
    {
       $job = (new JobEmail($event->data))
              ->onQueue('email')
               ->delay(5);
       dispatch($job);
    }
}
