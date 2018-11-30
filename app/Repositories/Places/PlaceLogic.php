<?php

namespace App\Repositories\Places;

use App\Repositories\BaseLogic;

class PlaceLogic extends BaseLogic
{
    protected $model;
    protected $placetranslate;

    public function __construct(
        PlaceRepositoryInterface $place,
        PlaceTranslateRepositoryInterface $placetranslate)
    {
        $this->model          = $place;
        $this->placetranslate = $placetranslate;
    }

    /**
     * Thêm mới dữ liệu vào place
     * @author sonduc <ndson1998@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function store($data)
    {
        $data_place = parent::store($data);
        $data_place->rooms()->sync($data_place->id);
        // $this->placetranslate->storePlaceTranslate($data_place, $data);
        return $data_place;
    }

    /**
     * Cập nhật dữ liệu cho place
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
        $data_place = parent::update($id, $data);
        // $this->placetranslate->updatePlaceTranslate($data_place, $data);
        return $data_place;
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
        $data_place = parent::update($id, $data);
        return $data_place;
    }
}
