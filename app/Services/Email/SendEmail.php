<?php

namespace App\Services\Email;

use App\Jobs\Traits\DispatchesJobs;
use App\Repositories\Rooms\RoomRepository;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Users\UserRepository;
use App\Repositories\Users\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class SendEmail
{
    use DispatchesJobs;
    public $room;
    public $user;


    public function __construct(RoomRepositoryInterface $room, UserRepositoryInterface $user)
    {
        $this->room = $room;
        $this->user = $user;
    }

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
            Mail::send($template, ['data' => $info], function ($message) use ($email) {
                $message->from(env('MAIL_TEST'));
                $message->to($email)->subject('Xác thực tài khoản');
            });
        } catch (\Exception $e) {
            logs('emails', 'Email xác nhận gửi thất bại ' . $email);
            throw $e;
        }
    }


    public function sendBookingAdmin($booking, $template = 'email.sendBookingAdmin')
    {
        $email = 'taikhoan149do@gmail.com';
        try {
            Mail::send($template, ['new_booking' => $booking->data], function ($message) use ($email) {
                $message->from(env('MAIL_TEST'));
                $message->to($email)->subject('Thông tin booking mới');
            });
        } catch (\Exception $e) {
            logs('emails', 'Email cho admin gửi thất bại ' . $email);
            throw $e;
        }
    }

    /**
     * Gửi email khi có yêu cầu tạo booking
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param        $booking
     * @param string $template
     *
     * @throws \Exception
     */
    public function sendBookingCustomer($booking, $template = 'email.sendBookingCustomer')
    {
        // lâý thông tin về phòng và merchant
        $merchant          = $this->user->getById($booking->data->merchant_id);
        $room_name         = $this->room->getRoom($booking->data->room_id);
        $booking->merchant = $merchant;
        $booking->room     = $room_name;

        // Tính tổng số giờ thuê phòng
        $booking->data->hours = $this->calculateHours($booking->data->checkin, $booking->data->checkout);
        $email                = $booking->data->email;

        try {
            Mail::send($template, ['new_booking' => $booking], function ($message) use ($email) {
                $message->from(env('MAIL_TEST'));
                $message->to($email)->subject('Yêu cầu đặt phòng của bạn đang chờ xử lý');
            });
        } catch (\Exception $e) {
            logs('emails', 'Email khách hàng gửi thất bại ' . $email);
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
            Mail::send($template, ['data' => $data], function ($message) use ($email) {
                $message->from(env('MAIL_TEST'));
                $message->to($email)->subject('Thông báo ngày thuê phòng');
            });
        } catch (\Exception $e) {
            logs('emails', 'Email gửi thất bại '.$email);
            throw $e;
        }
    }

    /**
     * Gửi email cho customer , thông báo cho customer biết booking này có đước xác nhận hay không
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param        $booking
     * @param string $template
     *
     * @throws \Exception
     */
    public function sendBookingConfirmedCustomer($booking, $template = 'email.sendBookingCustomer')
    {
        // lâý thông tin về phòng và merchant
        $merchant          = $this->user->getById($booking->data->merchant_id);
        $room_name         = $this->room->getRoom($booking->data->room_id);
        $booking->merchant = $merchant;
        $booking->room     = $room_name;

        // Tính tổng số giờ thuê phòng
        $booking->data->hours = $this->calculateHours($booking->data->checkin, $booking->data->checkout);
        $email                = $booking->data['email'];
        try {
            Mail::send($template, ['new_booking' => $booking], function ($message) use ($email) {
                $message->from(env('MAIL_TEST'));
                $message->to($email)->subject('Yêu cầu đặt phòng của bạn đã được chủ nhà xác nhận');
            });
        } catch (\Exception $e) {
            dd($e);
            logs('emails', 'Email xác nhận khách hàng gửi thất bại ' . $email);
            throw $e;
        }
    }


    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $booking
     * @param string $template
     * @throws \Exception
     */
    public function sendBookingHost($booking, $template = 'email.sendBookingHost')
    {
        // lâý thông tin về phòng và merchant
        $merchant          = $this->user->getById($booking->data->merchant_id);
        $room_name         = $this->room->getRoom($booking->data->room_id);
        $booking->merchant = $merchant;
        $booking->room     = $room_name;

        $timeSubmit                = Carbon::now()->timestamp;
        $booking->data->timeSubmit = base64_encode($timeSubmit);

        if (!empty($booking->merchant->email)) {
            $email = $booking->merchant->email;
        }

        // Tính tổng số giờ thuê phòng
        $booking->data->hours = $this->calculateHours($booking->data->checkin, $booking->data->checkout);

        try {
            Mail::send($template, ['new_booking' => $booking], function ($message) use ($email) {
                $message->from(env('MAIL_TEST'));
                $message->to($email)->subject('Thông tin booking mới');
            });
        } catch (\Exception $e) {
            logs('emails', 'Email gửi thất bại ' . $email);
            throw $e;
        }
    }

    private function calculateHours($checkin, $checkout)
    {
        $checkin_Carbon              = Carbon::parse($checkin);
        $checkout_Carbon             = Carbon::parse($checkout);
        return $checkout_Carbon->copy()->ceilHours()->diffInHours($checkin_Carbon);
    }

    /**
     * Email mời khách reviews sau checkout
     *
     */
    public function mailReviewsBooking($dataBooking, $template = 'email.reviews')
    {
        $email      = $dataBooking->name->email;
        $data       = $dataBooking->name;
        $dataTime   = $dataBooking->data;
        try {
            Mail::send($template, ['data' => $data,'dataTime' => $dataTime], function ($message) use ($email) {
                $message->from(env('MAIL_TEST'));
                $message->to($email)->subject('Đánh giá căn hộ');
            });
        } catch (\Exception $e) {
            logs('emails', 'Email gửi thất bại '.$email);
            throw $e;
        }
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $user
     * @param string $template
     * @throws \Exception
     */

    public function sendMailResetPassword($user, $template = 'email.reset_password')
    {

          $timeSubmit                = Carbon::now()->timestamp;
          $user->data->timeSubmit    = base64_encode($timeSubmit);
          $email                     = $user->data->email;
        try {
            Mail::send($template, ['user' => $user], function ($message) use ($email) {
                $message->from(env('MAIL_USERNAME'));
                $message->to($email)->subject('Khôi phục mật khẩu !!!');
            });
        } catch (\Exception $e) {
            logs('emails', 'Email gửi thất bại ' . $email);
            throw $e;
        }

    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $user
     * @param string $template
     * @throws \Exception
     */
    public function setPassword($user, $template = 'email.set_password')
    {
        $timeSubmit                = Carbon::now()->timestamp;
        $user->data->timeSubmit    = base64_encode($timeSubmit);
        $email                     = $user->data->email;
        try {
            Mail::send($template, ['user' => $user], function ($message) use ($email) {
                $message->from(env('MAIL_USERNAME'));
                $message->to($email)->subject('Chỉ cần xác nhận để đăng ký  !!!');
            });
        } catch (\Exception $e) {
            logs('emails', 'Email gửi thất bại ' . $email);
            throw $e;
        }

    }


}
