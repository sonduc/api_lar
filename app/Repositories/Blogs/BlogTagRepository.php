<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 27/09/2018
 * Time: 13:26
 */

namespace App\Repositories\Blogs;

use App\Repositories\BaseRepository;
class BlogTagRepository extends  BaseRepository
{
    protected $model;
    public function __construct(BlogTag $blog)
    {
        $this->model = $blog;
    }

    public function storeBlogTag($tag, $blog , $list = [])
    {
        if (isset($tag) && isset($blog) ) {
          $list['blog_id']      = $blog->id;
          $list['tag_id']       = $tag->id;
        }
        parent::store($list);
    }
    public function updateBlogTag($tag, $blog)
    {
        $this->deleteBlogTagID($blog);
        $this->storeBlogTag($tag,$blog);
    }

    public function deleteBlogTagID($blog)
    {
        $this->model->where('blog_id', $blog->id)->forceDelete();
    }

    public function getByBlogTagID($id)
    {
        return $this->model->where('blog_id', $id)->select('id')->get();
    }


}
