<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;

class RoomRepository extends BaseRepository
{
    /**
     * Role model.
     * @var Model
     */
    protected $model;

    /**
     * RoomRepository constructor.
     * @param Room $room
     * @param RoomTranslateRepository $roomTranslate
     * @param RoomOptionalPriceRepository $roomOptionalPrice
     */
    public function __construct(
        Room $room,
        RoomTranslateRepository $roomTranslate,
        RoomOptionalPriceRepository $roomOptionalPrice
    )
    {
        $this->model                = $room;
        $this->roomTranslate        = $roomTranslate;
        $this->roomOptionalPrice    = $roomOptionalPrice;
    }

    /**
     * Lưu trữ bản ghi của phòng vào bảng rooms, room_translates, room_optional_prices, room_comfort
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $data
     * @return \App\Repositories\Eloquent
     */
    public function store($data)
    {
        $data_room      = parent::store($data);
        $this->roomTranslate->storeRoomTranslate($data_room, $data);
        $this->roomOptionalPrice->storeRoomOptionalPrice($data_room, $data);
        $this->storeRoomComforts($data_room, $data);

        return $data_room;
    }

    /**
     * Cập nhật cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param int $id
     * @param $data
     * @param array $excepts
     * @param array $only
     * @return bool
     */
    public function update($id, $data, $excepts = [], $only = [])
    {
        $data_room = parent::update($id, $data);
        $this->roomTranslate->updateRoomTranslate($data_room, $data);
        $this->roomOptionalPrice->updateRoomOptionalPrice($data_room, $data);
        $this->storeRoomComforts($data_room, $data);
        return $data_room;
    }

    /**
     * Lưu comforts cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $data_room
     * @param $data
     */
    public function storeRoomComforts($data_room, $data)
    {
        if (!empty ($data)) {
            if (isset($data['comforts'])) {
                $data_room->comforts()->detach();
                $data_room->comforts()->attach($data['comforts']);
            }
        }
    }

    /**
     * Chỉnh sửa trạng thái của phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     * @param $data
     * @return bool
     */
    public function status($id, $data)
    {
        $data_room = parent::update($id, $data);
        return $data_room;
    }

    /**
     * Lấy ra kiểu phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return mixed
     */
    public function getRoomType()
    {
        return $this->model::ROOM_TYPE;
    }

}
