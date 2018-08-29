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
     *
     * @param $data
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

    public function update($id, $data, $excepts = [], $only = [])
    {
        $data_room = parent::update($id, $data);
//        $this->roomTranslate->updateRoomTranslate($data_room, $data);
//        $this->roomOptionalPrice->updateRoomOptionalPrice($data_room, $data);
        return $data_room;
    }

    /**
     * Lưu comforts cho phòng
     *
     * @param $data_room
     * @param $data
     *
     * @return void
     */
    public function storeRoomComforts($data_room, $data)
    {
        if (!empty ($data)) {
            if (isset($data['comforts'])) {
                $data_room->comforts()->attach($data['comforts']);
            }
        }
    }


}
