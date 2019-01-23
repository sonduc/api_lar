<?php

namespace App\Repositories\Cities;

use App\Repositories\BaseRepository;
use App\Repositories\Districts\District;
use App\Repositories\Districts\DistrictRepositoryInterface;
use Illuminate\Support\Collection;

class CityRepository extends BaseRepository implements CityRepositoryInterface
{
    /**
     * Role model.
     * @var Model
     */
    protected $model;
    protected $district;

    /**
     * CityRepository constructor.
     *
     * @param City $city
     */
    public function __construct(City $city, DistrictRepositoryInterface $district)
    {
        $this->model    = $city;
        $this->district = $district;
    }

    /**
     * Lấy tên thành phố theo id(mảng id)
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $idCities
     *
     * @return array
     */
    public function getCityByListId($idCities)
    {
        $arrCity = [];
        foreach ($idCities as $k => $idCity) {
            $getVal    = $this->model->find($idCity);
            $valueCity = [
                "id"   => $getVal->id,
                "name" => $getVal->name,
            ];
            array_push($arrCity, $valueCity);
        }
        return $arrCity;
        // return $this->model->where('room_id', $id)->where('lang', 'vi')->first();
    }

    /**
     * Lấy thành phố theo id(mảng id)
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $idCities
     *
     * @return array
     */
    public function getCityByListIdIndex($idCities)
    {
        /** @var Collection $getVal */
        $getVal = $this->model->whereIn('id', $idCities)->get(['id', 'name']);

        return $getVal->map(function ($value) {
            return [
                'id'   => $value->id,
                'name' => $value->name,
            ];
        })->toArray();
    }


}
