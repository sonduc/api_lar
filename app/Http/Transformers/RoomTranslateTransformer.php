<?php

namespace App\Http\Transformers;

use App\Repositories\Rooms\RoomTranslate;
use League\Fractal\TransformerAbstract;

class RoomTranslateTransformer extends TransformerAbstract
{
    protected $availableIncludes
        = [

        ];

    /**
     *
     * @param RoomTranslate $room
     *
     * @return array
     */
    public function transform(RoomTranslate $room)
    {
        if (is_null($room)) {
            return [];
        }

//        $field = array_keys($room->getAttributes());

        $data = [
            'id'          => $room->id,
            'room_id'     => $room->room_id,
            'name'        => $room->name,
            'slug_name'   => $room->slug_name,
            'address'     => $room->address,
            'description' => $room->description,
            'lang'        => $room->lang,
            'space'       => $room->space,
            'note'        => $room->note,
            'description' => $room->description,
            'created_at'  => $room->created_at ? $room->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'  => $room->updated_at ? $room->updated_at->format('Y-m-d H:i:s') : null,
        ];

        return $data;
    }


}
