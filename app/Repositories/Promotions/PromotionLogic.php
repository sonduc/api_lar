<?php

namespace App\Repositories\Promotions;

use App\Repositories\BaseLogic;
use App\Events\AmazonS3_Upload_Event;

class PromotionLogic extends BaseLogic
{
    protected $model;

    public function __construct(PromotionRepositoryInterface $promotion)
    {
        $this->model = $promotion;
    }

    /**
    * Thêm mới dữ liệu vào promotion
    * @author sonduc <ndson1998@gmail.com>
    *
    * @param array $data
    *
    * @return \App\Repositories\Eloquent
    */
    public function store($data)
    {
        $name = rand_name($data['image']);
        event(new AmazonS3_Upload_Event($name, $data['image']));
        $data['image']   = $name.'.jpeg';

        $data_promotion = parent::store($data);
        return $data_promotion;
    }

    /**
    * Cập nhật dữ liệu cho promotion
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
        // $data['image'] = rand_name();
        $collection = $this->model->getById($id);
        $name = rand_name($data['image']);
        event(new AmazonS3_Upload_Event($name, $data['image']));
        $data['image'] = $name.'jpeg';
        
        $data_promotion = parent::update($id, $data);
        return $data_promotion;
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
