<?php

namespace App\Repositories\Users;

trait FilterTrait
{
    public function scopeQ($query, $q)
    {
        if ($q) {
            return $query->where('name', 'like', "%${q}%");
        }
        return $query;
    }
    
    public function scopeType($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('users.type', $q);
        }
        
        return $query;
    }
    
    
}
