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


    public function mailInfo($data, $template = 'email.blank')
    {
        $allEmail= ['myoneemail@esomething.com', 'myother@esomething.com','myother2@esomething.com'];
        $allEmail = $data->name['email'];
        $info  = $data->name;
        try {
            foreach ($allEmail as $email)
            {
                Mail::send($template,['data' => $info] ,function ($message) use ($email) {
                    $message->from('ducchien0612@gmail.com');
                    $message->to($email)->subject('Xác thực tài khoản !!!');
                });
            }
        } catch (\Exception $e) {
            logs('emails', 'Email gửi thất bại '.$email );
            throw $e;
        }
    }

}
