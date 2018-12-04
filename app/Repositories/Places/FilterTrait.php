<?php
namespace App\Repositories\Places;

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
            $query->where('places.status', $q);
        }

        return $query;
    }
}
