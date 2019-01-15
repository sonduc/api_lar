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

    /**
     * Lấy ra danh sách các quận huyện theo từ khóa khi không đủ 6 gợi ý tìm kiếm từ thành phố
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */

    public function getDistrictUsedForSerach($data,$request)
    {
            $result_district =  $this->model
                ->select('districts.city_id','districts.name','districts.hot','districts.status')
                ->where('districts.name', 'like', "%$request->key%")
                ->where('districts.status',District::AVAILABLE)
                ->orderBy('districts.hot', 'desc')
                ->orderBy('districts.priority', 'desc')
                ->limit(City::SERACH_SUGGESTIONS - $data->count())->get();
            $result =  array_merge($data->toArray(),$result_district->toArray());
             $count = collect($result)->count();

           return $list = [$count,$result];


    }


}
