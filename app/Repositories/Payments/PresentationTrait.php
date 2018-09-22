<?php

namespace App\Repositories\Payments;

use App\Repositories\Bookings\BookingConstant;

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
    
    /**
     * Lấy trạng thái của payment_histories
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return string
     */
    public function paymentStatus()
    {
        return array_key_exists($this->status, BookingConstant::PAYMENT_HISTORY_STATUS)
            ? BookingConstant::PAYMENT_HISTORY_STATUS[$this->status]
            : 'Không xác định';
    }
}
