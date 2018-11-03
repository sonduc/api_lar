<?php

namespace App\Http\Transformers\Customer;

use App\Repositories\Rooms\RoomTranslate;
use League\Fractal\TransformerAbstract;

class RoomTranslateTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    public function transform(RoomTranslate $room = null)
    {
        if (is_null($room)) {
            return [];
        }

        return [
            'id'          => $room->id,
            'name'        => $room->name,
            'slug_name'   => $room->slug_name,
            'address'     => $room->address,
            'lang'        => $room->lang,
        ];
    }

}
