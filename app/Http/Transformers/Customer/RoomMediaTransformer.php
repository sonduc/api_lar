<?php

namespace App\Http\Transformers\Customer;

use App\Repositories\Rooms\RoomMedia;
use League\Fractal\TransformerAbstract;

class RoomMediaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    public function transform(RoomMedia $room = null)
    {
        if (is_null($room)) {
            return [];
        }

        return [
            'image'      => $room->image,
        ];
    }
}
