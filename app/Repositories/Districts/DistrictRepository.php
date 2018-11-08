<?php

namespace App\Repositories\Districts;

use App\Repositories\BaseRepository;

class DistrictRepository extends BaseRepository implements DistrictRepositoryInterface
{
    /**
     * Role model.
     * @var Model
     */
    protected $model;

    /**
     * CityRepository constructor.
     *
     * @param City $city
     */
    public function __construct(District $district)
    {
        $this->model = $district;

    }

    /**
     * Lấy tên quận huyện theo id(mảng id) 
     * @author sonduc <ndson1998@gmail.com>
     *
     * @param $room
     */
    public function getDistrictByListId($idDistricts)
    {
        $arrDistrict =[];
        foreach ($idDistricts as $k => $idDistrict) {
            $getVal = $this->model->find($idDistrict);
            $valueDistrict = [
                "id" => $getVal->id,
                "name" => $getVal->name,
            ];
            array_push($arrDistrict,$valueDistrict);
        }
        return $arrDistrict;
    }

}
