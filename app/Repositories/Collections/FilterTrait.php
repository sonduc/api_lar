<?php

namespace App\Repositories\Collections;
use App\Repositories\GlobalTrait;


trait FilterTrait
{
    use GlobalTrait;
    public function scopeQ($query, $q)
    {
        if ($q) {
            return $query->where('name', 'like', "%${q}%");
        }
        return $query;
    }


    /**
     * Scope Category
     * @author ducchien062 <ducchien0612@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scope($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('blogs.category_id', $q);
        }
        return $query;
    }






}
