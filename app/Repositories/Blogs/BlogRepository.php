<?php

namespace App\Repositories\Blogs;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
class BlogRepository extends BaseRepository
{
    /**
     * @var Blog
     */
    protected $model;
    protected $blogTranslate;
    protected $tag;

    /**
     * CategoryRepository constructor.
     *
     * @param Blog                    $blog
     * @param BlogTranslateRepository $blogTranslate
     * @param TagRepository           $tag
     */
    public function __construct(
        Blog $blog,
        BlogTranslateRepository $blogTranslate,
        TagRepository $tag
    )
    {
        $this->model         = $blog;
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
        $data['user_id']    = Auth::user()->id;
        $data['image']      = rand_name($data['image']);
        $data_blog          = parent::store($data);
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
        $data['user_id']    = Auth::user()->id;
        $data['image'] = rand_name($data['image']);
        $data_blog     = parent::update($id, $data);
        $this->blogTranslate->updateBlogTranslate($data_blog, $data);
        $list_tag_id = $this->tag->storeTag($data);
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
