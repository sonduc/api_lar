<?php

namespace App\Http\Transformers;

use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\Rooms\Room;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class RoomTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes
        = [
            'user',
            'details',
            'comforts',
            'prices',
            'blocks',
            'media',
            'city',
            'district',
        ];


    /**
     * Lấy thông tin của phòng
     *
     * @param Room $room
     *
     * @return array
     */
    public function transform(Room $room)
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
            'room_type_id'         => $room->room_type_id ?? 'Không xác định',
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
            'latest_deal'          => $room->latest_deal ?? 'Không khả dụng',
            'rent_type'            => $room->rent_type,
            'rent_type_txt'        => $room->rentStatus(),
            'rules'                => $room->rules,
            'longitude'            => $room->longitude,
            'latitude'             => $room->latitude,
            'total_booking'        => $room->total_booking,
            'status'               => $room->status,
            'status_txt'           => $room->roomStatus(),
            'created_at'           => $room->created_at->format('Y-m-d H:i:s'),
            'updated_at'           => $room->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Xem ai là chủ phòng
     *
     * @param Room $room
     *
     * @return $room->user
     */
    public function includeUser(Room $room = null)
    {
        if (is_null($room)) {
            return $this->null();
        }
        return $this->item($room->user, new UserTransformer);
    }

    /**
     * Thông tin chi tiết phòng
     *
     * @param Room|null $room
     *
     * @return $room->roomTrans
     */
    public function includeDetails(Room $room = null, ParamBag $params = null)
    {
        if (is_null($room)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $room->roomTrans())->get();

        return $this->collection($data, new RoomTranslateTransformer);
    }


    /**
     * Thông tin tiện nghi của phòng
     *
     * @param Comfort|null $comfort
     *
     * @return $comfort->comfort
     */
    public function includeComforts(Room $room = null, ParamBag $params = null)
    {
        if (is_null($room)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $room->comforts())->get();

        return $this->collection($data, new ComfortTransformer);
    }

    /**
     * Include Prices
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Room|null     $room
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includePrices(Room $room = null, ParamBag $params = null)
    {
        if (is_null($room)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $room->prices())->get();

        return $this->collection($data, new RoomOptionalPriceTransformer);
    }

    /**
     * Include Room Time Block
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Room|null     $room
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeBlocks(Room $room = null, ParamBag $params = null)
    {
        if (is_null($room)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $room->blocks())->get();

        return $this->collection($data, new RoomTimeBlockTransformer);
    }

    /**
     * Include Room Media
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Room|null     $room
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeMedia(Room $room = null, ParamBag $params = null)
    {
        if (is_null($room)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $room->media())->get();

        return $this->collection($data, new RoomMediaTransformer);
    }

    public function includeCity(Room $room = null)
    {
        if (is_null($room)) {
            return $this->null();
        }
        return $this->item($room->city, new CityTransformer);
    }

    public function includeDistrict(Room $room = null)
    {
        if (is_null($room)) {
            return $this->null();
        }
        return $this->item($room->district, new DistrictTransformer);
    }
}
