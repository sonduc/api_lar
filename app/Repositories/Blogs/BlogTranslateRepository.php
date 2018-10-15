<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 27/09/2018
 * Time: 17:21
 */

namespace App\Repositories\Blogs;


use App\Repositories\BaseRepository;

class BlogTranslateRepository extends BaseRepository
{
    protected $model;
    public function __construct(BlogTranslate $blog)
    {
        $this->model = $blog;
    }

    public function storeBlogTranslate($blog, $data = [], $list = [])
    {
        if (!empty($data)) {
            if (isset($data['details'])) {
                foreach ($data['details']['data'] as $val) {
                    $val['blog_id']                 = $blog->id;
                    $val['slug']                    = str_slug($val['title'],'-');
                    $list[]                         = $val;
                }
            }
        }
        parent::storeArray($list);
    }
    public function updateBlogTranslate($blog, $data = [])
    {
        $this->deleteBlogTranslate($blog);
        $this->storeBlogTranslate($blog, $data);
    }
    // Xoa theo kieu id lay tu mang du lieu
    public function deleteBlogTranslate($blog)
    {
        $this->model->where('blog_id', $blog->id)->forceDelete();
    }

    //Xoa theo id truyen truc tiep
    public function deleteBlogTranslateByBlogID($id)
    {
        $this->model->where('blog_id', $id)->forceDelete();
    }

}
