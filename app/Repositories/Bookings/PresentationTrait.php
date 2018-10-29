<?php

namespace App\Repositories\Bookings;

use App\Helpers\ErrorCore;
use App\User;

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
