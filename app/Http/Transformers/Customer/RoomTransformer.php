<?php

namespace App\Http\Transformers\Customer;

use App\Helpers\ErrorCore;
use App\Http\Transformers\Traits\FilterTrait;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;
use App\Repositories\Rooms\Room;

class RoomTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [
        'media', 'details'
    ];

    public function transform(Room $room = null)
    {
        if (is_null($room)) {
            return [];
        }

        return [
            'id'                   => $room->id,
            'merchant_id'          => $room->merchant_id,
            'room_type'            => $room->room_type,
            'room_type_txt'        => $room->roomType(),
            'max_guest'            => $room->max_guest,
            'max_additional_guest' => $room->max_additional_guest ?? 0,
            'number_bed'           => $room->number_bed,
            'number_room'          => $room->number_room,
            'city_id'              => $room->city_id ?? 'Không xác định',
            'district_id'          => $room->district_id ?? 'Không xác định',
            'checkin'              => $room->checkin,
            'checkout'             => $room->checkout,
            'price_day'            => $room->price_day ?? 0,
            'price_hour'           => $room->price_hour ?? 0,
            'price_after_hour'     => $room->price_after_hour ?? 0,
            'price_charge_guest'   => $room->price_charge_guest ?? 0,
            'cleaning_fee'         => $room->cleaning_fee ?? 0,
            'standard_point'       => $room->standard_point,
            'is_manager'           => $room->is_manager,
            'manager_txt'          => $room->managerStatus(),
            'hot'                  => $room->hot ?? 0,
            'new'                  => $room->new ?? 0,
            'latest_deal'          => $room->latest_deal,
            'latest_deal_txt'      => $room->latest_deal ? 'Có' : trans2(ErrorCore::NOT_AVAILABLE),
            'rent_type'            => $room->rent_type,
            'rent_type_txt'        => $room->rentStatus(),
            'longitude'            => $room->longitude,
            'latitude'             => $room->latitude,
            'status' => $room->status
        ];
    }

    public function includeMedia(Room $room = null, ParamBag $params = null)
    {
        if (is_null($room)) {
            return $this->null();
        }

        $data = $this->pagination($params, $room->media());

        return $this->collection($data, new RoomMediaTransformer);
    }

    public function includeDetails(Room $room = null, ParamBag $params = null)
    {
        if (is_null($room)) {
            return $this->null();
        }
        $lang = getLocale();
        $data = $this->pagination($params, $room->roomTrans($lang));

        return $this->collection($data, new RoomTranslateTransformer);
    }

}
