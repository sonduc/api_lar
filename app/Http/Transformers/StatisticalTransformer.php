<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;
use App\Repositories\Statisticals\Statistical;

class StatisticalTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    public function transform(Statistical $statistical = null)
    {
        if (is_null($statistical)) {
            return [];
        }

        return [
            'id'    => $statistical->id,
        ];
    }

}
