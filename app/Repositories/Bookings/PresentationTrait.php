<?php
namespace App\Repositories\Bookings;
use App\User;
trait PresentationTrait
{
    /**
     * Check specific role has access a resource
     * @param  array   $permissions
     * @return boolean
     */
    public function hasAccess(array $permissions) : bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check a specific permission that belongs to this role
     * @param  string  $permission
     * @return boolean
     */
    private function hasPermission(string $permission) : bool
    {
        return $this->permissions[$permission] ?? false;
    }

    public function getSex()
    {
        return array_key_exists($this->sex, User::SEX) ? User::SEX[$this->sex] : 'Không xác định';
    }

    public function getPriceRange()
    {
        return array_key_exists($this->price_range, BookingConstant::PRICE_RANGE)
            ? BookingConstant::PRICE_RANGE[$this->price_range]
            : 'Không xác định';
    }

    public function getBookingType()
    {
        return array_key_exists($this->booking_type, BookingConstant::BOOKING_TYPE)
            ? BookingConstant::BOOKING_TYPE[$this->booking_type]
            : 'Không xác định';
    }

    public function getType()
    {
        return array_key_exists($this->type, BookingConstant::TYPE)
            ? BookingConstant::TYPE[$this->type]
            : 'Không xác định';
    }

    public function getBookingSource()
    {
        return array_key_exists($this->source, BookingConstant::BOOKING_SOURCE)
            ? BookingConstant::BOOKING_SOURCE[$this->source]
            : 'Không xác định';
    }

    public function getPaymentStatus()
    {
        return array_key_exists($this->payment_status, BookingConstant::PAYMENT_STATUS)
            ? BookingConstant::PAYMENT_STATUS[$this->payment_status]
            : 'Không xác định';
    }

    public function getBookingStatus()
    {
        return array_key_exists($this->status, BookingConstant::BOOKING_STATUS)
            ? BookingConstant::BOOKING_STATUS[$this->status]
            : 'Không xác định';
    }
}
