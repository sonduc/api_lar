<?php
namespace App\Repositories\Coupons;

use App\Helpers\ErrorCore;
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

    public function getCouponStatus()
    {
        return array_key_exists($this->status, Coupon::COUPON_STATUS)
            ? Coupon::COUPON_STATUS[$this->status]
            : trans2(ErrorCore::UNDEFINED);
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
}
