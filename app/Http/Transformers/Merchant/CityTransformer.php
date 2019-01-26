<?php

namespace App\Http\Transformers\Merchant;

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
            'name'         => $city->name,
        ];
    }
}
