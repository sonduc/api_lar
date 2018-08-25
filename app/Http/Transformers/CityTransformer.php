<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;
use App\Repositories\Cities\City;

class CityTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'rooms'
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

    public function includeRooms(City $city = null)
    {
        if (is_null($city)) {
            return $this->null();
        }
        return $this->collection($city->users, new UserTransformer);
    }

}
