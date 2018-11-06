<?php

namespace App\Http\Transformers\Customer;

use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\Cities\City;
use League\Fractal\TransformerAbstract;

class CityTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [

    ];

    public function transform(City $city = null)
    {
        if (is_null($city)) {
            return [];
        }

        return [
            'id'           => $city->id,
            'region_id'    => $city->region_id,
            'region_txt'   => $city->getRegion(),
            'name'         => $city->name,
            'short_name'   => $city->short_name,
            'code'         => $city->code,
            'longitude'    => $city->longitude,
            'latitude'     => $city->latitude,
            'priority'     => $city->priority,
            'priority_txt' => $city->getPriorityStatus(),
            'hot'          => $city->hot,
            'status'       => $city->status,
            'status_txt'   => $city->getStatus(),
//            'created_at'   => $city->created_at ? $city->created_at->format('Y-m-d H:i:s') : null,
//            'updated_at'   => $city->updated_at ? $city->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }

}
