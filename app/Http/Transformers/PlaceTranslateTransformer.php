<?php

namespace App\Http\Transformers;

use App\Repositories\Places\PlaceTranslate;
use League\Fractal\TransformerAbstract;

class PlaceTranslateTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    public function transform(PlaceTranslate $placetranslate = null)
    {
        if (is_null($placetranslate)) {
            return [];
        }

        return [
            'id'          => $placetranslate->id,
            'name'        => $placetranslate->name,
            'description' => $placetranslate->description,
            'lang'        => $placetranslate->lang,
            'place_id'    => $placetranslate->place_id,
            'created_at'  => $placetranslate->created_at ? $placetranslate->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'  => $placetranslate->updated_at ? $placetranslate->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }

}
