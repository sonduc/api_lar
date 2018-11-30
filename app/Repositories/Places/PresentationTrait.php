<?php
namespace App\Repositories\Places;

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

    public function getPlaceStatus()
    {
        return array_key_exists($this->status, Place::PLACE_STATUS)
            ? Place::PLACE_STATUS[$this->status]
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
