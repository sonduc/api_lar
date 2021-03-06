<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 18/12/2018
 * Time: 08:13
 */

namespace App\Http\Transformers\Merchant;

use App\Http\Transformers\BookingTransformer;
use App\Http\Transformers\CityTransformer;
use App\Http\Transformers\ComfortTransformer;
use App\Http\Transformers\DistrictTransformer;
use App\Http\Transformers\PlaceTransformer;
use App\Http\Transformers\RoomMediaTransformer;
use App\Http\Transformers\RoomOptionalPriceTransformer;
use App\Http\Transformers\RoomReviewTransformer;
use App\Http\Transformers\RoomTimeBlockTransformer;
use App\Http\Transformers\RoomTranslateTransformer;
use App\Http\Transformers\Traits\FilterTrait;
use App\Http\Transformers\UserTransformer;
use App\Repositories\Rooms\Room;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class RoomTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [
        'user',
        'details',
        'comforts',
        'prices',
        'blocks',
        'media',
        'city',
        'district',
        'bookings',
        'reviews',
        'places',
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

//        $field = array_keys($room->getAttributes());

        $data = [
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
            'latest_deal'          => $room->latest_deal,
            'latest_deal_txt'      => $room->latest_deal ?? 'Không khả dụng',
            'is_discount'          => $room->is_discount,
            'is_discount_txt'      => $room->discountStatus(),
            'price_day_discount'   => $room->price_day_discount,
            'price_hour_discount'  => $room->price_hour_discount,
            'rent_type'            => $room->rent_type,
            'rent_type_txt'        => $room->rentStatus(),
            'rules'                => $room->rules,
            'longitude'            => $room->longitude,
            'latitude'             => $room->latitude,
            'total_booking'        => $room->total_booking,
            'status'               => $room->status,
            'merchant_status'      => $room->merchant_status,
            'status_txt'           => $room->roomStatus(),
            'merchant_status_txt'  => $room->roomStatus(),
            'cleanliness'          => $room->avg_cleanliness,
            'quality'              => $room->avg_quality,
            'service'              => $room->avg_service,
            'valuable'             => $room->avg_valuable,
            'avg_rating'           => $room->avg_avg_rating,
            'avg_cleanliness'      => $room->avg_cleanliness,
            'avg_quality'          => $room->avg_quality,
            'avg_service'          => $room->avg_service,
            'avg_valuable'         => $room->avg_valuable,
            'total_review'         => $room->total_review,
            'total_recommend'      => $room->total_recommend,

            'settings'             => json_decode($room->settings),
            'airbnb_calendar'      => $room->airbnb_calendar,
            'westay_calendar'      => $room->westay_calendar,
            'percent'              => $room->percent,
            'created_at'           => $room->created_at ? $room->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'           => $room->updated_at ? $room->updated_at->format('Y-m-d H:i:s') : null,
        ];

        return $data;
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Room|null $room
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeUser(Room $room = null)
    {
        if (is_null($room)) {
            return $this->null();
        }
        return $this->item($room->user, new UserTransformer);
    }

    /**
     *
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

        $columns = ['*'];
        $data = $room->roomTransMerchant();

        $data = $this->pagination($params, $data, $columns);

        return $this->collection($data, new RoomTranslateTransformer);
    }


    /**
     *
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

    public function includePlaces(Room $room = null, ParamBag $params = null)
    {
        if (is_null($room)) {
            return $this->null();
        }

        $data = $this->pagination($params, $room->places());

        return $this->collection($data, new PlaceTransformer);
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

    public function includeBookings(Room $room = null, ParamBag $params = null)
    {
        if (is_null($room)) {
            return $this->null();
        }

        $data = $this->pagination($params, $room->bookings());

        return $this->collection($data, new BookingTransformer());
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


    /**
     * Đánh giá phòng
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
}
