<?php

namespace App\Repositories\GuidebookCategories;

use App\Repositories\BaseLogic;

class GuidebookCategoryLogic extends BaseLogic
{
    protected $model;

    public function __construct(GuidebookCategoryRepositoryInterface $guidebookcategory)
    {
        $this->model = $guidebookcategory;
    }

    /**
    * Thêm mới dữ liệu vào guidebookcategory
    * @author sonduc <ndson1998@gmail.com>
    *
    * @param array $data
    *
    * @return \App\Repositories\Eloquent
    */
    public function store($data)
    {
        $data_guidebookcategory = parent::store($data);
        return $data_guidebookcategory;
    }

    /**
    * Cập nhật dữ liệu cho guidebookcategory
    * @author sonduc <ndson1998@gmail.com>
    *
    * @param int   $id
    * @param       $data
    * @param array $excepts
    * @param array $only
    *
    * @return \App\Repositories\Eloquent
    */

    public function update($id, $data, $excepts = [], $only = [])
    {
        $data_guidebookcategory = parent::update($id, $data);
        return $data_guidebookcategory;
    }

    /**
     * Cập nhật trường trạng thái status
     * @author sonduc <ndson1998@gmail.com>
     *
     * @param $id
     * @param $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function singleUpdate($id, $data)
    {
        $data_promotion = parent::update($id, $data);
        return $data_promotion ;
    }
}
