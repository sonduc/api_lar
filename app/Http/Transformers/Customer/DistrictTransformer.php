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

        ];
    }
}
