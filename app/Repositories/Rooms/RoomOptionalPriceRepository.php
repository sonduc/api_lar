<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;

class RoomOptionalPriceRepository extends BaseRepository
{
    /**
     * RoomOptionalPrice model.
     * @var Model
     */
    protected $model;

    /**
     * RoomOptionalPriceRepository constructor.
     * @param RoomOptionalPrice $room
     */
    public function __construct(RoomOptionalPrice $room)
    {
        $this->model = $room;
    }

    /**
     * Lưu giá cụ thể cho phòng
     *
     * @param $room
     * @param array $data
     *
     * @return void
     */
    public function storeRoomOptionalPrice($room, $data = [], $list = [])
    {
        if (!empty($data)) {
            if (isset($data['weekday_price'])) {
                $roomWeekPrices = $this->storeRoomOptionalWeekdayPrice($room, $data);
                $list = array_merge($list, $roomWeekPrices);
            }

            if (isset($data['optional_prices']['days'])) {
                $roomDayPrices = $this->storeRoomOptionalDayPrice($room, $data);
                $list = array_merge($list, $roomDayPrices);
            }
        }

        parent::storeArray($list);
    }

    public function updateRoomOptionalPrice($room, $data = [])
    {
        $this->deleteRoomOptionalPriceByRoomID($room);
        $this->storeRoomOptionalPrice($room, $data);
    }

    /**
     * Thêm giá theo các ngày trong tuần cho phòng
     *
     * @param $room
     * @param array $data
     *
     * @return array
     */
    public function storeRoomOptionalWeekdayPrice($room, $data = [], $list = [])
    {
        foreach ($data['weekday_price'] as $obj) {
                $obj['room_id'] = $room->id;
                $list[] = $obj;
        }
        return $list;
    }

    /**
     * Thêm giá theo từng ngày cụ thể cho phòng
     *
     * @param $room
     * @param array $data
     * @param array $list
     *
     * @return array
     */
    public function storeRoomOptionalDayPrice($room, $data = [], $list = [])
    {
        foreach ($data['optional_prices']['days'] as $day) {
            $obj = $data;
            $obj['room_id'] = $room->id;
            $obj['day']     = $day;
            $list[] = $obj;
        }
        return $list;
    }

    public function deleteRoomOptionalPriceByRoomID($room)
    {
        $this->model->where('room_id', $room->id)->forceDelete();
    }

}
