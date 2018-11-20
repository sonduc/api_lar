<?php

namespace App\Services\Email;

use App\Jobs\Traits\DispatchesJobs;
use Illuminate\Support\Facades\Mail;

class SendEmail
{
    use DispatchesJobs;

    /**
     * @param Email  $email
     * @param string $template
     *
     * @return bool
     * @throws \Exception
     */
    public function mailConfirm($data, $template = 'email.blank')
    {
        $email = $data->name['email'];
        $info  = $data->name;
        try {
            Mail::send($template,['data' => $info] ,function ($message) use ($email) {
                $message->from('ducchien0612@gmail.com');
                $message->to($email)->subject('Xác thực tài khoản !!!');
            });
        } catch (\Exception $e) {
            logs('emails', 'Email gửi thất bại '.$email );
            throw $e;
        }
    }

    /**
     * Email thông báo cho khách trước 48h
     *
     */
    public function mailNotificationBooking($data, $template = 'email.notification')
    {
        $email = $data->name->email;
        $data  = $data->name;
        try {
            Mail::send($template,['data' => $data] ,function ($message) use ($email) {
                $message->from('ndson1998@gmail.com');
                $message->to('ndson1998@gmail.com')->subject('Thông báo ngày thuê phòng !!!');
            });
        } catch (\Exception $e) {
            logs('emails', 'Email gửi thất bại '.$email );
            throw $e;
        }
    }

}
