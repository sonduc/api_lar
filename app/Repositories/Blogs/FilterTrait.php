<?php

namespace App\Repositories\Blogs;
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
    public function scopeCategory($query, $q)
    {
        $q =' '.$q;
        if ($q && is_numeric($q)) {
            $query->where('blogs.category_id', $q);
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
        $q =' '.$q;
        if (($q && is_numeric($q))) {
            $query->where('blogs.hot', $q);
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
        $q =' '.$q;
        if (($q && is_numeric($q))) {
            $query->where('blogs.status', $q);
        }
        return $query;
    }


    /**
     * Scope User
     * @author ducchien062 <ducchien0612@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeUser($query, $q)
    {
        $q =' '.$q;
        if ($q && is_numeric($q)) {
            $query->where('blogs.user_id', $q);
        }
        return $query;
    }

    /**
     * Scope Tag
     * @author ducchien062 <ducchien0612@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeTag($query, $q)
    {
        $q =' '.$q;
        if ($q && is_numeric($q)) {
            $tagColumns      = $this->columnsConverter(['id', 'created_at', 'updated_at']);
            $blog_tagsColmns = $this->columnsConverter(['tag_id'], 'blog_tags', false);
            $columns         = self::mergeUnique($tagColumns, $blog_tagsColmns);
            $query
                ->addSelect($columns)
                ->join('blog_tags', 'blog_id', '=', 'blogs.id')
                ->where('blog_tags.tag_id',$q);
        }
        return $query;

    }


    public function scopeDateStart($query, $q)
    {
        if ($q)
        {
            $query->where('blogs.created_at','>=',$q);
        }
        return $query;

    }

    public function scopeDateEnd($query, $q)
    {
        if ($q)
        {
            $query->where('blogs.created_at','<=',$q);
        }
        return $query;

    }



}
