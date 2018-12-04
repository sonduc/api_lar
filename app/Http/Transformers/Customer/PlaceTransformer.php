<?php

namespace App\Http\Transformers\Customer;

use League\Fractal\ParamBag;
use App\Repositories\Places\Place;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\Traits\FilterTrait;

class PlaceTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [

    ];

    public function transform(Place $place = null)
    {
        if (is_null($place)) {
            return [];
        }

        return [
            'name'                  => $place->name,
            'description'           => $place->description,
            'latitude'              => $place->latitude,
            'longitude'             => $place->longitude,
        ];
    }

}
