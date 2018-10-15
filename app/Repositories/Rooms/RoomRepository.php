<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;

class RoomRepository extends BaseRepository
{
    /**
     * Room model.
     * @var Room
     */
    protected $model;
    protected $roomTranslate;
    protected $roomOptionalPrice;
    protected $roomMedia;
    protected $roomTimeBlock;

    /**
     * RoomRepository constructor.
     *
     * @param Room                        $room
     * @param RoomTranslateRepository     $roomTranslate
     * @param RoomOptionalPriceRepository $roomOptionalPrice
     * @param RoomMediaRepository         $roomMedia
     * @param RoomTimeBlockRepository     $roomTimeBlock
     */
    public function __construct(
        Room $room,
        RoomTranslateRepository $roomTranslate,
        RoomOptionalPriceRepository $roomOptionalPrice,
        RoomMediaRepository $roomMedia,
        RoomTimeBlockRepository $roomTimeBlock
    )
    {
        $this->model             = $room;
        $this->roomTranslate     = $roomTranslate;
        $this->roomOptionalPrice = $roomOptionalPrice;
        $this->roomMedia         = $roomMedia;
        $this->roomTimeBlock     = $roomTimeBlock;
    }

    /**
     * Lưu trữ bản ghi của phòng vào bảng rooms, room_translates, room_optional_prices, room_comfort
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function store($data)
    {
        $data_room = parent::store($data);

        $this->roomTranslate->storeRoomTranslate($data_room, $data);
        $this->roomOptionalPrice->storeRoomOptionalPrice($data_room, $data);
        $this->roomMedia->storeRoomMedia($data_room, $data);
        $this->roomTimeBlock->storeRoomTimeBlock($data_room, $data);
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
                $data_room->comforts()->sync($data['comforts']);
            }
        }
    }

    /**
     * Cập nhật cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param int   $id
     * @param       $data
     * @param array $excepts
     * @param array $only
     *
     * @return \App\Repositories\Eloquent
     */
    public function update($id, $data, $excepts = [], $only = [])
    {
        $data_room = parent::update($id, $data);

        $this->roomTranslate->updateRoomTranslate($data_room, $data);
        $this->roomOptionalPrice->updateRoomOptionalPrice($data_room, $data);
        $this->roomMedia->updateRoomMedia($data_room, $data);
        $this->roomTimeBlock->updateRoomTimeBlock($data_room, $data);
        $this->storeRoomComforts($data_room, $data);

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

    /**
     * Cập nhật riêng lẻ các thuộc tính của phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $id
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function minorRoomUpdate($id, $data = [])
    {
        return parent::update($id, $data);
    }
}
