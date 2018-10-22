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
            'date_start' => $room->date_start,
            'date_end'   => $room->date_end,
        ];
    }

}
