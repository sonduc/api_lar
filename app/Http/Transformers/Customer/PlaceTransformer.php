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
        'guidebook'
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

    /**
     * Include GuideBook
     * @author tuananhpham1402 <tuananhpham1402@gmail.com>
     *
     * @param Place|null     $place
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeGuidebook(Place $place = null, ParamBag $params = null)
    {
        if (is_null($place)) {
            return $this->null();
        }

        return $this->item($place->guidebookcategory, new GuidebookCategoryTransformer);
    }
}
