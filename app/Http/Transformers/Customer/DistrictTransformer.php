<?php

namespace App\Http\Transformers\Customer;

use App\Http\Transformers\Traits\FilterTrait;
use League\Fractal\TransformerAbstract;
use App\Repositories\Districts\District;

class DistrictTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [

    ];

    public function transform(District $district = null)
    {
        if (is_null($district)) {
            return [];
        }

        return [
            'id'           => $district->id,
            'name'         => $district->name,
            'short_name'   => $district->short_name,
            'code'         => $district->code,
            'priority'     => $district->priority,
            'priority_txt' => $district->getPriorityStatus(),
            'hot'          => $district->hot,
            'city_id'      => $district->city_id,
            'status'       => $district->status,
            'status_txt'   => $district->getStatus(),
//            'updated_at'   => $district->updated_at ? $district->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }

}
