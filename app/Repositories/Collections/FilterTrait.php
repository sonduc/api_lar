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
            $query->where('collections.hot', $q);
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
            $query->where('collections.status', $q);
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
            $query->where('collections.new', $q);
        }
        return $query;
    }

    public function scopeName($query, $q)
    {
        if ($q) {
            $collectionColumns      = $this->columnsConverter(['id','image','status','hot','new','created_at', 'updated_at'], 'collections', false);
            $collectionTransColumns = $this->columnsConverter(['name','lang'], 'collection_translates', false);
            $columns                = self::mergeUnique($collectionColumns, $collectionTransColumns);
            $query
                ->addSelect($columns)
                ->join('collection_translates','collection_translates.collection_id','=','collections.id')
                ->where('collection_translates.name', 'like', "%${q}%");
        }
        return $query;
    }






}
