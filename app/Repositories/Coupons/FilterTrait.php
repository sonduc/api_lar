<?php
namespace App\Repositories\Coupons;

trait FilterTrait
{
    public function scopeQ($query, $q)
    {
        if ($q) {
            return $query->where('name', 'like', "%${q}%");
        }
        return $query;
    }

    /**
    * Scope Status
    * @author sonduc <ndson1998@gmail.com>
    *
    * @param $query
    * @param $q
    *
    * @return mixed
    */
    public function scopeStatus($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('coupons.status', $q);
        }

        return $query;
    }

    /**
    * Scope AllDay
    * @author sonduc <ndson1998@gmail.com>
    *
    * @param $query
    * @param $q
    *
    * @return mixed
    */
    public function scopeAllDay($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('coupons.all_day', $q);
        }

        return $query;
    }
}
