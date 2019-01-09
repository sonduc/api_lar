<?php

namespace App\Http\Transformers\Customer;

use App\Repositories\Rooms\RoomTranslate;
use League\Fractal\TransformerAbstract;

class RoomTranslateTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'description','note','space'
    ];

    public function transform(RoomTranslate $room = null)
    {
        if (is_null($room)) {
            return [];
        }

        return [
            'name'            => $room->name,
            'address'         => $room->address,
            'description'     => $room->description,
        ];
    }

    public function includeDescription(RoomTranslate $room)
    {
        if (is_null($room)) {
            return $this->null();
        }

        return $this->primitive($room->description);
    }

    public function includeNote(RoomTranslate $room)
    {
        if (is_null($room)) {
            return $this->null();
        }

        return $this->primitive($room->note);
    }

    public function includeSpace(RoomTranslate $room)
    {
        if (is_null($room)) {
            return $this->null();
        }

        return $this->primitive($room->space);
    }
}
