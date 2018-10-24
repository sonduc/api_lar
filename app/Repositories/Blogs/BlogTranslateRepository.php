<?php

namespace App\Repositories\Blogs;


use App\Repositories\BaseRepository;

class BlogTranslateRepository extends BaseRepository implements BlogTranslateRepositoryInterface
{
    protected $model;

    public function __construct(BlogTranslate $blog)
    {
        $this->model = $blog;
    }

    /**
     * Cập nhật blog translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $blog
     * @param array $data
     */
    public function updateBlogTranslate($blog, $data = [])
    {
        $this->deleteBlogTranslate($blog);
        $this->storeBlogTranslate($blog, $data);
    }


    /**
     * Xoa theo kieu id lay tu mang du lieu
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $blog
     */
    public function deleteBlogTranslate($blog)
    {
        $this->model->where('blog_id', $blog->id)->forceDelete();
    }

    /**
     * Lưu blog translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $blog
     * @param array $data
     * @param array $list
     */
    public function storeBlogTranslate($blog, $data = [], $list = [])
    {
        if (!empty($data)) {
            if (isset($data['details'])) {
                foreach ($data['details']['data'] as $val) {
                    $val['blog_id'] = $blog->id;
                    $val['slug']    = str_slug($val['title'], '-');
                    $list[]         = $val;
                }
            }
        }
        parent::storeArray($list);
    }

    /**
     * Xoa theo id truyen truc tiep
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     */
    public function deleteBlogTranslateByBlogID($id)
    {
        $this->model->where('blog_id', $id)->forceDelete();
    }

}
