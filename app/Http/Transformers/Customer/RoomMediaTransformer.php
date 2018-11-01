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
            'id'         => $room->id,
            'image'      => $room->image,
            'type'       => $room->type,
            'type_txt'   => $room->roomMedia(),
            'status'     => $room->status,
            'status_txt' => $room->status == 1 ? trans2('status.activate') : trans2('status.deactivate'),
        ];
    }

}
