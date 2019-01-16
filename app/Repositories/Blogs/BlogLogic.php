<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 23/10/2018
 * Time: 15:12
 */

namespace App\Repositories\Blogs;

use App\Repositories\BaseLogic;
use Illuminate\Support\Facades\Auth;
use App\Events\AmazonS3_Upload_Event;

class BlogLogic extends BaseLogic
{
    protected $model;
    protected $blogTranslate;
    protected $tag;

    /**
     * BlogLogic constructor.
     *
     * @param BlogRepositoryInterface|BlogRepository                   $blog
     * @param BlogTranslateRepositoryInterface|BlogTranslateRepository $blogTranslate
     * @param TagRepositoryInterface|TagRepository                     $tag
     */
    public function __construct(
        BlogRepositoryInterface $blog,
        BlogTranslateRepositoryInterface $blogTranslate,
        TagRepositoryInterface $tag
    ) {
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
        $data['user_id'] = Auth::user()->id;
        $name = rand_name($data['image']);
        event(new AmazonS3_Upload_Event($name, $data['image']));
        $data['image']   = $name.'.jpeg';
        $data['slug'] = to_slug($data['title']);
        $data_blog       = parent::store($data);
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
        $data['user_id'] = Auth::user()->id;
        $name = rand_name($data['image']);
        event(new AmazonS3_Upload_Event($name, $data['image']));
        $data['image']   = $name.'.jpeg';

        $data_blog       = parent::update($id, $data);
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
