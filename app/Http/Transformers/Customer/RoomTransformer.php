<?php

namespace App\Http\Transformers\Customer;

use League\Fractal\TransformerAbstract;
use App\Repositories\Rooms\Room;

class RoomTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    public function transform(Room $room = null)
    {
        if (is_null($room)) {
            return [];
        }

        return [
            'id'    => $room->id,
        ];
    }

}
