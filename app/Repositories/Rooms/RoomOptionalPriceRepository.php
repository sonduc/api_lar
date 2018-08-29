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
                $list = array_merge($roomWeekPrices, $list);
            }

            if (isset($data['optional_prices']['days'])) {
                $roomDayPrices = $this->storeRoomOptionalDayPrice($room, $data);
                $list = array_merge($roomDayPrices, $list);
            }
        }

        parent::storeArray($list);
    }

    public function updateRoomOptionalPrice($room, $data = [])
    {
        if (!empty($data)) {
            if (isset($data['weekday_price'])) {
                $this->updateRoomOptionalWeekdayPrice($room, $data);
            }

            if (isset($data['optional_prices']['days'])) {

            }
        }
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

    public function updateRoomOptionalWeekdayPrice($room, $data = [])
    {
        $arrRoom    = $this->getRoomOptionalPriceByRoomID($room);
        foreach ($data['weekday_price'] as $obj) {
            foreach ($arrRoom as $roomOp) {
                $obj    = array_except($obj, ['title']);
                $room   = array_except($roomOp->toArray(),['id', 'room_id', 'day', 'created_at', 'updated_at', 'deleted_at']);

                if ($obj != $room) {
                    if ($roomOp['weekday'] == $obj['weekday']) {
                        parent::update($roomOp->id, $obj);
                        continue;
                    }
                }
            }
        }
    }

    public function updateRoomOptionalDayPrice($room, $data = [])
    {
        foreach ($data['optional_prices']['days'] as $day) {
//            $obj = $data;
//            $obj['room_id'] = $room->id;
//            $obj['day']     = $day;
//            parent::store($obj);
        }
    }

    public function getRoomOptionalPriceByRoomID($room)
    {
        return $this->model
        ->where([
            ['room_id', $room->id],
        ])->get();
    }

}
