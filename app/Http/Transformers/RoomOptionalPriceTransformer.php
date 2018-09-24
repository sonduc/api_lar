<?php

namespace App\Http\Transformers;

use App\Repositories\Rooms\RoomOptionalPrice;
use League\Fractal\TransformerAbstract;

class RoomOptionalPriceTransformer extends TransformerAbstract
{
    protected $availableIncludes
        = [

        ];

    public function transform(RoomOptionalPrice $room = null)
    {
        if (is_null($room)) {
            return [];
        }

        return [
            'id'                 => $room->id,
            'weekday'            => $room->weekday,
            'day'                => $room->day ?? 'Không xác định',
            'price_day'          => $room->price_day ?? 0,
            'price_hour'         => $room->price_hour ?? 0,
            'price_after_hour'   => $room->price_after_hour ?? 0,
            'price_charge_guest' => $room->price_charge_guest ?? 0,
            'status'             => $room->status,
            'status_txt'         => $room->status == 1 ? 'Kích hoạt' : 'Chưa kích hoạt',
            'created_at'         => $room->created_at->format('Y-m-d H:m:i'),
            'updated_at'         => $room->updated_at->format('Y-m-d H:m:i'),
        ];
    }

}
