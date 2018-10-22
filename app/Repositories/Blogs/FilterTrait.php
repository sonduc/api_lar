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
        if (is_numeric($q)) {
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
        if (is_numeric($q)) {
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
        if (is_numeric($q)) {
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
        if (is_numeric($q)) {
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
        if ($q) {
            $blogColumns      = $this->columnsConverter(['id', 'created_at', 'updated_at']);
            $tagsColmns = $this->columnsConverter(['name'], 'tags', false);
            $columns         = self::mergeUnique($blogColumns,$tagsColmns);
            $query
                ->addSelect($columns)
                ->join('blog_tags', 'blog_id', '=', 'blogs.id')
                ->join('tags', 'tags.id', '=', 'blog_tags.tag_id')
                ->where('tags.name','=',$q);
        }
        return $query;
    }

    /**
     * Scope Date
     * @author ducchien062 <ducchien0612@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
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
