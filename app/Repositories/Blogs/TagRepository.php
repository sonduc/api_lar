<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 27/09/2018
 * Time: 13:28
 */

namespace App\Repositories\Blogs;

use App\Repositories\BaseRepository;

class TagRepository extends BaseRepository
{
    protected $model;
    protected $blogTag;
    protected $blog;

    /**
     * ComfortRepository constructor.
     *
     * @param Comfort $comfort
     */
    public function __construct(Tag $tag,BlogTagRepository $blogTag)
    {
        $this->model        = $tag;
        $this->blogTag      = $blogTag;
    }
   // Kiem tra nếu tag đã  tồn tại bản ghi thì lấy ra id thêm vào blog tag còn chưa tồn tại thì thêm mới
    public function storeTag($blog, $data = [], $tag = [],$list= [])
    {
        if (isset($data['tags'])) {
            $arr = explode(',', $data['tags']['data'][0]['name']);
            for ($i = 0; $i < count($arr); $i++) {
                $countTag = $this->countTag($arr[$i]);
                if ($countTag > 0) {
                    $data_tag = $this->findTagByName( $arr[$i]);
                    $list[]= $data_tag->id;
                    //$this->blogTag->storeBlogTag($data_tag,$blog);
                } else {
                    $tag['name'] = $arr[$i];
                    $tag['slug'] = str_slug($arr[$i],'-');
                    $data_tag = parent::store($tag);
                    $list[]= $data_tag->id;
                    //$this->blogTag->storeBlogTag($data_tag,$blog);
                }
            }
        }

        return $list;

    }
    public function updateTag($blog, $data = [], $tag = [],$list = [])
    {
        if (isset($data['tags'])) {
            $arr = explode(',', $data['tags']['data'][0]['name']);
            for ($i = 0; $i < count($arr); $i++) {
               $countTag = $this->countTag($arr[$i]);
                if ($countTag > 0) {
                    $data_tag = $this->findTagByName( $arr[$i]);
                    $list[] = $data_tag->id;
                    //$this->blogTag->updateBlogTag($data_tag,$blog);
                } else {
                    $tag['name'] = $arr[$i];
                    $tag['slug'] = str_slug($arr[$i],'-');
                    $data_tag = parent::store($tag);
                    $list[] = $data_tag->id;
                    //$this->blogTag->storeBlogTag($data_tag,$blog);
                }
            }
        }
        return $list;

    }

    public function deleteTagID($comfort)
    {
        $this->model->where('tag_id', $comfort->id)->forceDelete();
    }

    public function getByTagID($id)
    {
        return $this->model->where('tag_id', $id)->select('id')->get();
    }
    public function countTag($name) {
        return $this->model->where('name', $name)->count();
    }
    public function findTagByName($name) {
        return $this->model->where('name',$name)->first();
    }
}
