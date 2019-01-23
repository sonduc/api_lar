<?php

namespace App\Repositories\Districts;

use App\Repositories\BaseRepository;
use App\Repositories\Cities\City;
use Illuminate\Support\Collection;

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
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $idDistricts
     *
     * @return array
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
            $arrDistrict[] = $valueDistrict;
        }
        return $arrDistrict;
    }

    /**
     * Lấy districts theo id(mảng id)
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $idDistricts
     *
     * @return array
     */
    public function getDistrictByListIdIndex(array $idDistricts): array
    {
        /** @var Collection $getVal */
        $getVal = $this->model->whereIn('id', $idDistricts)->get(['id','name']);

        return $getVal->map(function ($item) {
          return [
              'id' => $item->id,
              'name' => $item->name
          ];
        })->toArray();

    }

}
