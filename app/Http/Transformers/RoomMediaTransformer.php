<?php

namespace App\Http\Transformers;

use App\Repositories\Rooms\RoomMedia;
use League\Fractal\TransformerAbstract;

class RoomMediaTransformer extends TransformerAbstract
{
    protected $availableIncludes
        = [

        ];

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param RoomMedia|null $room
     *
     * @return array
     */
    public function transform(RoomMedia $room = null)
    {
        if (is_null($room)) {
            return [];
        }

        return [
            'id'         => $room->id,
            'room_id'    => $room->room_id,
            'image'      => $room->image,
            'type'       => $room->type,
            'type_txt'   => $room->roomMedia(),
            'status'     => $room->status,
            'status_txt' => $room->status == 1 ? 'Kích hoạt' : 'Chưa kích hoạt',
            'created_at' => $room->created_at ? $room->created_at->format('Y-m-d H:m:i'),
            'updated_at' => $room->updated_at ? $room->updated_at->format('Y-m-d H:m:i'),
        ];
    }

}
