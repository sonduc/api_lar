<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;

class RoomMediaRepository extends BaseRepository
{
    /**
     * RoomMedia model.
     * @var Model
     */
    protected $model;

    /**
     * RoomMediaRepository constructor.
     * @param RoomMedia $roomMedia
     */
    public function __construct(RoomMedia $roomMedia)
    {
        $this->model = $roomMedia;
    }

    /**
     * Lưu ảnh cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $room
     * @param array $data
     * @param array $list
     */
    public function storeRoomMedia($room, $data = [], $list = [])
    {
        foreach ($data['images'] as $img) {
            $img['room_id']     = $room->id;
            $img['image']       = rename_image($img['name']);
            $list[] = $img;
        }
        parent::storeArray($list);
    }

    /**
     * Cập nhật ảnh cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $room
     * @param array $data
     * @param array $list
     */
    public function updateRoomMedia($room, $data = [], $list = [])
    {
        $this->deleteRoomMediaByRoomID($room);
        $this->storeRoomMedia($room, $data);
    }

    /**
     * Xóa ảnh phòng dựa theo room_id
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $room
     */
    public function deleteRoomMediaByRoomID($room)
    {
        $this->model->where('room_id', $room->id)->forceDelete();
    }

}
