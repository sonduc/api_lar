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
    public static function send($data, $template = 'email.blank')
    {
        $info = [
            'data' => $data,
        ];
        try {
            Mail::send($template, $info, function ($message) use ($data) {
                $message->from('support@westay.org');
                $message->to($data['email'])->subject('Khôi phục mật khẩu!');
            });
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
