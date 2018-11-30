<?php

namespace App\Http\Transformers;

use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\Places\Place;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class PlaceTransformer extends TransformerAbstract
{
    use FilterTrait;

    protected $availableIncludes = [
        'details',
    ];

    public function transform(Place $place = null)
    {
        if (is_null($place)) {
            return [];
        }

        return [
            'id'                    => $place->id,
            'name'                  => $place->name,
            'description'           => $place->description,
            'latitude'              => $place->latitude,
            'longitude'             => $place->longitude,
            'status'                => $place->status,
            'status_txt'            => $place->getPlaceStatus(),
            'guidebook_category_id' => $place->guidebook_category_id,
            'created_at'            => $place->created_at ? $place->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'            => $place->updated_at ? $place->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     *
     * @author sonduc <ndson1998@gmail.com>
     *
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeDetails(Place $place = null, ParamBag $params = null)
    {
        if (is_null($place)) {
            return $this->null();
        }

        $columns = ['*'];
        $data    = $place->placeTrans();

        $data = $this->pagination($params, $data, $columns);

        return $this->collection($data, new PlaceTranslateTransformer);
    }

}
