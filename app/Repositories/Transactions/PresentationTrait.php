<?php
namespace App\Repositories\Transactions;

use App\Helpers\ErrorCore;
use App\Repositories\TransactionTypes\TransactionType;

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

    public function getTransactionType()
    {
        return array_key_exists($this->type, TransactionType::TYPE)
            ? TransactionType::TYPE[$this->type]
            : trans2(ErrorCore::UNDEFINED);
    }
    
    public function getTransactionStatus()
    {
        return array_key_exists($this->status, Transaction::STATUS)
            ? Transaction::STATUS[$this->status]
            : trans2(ErrorCore::UNDEFINED);
    }
}
