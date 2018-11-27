<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;

class RoomOptionalPriceRepository extends BaseRepository implements RoomOptionalPriceRepositoryInterface
{
    /**
     * RoomOptionalPrice model.
     * @var Model
     */
    protected $model;

    /**
     * RoomOptionalPriceRepository constructor.
     *
     * @param RoomOptionalPrice $room
     */
    public function __construct(RoomOptionalPrice $room)
    {
        $this->model = $room;
    }

    /**
     * Cập nhật giá cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
     * @param array $data
     */
    public function updateRoomOptionalPrice($room, $data = [])
    {
        $this->deleteRoomOptionalPriceByRoomID($room);
        $this->storeRoomOptionalPrice($room, $data);
    }

    /**
     * Xóa giá của phòng theo room_id
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $room
     */
    public function deleteRoomOptionalPriceByRoomID($room)
    {
        $this->model->where('room_id', $room->id)->forceDelete();
    }

    /**
     * Lưu giá cụ thể cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
     * @param array $data
     * @param array $list
     */
    public function storeRoomOptionalPrice($room, $data = [], $list = [])
    {
        if (!empty($data)) {
            if (isset($data['weekday_price'])) {
                $roomWeekPrices = $this->storeRoomOptionalWeekdayPrice($room, $data);
                $list           = array_merge($list, $roomWeekPrices);
            }

            if (isset($data['optional_prices']['days'])) {
                $roomDayPrices = $this->storeRoomOptionalDayPrice($room, $data);
                $list          = array_merge($list, $roomDayPrices);
            }
        }
        parent::storeArray($list);
    }

    /**
     * Thêm giá theo các ngày trong tuần cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
     * @param array $data
     * @param array $list
     *
     * @return array
     */
    public function storeRoomOptionalWeekdayPrice($room, $data = [], $list = [])
    {
        foreach ($data['weekday_price'] as $obj) {
            $obj['room_id'] = $room->id;
            $list[]         = $obj;
        }
        return $list;
    }

    /**
     * Thêm giá theo từng ngày cụ thể cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
     * @param array $data
     * @param array $list
     *
     * @return array
     */
    public function storeRoomOptionalDayPrice($room, $data = [], $list = [])
    {
        $price_day        =
            array_key_exists('price_day', $data['optional_prices']) ? $data['optional_prices']['price_day'] : 0;
        $price_hour       =
            array_key_exists('price_hour', $data['optional_prices']) ? $data['optional_prices']['price_hour'] : 0;
        $price_after_hour =
            array_key_exists(
                'price_after_hour',
                             $data['optional_prices']
            ) ? $data['optional_prices']['price_after_hour'] : 0;

        foreach ($data['optional_prices']['days'] as $day) {
            $obj                     = $data;
            $obj['room_id']          = $room->id;
            $obj['day']              = $day;
            $obj['price_day']        = $price_day;
            $obj['price_hour']       = $price_hour;
            $obj['price_after_hour'] = $price_after_hour;
            $list[]                  = $obj;
        }

        return $list;
    }

    public function getOptionalPriceByRoomId($id)
    {
        return $this->model->where('room_id', $id)->get();
    }
}
