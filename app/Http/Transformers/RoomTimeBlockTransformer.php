<?php

namespace App\Http\Transformers;

use App\Repositories\Rooms\RoomTimeBlock;
use League\Fractal\TransformerAbstract;

class RoomTimeBlockTransformer extends TransformerAbstract
{
    protected $availableIncludes
        = [

        ];

    public function transform(RoomTimeBlock $room = null)
    {
        if (is_null($room)) {
            return [];
        }

        return [
            'id'         => $room->id,
            'room_id'    => $room->room_id,
            'time_block' => $room->time_block,
            'status'     => $room->status,
            'status_txt' => $room->status == 1 ? 'Đã kích hoạt' : 'Chưa kích hoạt',
            'created_at' => $room->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $room->updated_at->format('Y-m-d H:i:s'),
        ];
    }

}
