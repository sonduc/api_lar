<?php
namespace App\Repositories\Logs;

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

    /**
     * Lấy thuộc tính của log
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return mixed|null
     */
    public function properties()
    {
        return $this->properties ? json_decode($this->properties) : null;
    }

    /**
     * Lấy tên log tiêng việt
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return mixed
     */
    public function logVi()
    {
        return array_key_exists($this->log_name, self::LOG_NAME) ? self::LOG_NAME[$this->log_name] : 'Không xác định';
    }
}
