<?php
/**
 * Created by PhpStorm.
 * User: Hariki
 * Date: 10/22/2018
 * Time: 15:30
 */

namespace App\Repositories\Blogs;


use App\Repositories\BaseLogic;

class BlogLogic extends BaseLogic
{
    public $blogTranslate;
    public $tag;

    public function __construct(
        BlogRepositoryInterface $model,
        BlogTranslateRepositoryInterface $blogTranslate,
        TagRepositoryInterface $tag
    )
    {
        $this->model         = $model;
        $this->blogTranslate = $blogTranslate;
        $this->tag           = $tag;
    }

    /**
     * Thêm mới  bản ghi vào blogs và blogs_translate,tags,blog_tags
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function store($data = null)
    {
        $data['image'] = rand_name($data['image']);
        $data_blog     = parent::store($data);
        $this->blogTranslate->storeBlogTranslate($data_blog, $data);
        $list_tag_id = $this->tag->storeTag($data);
        $data_blog->tags()->detach();
        $data_blog->tags()->attach($list_tag_id);
        return $data_blog;
    }

    /**
     * Cập nhập bản ghi vào blogs và blogs_translate,tags,blog_tags
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function update($id, $data = null, $except = [], $only = [])
    {
        $data['image'] = rand_name($data['image']);
        $data_blog     = parent::update($id, $data);
        $this->blogTranslate->updateBlogTranslate($data_blog, $data);
        $list_tag_id = $this->tag->storeTag($data_blog, $data);
        $data_blog->tags()->detach();
        $data_blog->tags()->attach($list_tag_id);
        return $data_blog;
    }

    /**
     * Xóa bản ghi  blogs và blogs_translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     */
    public function destroyBlog($id)
    {
        $this->blogTranslate->deleteBlogTranslateByBlogID($id);
        parent::destroy($id);
    }


    /**
     * Cập nhật một số trường trạng thái
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function singleUpdate($id, $data)
    {
        $data_blog = parent::update($id, $data);
        return $data_blog;
    }
}