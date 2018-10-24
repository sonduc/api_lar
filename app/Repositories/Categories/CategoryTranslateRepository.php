<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 26/09/2018
 * Time: 09:38
 */

namespace App\Repositories\Categories;

use App\Repositories\BaseRepository;

class CategoryTranslateRepository extends BaseRepository implements CategoryTranslateRepositoryInterface
{
    protected $model;

    public function __construct(CategoryTranslate $category)
    {
        $this->model = $category;
    }

    public function updateCategoryTranslate($category, $data = [])
    {
        $this->deleteCategoryTranslate($category);
        $this->storeCategoryTranslate($category, $data);
    }

    /**
     * Xóa bản dich danh mục
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $category
     */
    public function deleteCategoryTranslate($category)
    {
        $this->model->where('category_id', $category->id)->forceDelete();
    }

    /**
     * Thêm danh mục mới
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $category
     * @param array $data
     * @param array $list
     */
    public function storeCategoryTranslate($category, $data = [], $list = [])
    {
        if (!empty($data)) {
            if (isset($data['details'])) {
                foreach ($data['details']['data'] as $val) {
                    $val['category_id'] = $category->id;
                    $val['slug']        = str_slug($val['name']);
                    $list[]             = $val;
                }
            }
        }

        parent::storeArray($list);
    }

    public function deleteCategoryTranslateByCategoryID($id)
    {
        $this->model->where('category_id', $id)->forceDelete();
    }


}
