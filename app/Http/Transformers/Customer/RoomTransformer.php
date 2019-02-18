<?php

namespace App\Http\Transformers\Customer;

use App\Helpers\ErrorCore;
use App\Http\Transformers\RoomTimeBlockTransformer;
use App\Http\Transformers\Traits\FilterTrait;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;
use App\Repositories\Rooms\Room;

class RoomTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [
        'media', 'details', 'comforts', 'user', 'reviews', 'city','district', 'prices',
        'places','blocks','merchant'
    ];

    public function transform(Room $room = null)
    {
        if (is_null($room)) {
            return [];
        }

        return [
            'id'                    => $room->id,
            'room_type'             => $room->room_type,
            'room_type_txt'         => $room->roomType(),
            'max_guest'             => $room->max_guest,
            'max_additional_guest'   => $room->max_additional_guest,
            'number_bed'            => $room->number_bed,
            'number_room'           => $room->number_room,
            'price_day'             => $room->price_day ?? 0,
            'price_hour'            => $room->price_hour ?? 0,
            'standard_point'        => $room->standard_point,
            'manager'               => $room->is_manager,
            'manager_txt'           => $room->managerStatus(),
            // 'latest_deal_txt'       => $room->latest_deal ? 'Có' : trans2(ErrorCore::NOT_AVAILABLE),
            // 'hot_txt'               => $room->hot ? 'Có' : trans2(ErrorCore::NOT_AVAILABLE),
            // 'new_txt'               => $room->new ? 'Có' : trans2(ErrorCore::NOT_AVAILABLE),
            // 'latest_deal'           => $room->latest_deal,
            // 'hot'                   => $room->hot,
            // 'new'                   => $room->new,
            'rent_type'             => $room->rent_type,
            'rent_type_txt'         => $room->rentStatus(),
            'longitude'             => $room->longitude,
            'latitude'              => $room->latitude,
            'avg_rating'            => $room->avg_avg_rating,
            'avg_cleanliness'       => $room->avg_cleanliness,
            'avg_quality'           => $room->avg_quality,
            'avg_service'           => $room->avg_service,
            'avg_valuable'          => $room->avg_valuable,
            'avg_rating_txt'        => $room->getTextAvgRating($room->avg_avg_rating),
            'total_review'          => $room->total_review,
            
            'is_discount'          => $room->is_discount,
            'is_discount_txt'      => $room->discountStatus(),
            'price_day_discount'   => $room->price_day_discount,
            'price_hour_discount'  => $room->price_hour_discount,
            'price_charge_guest'   => $room->price_charge_guest,
            'price_after_hour'     => $room->price_after_hour,
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

        return $this->collection($room->media, new RoomMediaTransformer);
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

        return $this->collection($room->roomTrans, new RoomTranslateTransformer);
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

        return $this->collection($room->comforts, new ComfortTransformer);
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

        return $this->collection($room->reviews, new RoomReviewTransformer);
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

        return $this->collection($room->prices, new RoomOptionalPriceTransformer);
    }

    public function includePlaces(Room $room = null, ParamBag $params = null)
    {
        if (is_null($room)) {
            return $this->null();
        }

        return $this->collection($room->places, new PlaceTransformer);
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

        $data = $this->pagination($params, $room->blocks());

        return $this->collection($data, new RoomTimeBlockTransformer);
    }

    /**
     * Include Merchant
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Room|null     $room
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeMerchant(Room $room = null, ParamBag $params = null)
    {
        if (is_null($room)) {
            return $this->null();
        }

        return $this->item($room->user, new MerchantTransformer);
    }
}
