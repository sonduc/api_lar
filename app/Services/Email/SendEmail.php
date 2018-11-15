<?php

namespace App\Services\Email;

use App\Jobs\Traits\DispatchesJobs;
use Carbon\Carbon;
use function Couchbase\defaultDecoder;
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


    public function sendBookingAdmin($booking, $template = 'email.sendBookingAdmin')
    {
        $email = $booking->data['admin'];
        try {
            Mail::send($template,['new_booking' => $booking->data] ,function ($message) use ($email) {
                $message->from('ducchien0612@gmail.com');
                $message->to($email)->subject('Xác thực tài khoản !!!');
            });
        } catch (\Exception $e) {
            logs('emails', 'Email gửi thất bại '.$email );
            throw $e;
        }
    }


    public function sendBookingCustomer($booking, $template = 'email.sendBookingCustomer')
    {
        $email = $booking->data['email'];
        try {
            Mail::send($template,['new_booking' => $booking->data] ,function ($message) use ($email) {
                $message->from('ducchien0612@gmail.com');
                $message->to($email)->subject('Xác thực tài khoản !!!');
            });
        } catch (\Exception $e) {
            logs('emails', 'Email gửi thất bại '.$email );
            throw $e;
        }
    }


    public function sendBookingHost($booking, $template = 'email.sendBookingHost')
    {
        $email = $booking->merchant->email;
        $data_host              = $booking->data;
        $checkin                =  Carbon::parse($booking->data->checkin);
        $checkout               =  Carbon::parse($booking->data->checkout);
        $hours                  = $checkout->copy()->ceilHours()->diffInHours($checkin);
        $data_host['hours']     = $hours;
        $data_host['room_name'] = $booking->room_name->name;
        try {
            Mail::send($template,['new_booking' => $data_host] ,function ($message) use ($email) {
                $message->from('ducchien0612@gmail.com');
                $message->to($email)->subject('Xác thực tài khoản !!!');
            });
        } catch (\Exception $e) {
            logs('emails', 'Email gửi thất bại '.$email );
            throw $e;
        }
    }





}
