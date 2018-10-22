<?php

namespace App\Repositories\Categories;

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
     * Scope Hot
     * @author ducchien062 <ducchien0612@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeHot($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('categories.hot', $q);
        }

        return $query;
    }


    /**
     * Scope Status
     * @author ducchien062 <ducchien0612@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeStatus($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('categories.status', $q);
        }
        return $query;
    }

    /**
     * Scope New
     * @author ducchien062 <ducchien0612@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeNew($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('categories.new', $q);
        }
        return $query;
    }
}
