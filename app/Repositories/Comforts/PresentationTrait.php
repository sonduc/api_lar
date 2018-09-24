<?php

namespace App\Repositories\Comforts;

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

    public function getRegion()
    {
        return self::REGION[$this->region_id ?? self::UNKNOWN_REGION];
    }

    public function getStatus()
    {
        return self::STATUS[$this->status ?? self::UNAVAILABLE];
    }

    public function getPriorityStatus()
    {
        return self::PRIORITIES[$this->priority ?? self::NO_PRIORITY];
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
