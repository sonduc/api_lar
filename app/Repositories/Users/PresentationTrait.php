<?php

namespace App\Repositories\Users;

trait PresentationTrait
{
    /**
     * [hasAccess description]
     *
     * @param  array $permissions
     *
     * @return boolean
     */
    public function hasAccess(array $permissions): bool
    {
        foreach ($this->roles as $role) {
            if ($role->hasAccess(['admin.super-admin']) || $role->hasAccess($permissions)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * [inRole description]
     *
     * @param  string $slug
     *
     * @return boolean
     */
    public function inRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->count() == 1;
    }
    
    /**
     * [isSuperAdmin description]
     * @return boolean
     */
    public function isSuperAdmin()
    {
        return $this->hasAccess(['admin.super-admin']);
    }
    
    public function getStatus()
    {
        switch ($this->status) {
            case self::ENABLE:
                return 'Kích hoạt';
                break;
            case self::DISABLE:
                return 'Khóa';
            
            default:
                return 'Không xác định';
                break;
        }
    }
    
    public function getGender()
    {
        switch ($this->gender) {
            case self::MALE:
                return 'Nam';
                break;
            case self::FEMALE:
                return 'Nữ';
                break;
            case self::OTHER:
                return 'Khác';
                break;
            case self::NONE:
                return 'Không xác định';
                break;
            default:
                return 'Không xác định';
                break;
        }
    }
    
    public function getLevelStatus()
    {
        switch ($this->level) {
            case self::DIAMOND:
                return 'Kim cương';
                break;
            case self::PLATINUM:
                return 'Bạch kim';
                break;
            case self::GOLD:
                return 'Vàng';
                break;
            case self::SILVER:
                return 'Bạc';
                break;
            case self::BROZE:
                return 'Đồng';
                break;
            default:
                return 'Không xác định';
                break;
        }
    }
    
    public function getVipStatus()
    {
        switch ($this->vip) {
            case self::VIP_ACTIVE:
                return 'Tài khoản VIP';
                break;
            case self::VIP_DEACTIVE:
                return 'Tài khoản thường';
                break;
            default:
                return 'Không xác định';
                break;
        }
    }
    
    /**
     * lấy tài khoản owner
     * đối với tài khoản con -> lấy ra tài khoản cha
     * @date   2018-08-13
     * @return [type]     [description]
     */
    public function owner()
    {
        return $this->parent ?? $this;
    }
    
    public function getAccountType()
    {
        return self::TYPE_ACCOUNT[$this->type ?? 0];
    }
}
