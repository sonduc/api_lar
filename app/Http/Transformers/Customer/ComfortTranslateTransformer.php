<?php

namespace App\Http\Transformers\Customer;

use App\Repositories\Comforts\ComfortTranslate;
use League\Fractal\TransformerAbstract;

class ComfortTranslateTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    public function transform(ComfortTranslate $comfort = null)
    {
        if (is_null($comfort)) {
            return [];
        }

        return [
            'name'        => $comfort->name,
            'description' => $comfort->description,
            'lang'        => $comfort->lang,
        ];
    }

}
