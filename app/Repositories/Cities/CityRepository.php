<?php

namespace App\Repositories\Cities;

use App\Repositories\BaseRepository;

class CityRepository extends BaseRepository implements CityRepositoryInterface
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
    public function __construct(City $city)
    {
        $this->model = $city;
    }

    /**
     * Lấy tên thành phố theo id(mảng id)
     * @author sonduc <ndson1998@gmail.com>
     *
     * @param $room
     */
    public function getCityByListId($idCities)
    {
        $arrCity =[];
        foreach ($idCities as $k => $idCity) {
            $getVal = $this->model->find($idCity);
            $valueCity = [
                "id" => $getVal->id,
                "name" => $getVal->name,
            ];
            array_push($arrCity, $valueCity);
        }
        return $arrCity;
        // return $this->model->where('room_id', $id)->where('lang', 'vi')->first();
    }
    
    /**
     * Lấy tên phòng theo id(mảng id)
     * @author sonduc <ndson1998@gmail.com>
     *
     * @param $idCity
     */
    public function getCityByListIdIndex($idCities)
    {
        $getVal = $this->model->whereIn('id', $idCities)->get(['id','name']);
        $arrCity = [];
        foreach ($getVal as $key => $value) {
            $valueCity = [
                "id" => $value->id,
                "name" => $value->name,
            ];
            array_push($arrCity, $valueCity);
        }

        return $arrCity;
    }
}
