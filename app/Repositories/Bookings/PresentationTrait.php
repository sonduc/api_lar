<?php

namespace App\Repositories\Bookings;

use App\Helpers\ErrorCore;
use App\User;
use Carbon\Carbon;

trait PresentationTrait
{
    /**
     * Check specific role has access a resource
     *
     * @param  array $permissions
     *
     * @return boolean
     */
    public function hasAccess(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function getSex()
    {
        return array_key_exists($this->sex, User::SEX) ? User::SEX[$this->sex] : trans2(ErrorCore::UNDEFINED);
    }

    public function getPriceRange()
    {
        return array_key_exists($this->price_range, BookingConstant::PRICE_RANGE)
            ? BookingConstant::PRICE_RANGE[$this->price_range]
            : trans2(ErrorCore::UNDEFINED);
    }

    public function getBookingType()
    {
        return array_key_exists($this->booking_type, BookingConstant::BOOKING_TYPE)
            ? BookingConstant::BOOKING_TYPE[$this->booking_type]
            : trans2(ErrorCore::UNDEFINED);
    }

    public function getType()
    {
        return array_key_exists($this->type, BookingConstant::TYPE)
            ? BookingConstant::TYPE[$this->type]
            : trans2(ErrorCore::UNDEFINED);
    }

    public function getBookingSource()
    {
        return array_key_exists($this->source, BookingConstant::BOOKING_SOURCE)
            ? BookingConstant::BOOKING_SOURCE[$this->source]
            : trans2(ErrorCore::UNDEFINED);
    }

    public function getPaymentStatus()
    {
        return array_key_exists($this->payment_status, BookingConstant::PAYMENT_STATUS)
            ? BookingConstant::PAYMENT_STATUS[$this->payment_status]
            : trans2(ErrorCore::UNDEFINED);
    }

    public function getBookingStatus()
    {
        return array_key_exists($this->status, BookingConstant::BOOKING_STATUS)
            ? BookingConstant::BOOKING_STATUS[$this->status]
            : trans2(ErrorCore::UNDEFINED);
    }

    public function getPaymentMethod()
    {
        return array_key_exists($this->payment_method, BookingConstant::PAYMENT_METHOD)
            ? BookingConstant::PAYMENT_METHOD[$this->payment_method]
            : trans2(ErrorCore::UNDEFINED);
    }

    public function getBookingCancelReasonList()
    {
        return array_key_exists($this->code, BookingCancel::getBookingCancel())
            ? BookingCancel::getBookingCancel()[$this->code]
            : trans2(ErrorCore::UNDEFINED);
    }

    public function getEmailReminder()
    {
        return array_key_exists($this->email_reminder, BookingConstant::EMAIL_REMINDER)
            ? BookingConstant::EMAIL_REMINDER[$this->email_reminder]
            : trans2(ErrorCore::UNDEFINED);
    }

    public function getEmailReviews()
    {
        return array_key_exists($this->email_reviews, BookingConstant::EMAIL_REVIEWS)
            ? BookingConstant::EMAIL_REVIEWS[$this->email_reviews]
            : trans2(ErrorCore::UNDEFINED);
    }


    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $bookinng
     * @return string
     */
    public function getTotalRefund($bookinng)
    {
        $booking_settings           = json_decode($bookinng->settings);
        if (empty($booking_settings) || $booking_settings->no_booking_cancel == 1 )
        {
           return 'Bạn sẽ không được hoàn lại khoản tiền nào nếu hủy booking này';
        }

        // thời gian check_in booking
        $checkin = $bookinng['checkin'];
       // Thời gian cách ngày checkin cho phép hoàn lại tiền.
        $time_refund = $booking_settings->refund[0]->days* 24 *3600;

        $time_free_booking_caccel = Carbon::createFromTimestamp($checkin - $time_refund);
        return 'Hủy không tốn phí trước'.' '. $time_free_booking_caccel. ' (giờ địa phương)';

    }

    /**
     * Check a specific permission that belongs to this role
     *
     * @param  string $permission
     *
     * @return boolean
     */
    private function hasPermission(string $permission): bool
    {
        return $this->permissions[$permission] ?? false;
    }
}
