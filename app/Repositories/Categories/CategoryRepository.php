<?php

namespace App\Repositories\Categories;

use App\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    /**
     * CategoryPolicy model.
     * @var Model
     */
    protected $model;
    protected $categoryTranslate;

    /**
     * CategoryRepository constructor.
     *
     * @param Category $blog
     */
    public function __construct(Category $catagory, CategoryTranslateRepository $categoryTranslate)
    {
        $this->model = $catagory;
        $this->categoryTranslate= $categoryTranslate;
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
        $data['image'] = rename_image($data['image']);
        $data_category =  parent::store($data);
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
        $data['image'] = rename_image($data['image']);
        $data_category = parent::update($id, $data);
        $this->categoryTranslate->updateCategoryTranslate($data_category, $data);
        return $data_category;
    }

    /**
     * Xóa hoàn toàn bản ghi  catagories và catagories_translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function deleteCategory($id)
    {
        $this->categoryTranslate->deleteCategoryTranslateByCategoryID($id);
        parent::delete($id);
    }

    /**
     * Cập nhật một số trường trạng thái
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     * @param $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function singleUpdate($id, $data)
    {
        $data_category = parent::update($id, $data);
        return $data_category;
    }

}
