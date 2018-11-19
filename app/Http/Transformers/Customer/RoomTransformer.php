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
        'media', 'details', 'comforts', 'user', 'reviews', 'city','district', 'prices'
    ];

    public function transform(Room $room = null)
    {
        if (is_null($room)) {
            return [];
        }

        return [
            'id'                    => $room->id,
            'merchant_id'           => $room->merchant_id,
            'room_type'             => $room->room_type,
            'room_type_txt'         => $room->roomType(),
            'max_guest'             => $room->max_guest,
            'max_additional_guest'  => $room->max_additional_guest ?? 0,
            'number_bed'            => $room->number_bed,
            'number_room'           => $room->number_room,
            'city_id'               => $room->city_id ?? 'Không xác định',
            'district_id'           => $room->district_id ?? 'Không xác định',
            'checkin'               => $room->checkin,
            'checkout'              => $room->checkout,
            'price_day'             => $room->price_day ?? 0,
            'price_hour'            => $room->price_hour ?? 0,
            'price_after_hour'      => $room->price_after_hour ?? 0,
            'price_charge_guest'    => $room->price_charge_guest ?? 0,
            'cleaning_fee'          => $room->cleaning_fee ?? 0,
            'standard_point'        => $room->standard_point,
            'is_manager'            => $room->is_manager,
            'manager_txt'           => $room->managerStatus(),
            'hot'                   => $room->hot ?? 0,
            'new'                   => $room->new ?? 0,
            'latest_deal'           => $room->latest_deal,
            'latest_deal_txt'       => $room->latest_deal ? 'Có' : trans2(ErrorCore::NOT_AVAILABLE),
            'rent_type'             => $room->rent_type,
            'rent_type_txt'         => $room->rentStatus(),
            'longitude'             => $room->longitude,
            'latitude'              => $room->latitude,
            'status'                => $room->status,
            'cleanliness'           => $room->avg_cleanliness,
            'quality'               => $room->avg_quality,
            'service'               => $room->avg_service,
            'valuable'              => $room->avg_valuable,
            'avg_rating'            => $room->avg_avg_rating,
            'total_review'          => $room->total_review,
            'total_recommend'       => $room->total_recommend,
        ];
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

        $data = $this->pagination($params, $room->media());

        return $this->collection($data, new RoomMediaTransformer);
    }

    /**
     * Include Room Translates
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Room|null     $room
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeDetails(Room $room = null, ParamBag $params = null)
    {
        if (is_null($room)) {
            return $this->null();
        }
        $lang = getLocale();
        $data = $this->pagination($params, $room->roomTrans($lang));

        return $this->collection($data, new RoomTranslateTransformer);
    }

    /**
     * Include Comforts
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Room|null     $room
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeComforts(Room $room = null, ParamBag $params = null)
    {
        if (is_null($room)) {
            return $this->null();
        }

        $data = $this->pagination($params, $room->comforts());

        return $this->collection($data, new ComfortTransformer);
    }

    /**
     * Include User
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Room|null     $room
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeUser(Room $room = null, ParamBag $params = null)
    {
        if (is_null($room)) {
            return $this->null();
        }

        return $this->item($room->user, new UserTransformer);
    }

    /**
     * Include City
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Room|null     $room
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeCity(Room $room = null, ParamBag $params = null)
    {
        if (is_null($room)) {
            return $this->null();
        }

        return $this->item($room->city, new CityTransformer);
    }

    /**
     * Include District
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Room|null     $room
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeDistrict(Room $room = null, ParamBag $params = null)
    {
        if (is_null($room)) {
            return $this->null();
        }

        return $this->item($room->district, new DistrictTransformer);
    }

    /**
     * Include Reviews
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Room|null     $room
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeReviews(Room $room = null, ParamBag $params = null)
    {
        if (is_null($room)) {
            return $this->null();
        }

        $data = $this->pagination($params, $room->reviews());

        return $this->collection($data, new RoomReviewTransformer);
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

        $data = $this->pagination($params, $room->prices());

        return $this->collection($data, new RoomOptionalPriceTransformer);
    }
}
