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


    /**
     * Lấy ra danh sách thành phố từ danh sách gợi ý
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */

    public function getCityUserForSearchSuggestions($data)
    {
        $key   = $data['key'];
        $query =  $this->model;

        $result = $query->select('cities.name','cities.id','cities.hot','cities.status','cities.priority')
                        ->where('cities.name', 'like', "%$key%")
                        ->where('cities.status',City::AVAILABLE)
                        ->orderBy('cities.priority', 'desc')->limit(City::SERACH_SUGGESTIONS)->get();
        return $result;
    }


    /** Lấy ra danh sách các quận huyện được ưu tiên theo thành phố khi không đủ 6 gợi ý
      * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */

    public function getDistrictOfCityPriority($data,$request)
    {
        $district_priorty=  $this->model
            ->select('districts.name','districts.id','districts.hot','districts.status','districts.priority')
            ->join('districts', 'cities.id', '=', 'districts.city_id')
            ->where('cities.name', 'like', "%$request->key%")
            ->where('cities.status',City::AVAILABLE)
            ->orderBy('cities.priority', 'desc')->limit(City::SERACH_SUGGESTIONS)
            ->orderBy('districts.hot','desc')
            ->orderBy('districts.priority','desc')
            ->get()->toArray();
        $result =  array_merge($data,$district_priorty);
        return $result;
    }


}
