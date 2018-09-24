<?php

namespace App\Repositories\Logs;

trait FilterTrait
{
    public function scopeQ($query, $q)
    {
        if ($q) {
            return $query->where('description', 'like', "%${q}%");
        }
        return $query;
    }

    public function scopeName($query, $q)
    {
        if ($q) {
            return $query->where('log_name', $q);
        }
        return $query;
    }

    public function scopeDateStart($query, $q)
    {
        if ($q) {
            return $query->where('created_at', '>=', $q);
        }
        return $query;
    }

    public function scopeDateEnd($query, $q)
    {
        if ($q) {
            return $query->where('created_at', '<=', $q);
        }
        return $query;
    }
}
