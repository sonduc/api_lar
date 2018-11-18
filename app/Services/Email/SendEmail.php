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
        $email = $booking->data->admin;
        dd($email);
        try {
            Mail::send($template,['new_booking' => $booking->data] ,function ($message) use ($email) {
                $message->from('ducchien0612@gmail.com');
                $message->to($email)->subject('Thông tin booking mới');
            });
        } catch (\Exception $e) {
            logs('emails', 'Email gửi thất bại '.$email );
            throw $e;
        }
    }

    /**
     * Gửi email khi có yêu cầu tạo booking
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $booking
     * @param string $template
     * @throws \Exception
     */
    public function sendBookingCustomer($booking, $template = 'email.sendBookingCustomer')
    {
        $checkin                =  Carbon::parse($booking->data->checkin);
        $checkout               =  Carbon::parse($booking->data->checkout);
        $hours                  = $checkout->copy()->ceilHours()->diffInHours($checkin);
        $booking->data->hours   = $hours;
        $email                  = $booking->data->email;
        try {
            Mail::send($template,['new_booking' => $booking] ,function ($message) use ($email) {
                $message->from('ducchien0612@gmail.com');
                $message->to($email)->subject('Yêu cầu đặt phòng của bạn đang chờ xử lý');
            });
        } catch (\Exception $e) {
            dd($e);
            logs('emails', 'Email gửi thất bại '.$email );
            throw $e;
        }
    }


    /**
     * Gửi email cho customer , thông báo cho customer biết booking này có đước xác nhận hay không
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $booking
     * @param string $template
     * @throws \Exception
     */
    public function sendBookingConfirmCustomer($booking, $template = 'email.sendBookingCustomer')
    {
        $checkin                =  Carbon::parse($booking->data->checkin);
        $checkout               =  Carbon::parse($booking->data->checkout);
        $hours                  = $checkout->copy()->ceilHours()->diffInHours($checkin);
        $booking->data->hours   = $hours;
        $email                  = $booking->data['email'];
        try {
            Mail::send($template,['new_booking' => $booking] ,function ($message) use ($email) {
                $message->from('ducchien0612@gmail.com');
                $message->to($email)->subject('Yêu cầu đặt phòng của bạn đã được chủ nhà xác nhận');
            });
        } catch (\Exception $e) {
            dd($e);
            logs('emails', 'Email gửi thất bại '.$email );
            throw $e;
        }
    }


    public function sendBookingHost($booking, $template = 'email.sendBookingHost')
    {
        $timeSubmit                = Carbon::now()->timestamp;
        $timeSubmit                = base64_encode($timeSubmit);
        $booking->data->timeSubmit = $timeSubmit;
        if (!empty($booking->merchant->email))
        {
            $email = $booking->merchant->email;
        }

        $checkin                =  Carbon::parse($booking->data->checkin);
        $checkout               =  Carbon::parse($booking->data->checkout);
        $hours                  = $checkout->copy()->ceilHours()->diffInHours($checkin);
        $booking->data->hours   = $hours;

        try {
            Mail::send($template,['new_booking' => $booking] ,function ($message) use ($email) {
                $message->from('ducchien0612@gmail.com');
                $message->to($email)->subject('Thông tin booking mới !!!');
            });
        } catch (\Exception $e) {
            dd('sdfsdfsd');
            logs('emails', 'Email gửi thất bại '.$email );
            throw $e;
        }
    }





}
