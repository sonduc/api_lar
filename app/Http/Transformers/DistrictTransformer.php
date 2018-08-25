<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;
use App\Repositories\Districts\District;

class DistrictTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'rooms'
    ];

    public function transform(District $district = null)
    {
        if (is_null($district)) {
            return [];
        }

        return [
                'id'                    => $district->id,
                'name'                  => $district->name,
                'short_name'            => $district->short_name,
                'code'                  => $district->code,
                'priority'              => $district->priority,
                'priority_txt'          => $district->getPriorityStatus(),
                'hot'                   => $district->hot,
                'status'                => $district->status,
                'status_txt'            => $district->getStatus(),
                'updated_at'            => $district->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function includeRooms(District $district = null)
    {
        if (is_null($district)) {
            return $this->null();
        }
        return $this->collection($district->users, new UserTransformer);
    }

}
