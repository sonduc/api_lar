<?php

namespace App\Http\Transformers;

use App\Repositories\Rooms\RoomTranslate;
use League\Fractal\TransformerAbstract;

class RoomTranslateTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    /**
     *
     * @param RoomTranslate $room
     * @return array
     */
    public function transform(RoomTranslate $room)
    {
        if (is_null($room)) {
            return [];
        }

        return [
            'id'                    => $room->id,
            'room_id'               => $room->room_id,
            'room_name'             => $room->name,
            'slug_name'             => $room->slug_name,
            'address'               => $room->address,
            'slug_address'          => $room->slug_address,
            'description'           => $room->description,
            'lang'                  => $room->language,
            'space'                 => $room->space,
            'note'                  => $room->note,
            'description'           => $room->description,
            'created_at'            => $room->created_at->format('Y-m-d H:i:s'),
            'updated_at'            => $room->updated_at->format('Y-m-d H:i:s'),
        ];
    }



}
