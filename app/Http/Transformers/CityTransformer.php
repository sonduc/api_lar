<?php

namespace App\Http\Transformers;

use App\Http\Transformers\Traits\FilterTrait;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;
use App\Repositories\Cities\City;

class CityTransformer extends TransformerAbstract
{
    use FilterTrait;

    protected $availableIncludes = [
        'rooms', 'districts', 'users'
    ];

    public function transform(City $city = null)
    {
        if (is_null($city)) {
            return [];
        }

        return [
            'id'                    => $city->id,
            'region_id'             => $city->region_id,
            'region_txt'            => $city->getRegion(),
            'name'                  => $city->name,
            'short_name'            => $city->short_name,
            'code'                  => $city->code,
            'longitude'             => $city->longitude,
            'latitude'              => $city->latitude,
            'priority'              => $city->priority,
            'priority_txt'          => $city->getPriorityStatus(),
            'hot'                   => $city->hot,
            'status'                => $city->status,
            'status_txt'            => $city->getStatus(),
        ];
    }

    /**
     * Include Rooms
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param City|null $city
     * @param ParamBag|null $params
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeRooms(City $city = null, ParamBag $params = null)
    {
        if (is_null($city)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $city->rooms())->get();

        return $this->collection($data, new RoomTransformer);
    }

    /**
     * Include Districts
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param City|null $city
     * @param ParamBag|null $params
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeDistricts(City $city = null, ParamBag $params = null)
    {
        if (is_null($city)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $city->districts())->get();

        return $this->collection($data, new DistrictTransformer);
    }

    public function includeUsers(City $city = null, ParamBag $params = null)
    {
        if (is_null($city)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $city->users())->get();

        return $this->collection($data, new UserTransformer);
    }

}
