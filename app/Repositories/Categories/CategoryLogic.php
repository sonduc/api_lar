<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 23/10/2018
 * Time: 15:46
 */

namespace App\Repositories\Categories;

use App\Repositories\BaseLogic;
use App\Events\AmazonS3_Upload_Event;


class CategoryLogic extends BaseLogic
{
    protected $model;
    protected $categoryTranslate;

    /**
     * CategoryRepository constructor.
     *
     * @param Category                    $category
     * @param CategoryTranslateRepository $categoryTranslate
     */
    public function __construct(CategoryRepositoryInterface $category, CategoryTranslateRepositoryInterface $categoryTranslate)
    {
        $this->model             = $category;
        $this->categoryTranslate = $categoryTranslate;
    }

    /**
     * Thêm mới  bản ghi vào catagories và catagories_translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function store($data = null)
    {
        $name = rand_name($data['image']);
        event(new AmazonS3_Upload_Event($name, $data['image']));
        $data['image']   = $name;

        // $data['image'] = rand_name($data['image']);
        $data_category = parent::store($data);
        $this->categoryTranslate->storeCategoryTranslate($data_category, $data);
        return $data_category;
    }

    /**
     * Cập nhật  bản ghi vào catagories và catagories_translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function update($id, $data = null, $except = [], $only = [])
    {
        // $data['image'] = rand_name($data['image']);
        $collection = $this->model->getById($id);
        $name = rand_name($data['image']);
        event(new AmazonS3_Upload_Event($name, $data['image']));
        $data['image']   = $name;

        $data_category = parent::update($id, $data);
        $this->categoryTranslate->updateCategoryTranslate($data_category, $data);
        return $data_category;
    }

    /**
     * Xóa hoàn toàn bản ghi  catagories và catagories_translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     */
    public function deleteCategory($id)
    {
        $this->categoryTranslate->deleteCategoryTranslateByCategoryID($id);
        parent::delete($id);
    }

    /**
     * Cap nhat cac thuoc tinh rieng le
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param $data
     * @return \App\Repositories\Eloquent
     */
    public function singleUpdate($id, $data)
    {
        $data_category = parent::update($id, $data);
        return $data_category;
    }
}
