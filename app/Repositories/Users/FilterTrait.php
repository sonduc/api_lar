<?php

namespace App\Repositories\Users;

use App\User;

trait FilterTrait
{
    public function scopeQ($query, $q)
    {
        if ($q) {
            return $query->where('users.name', 'like', "%${q}%")->orWhere('users.email', 'like', "%{$q}%");
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

    public function scopeCityId($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('users.city_id', $q);
        }

        return $query;
    }

    public function scopeDistrictId($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('users.district_id', $q);
        }

        return $query;
    }

    public function scopeIsOwner($query, $q)
    {
        if (is_numeric($q)) {
            switch ($q) {
                case User::IS_OWNER:
                    $query->where('users.owner', User::IS_OWNER);
                    break;
                case User::NOT_OWNER:
                    $query->where('users.owner', User::NOT_OWNER);
                default:
                    $query;
                    break;
            }
        }

        return $query;
    }

}
