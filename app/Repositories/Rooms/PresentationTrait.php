<?php

namespace App\Repositories\Rooms;

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
    
    public function managerStatus()
    {
        switch ($this->is_manager) {
            case self::MANAGER_ACTIVE:
                return 'Hệ thống quản lý';
                break;
            case self::MANAGER_DEACTIVE:
                return 'Host quản lý';
                break;
            default:
                return 'Không xác định';
                break;
        }
    }
    
    public function rentStatus()
    {
        switch ($this->rent_type) {
            case self::TYPE_HOUR:
                return 'Theo giờ';
                break;
            
            case self::TYPE_DAY:
                return 'Theo ngày';
                break;
            case self::TYPE_ALL:
                return 'Theo ngày và giờ';
                break;
            default:
                return 'Không xác định';
                break;
        }
    }
    
    public function roomStatus()
    {
        return self::ROOM_STATUS[$this->status ?? self::NOT_APPROVED];
    }
    
    public function roomType()
    {
        return isset(self::ROOM_TYPE[$this->room_type]) ? self::ROOM_TYPE[$this->room_type] : 'Không xác định';
    }
    
    public function roomMedia()
    {
        return isset(self::IMAGE_TYPE[$this->type]) ? self::IMAGE_TYPE[$this->type] : 'Không xác định';
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
