<?php

namespace App\Http\Transformers;

use App\Repositories\Comforts\ComfortTranslate;
use League\Fractal\TransformerAbstract;

class ComfortTranslateTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    /**
     *
     * @param ComfortTranslate $comfort
     * @return array
     */
    public function transform(ComfortTranslate $comfort)
    {
        if (is_null($comfort)) {
            return [];
        }

        return [
            'id'                    => $comfort->id,
            'comfort_id'            => $comfort->comfort_id,
            'name'                  => $comfort->name,
            'description'           => $comfort->description,
            'lang'                  => $comfort->lang_id,
            'created_at'            => $comfort->created_at->format('Y-m-d H:i:s'),
            'updated_at'            => $comfort->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
