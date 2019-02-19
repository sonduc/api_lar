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
        'rooms','guidebook'
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

    public function includeRooms(Place $place, ParamBag $params = null)
    {
        if (is_null($place)) {
            return $this->null();
        }

        $data = $this->pagination($params, $place->rooms());

        return $this->collection($data, new RoomTransformer);
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
