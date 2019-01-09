<?php

namespace App\Repositories\Rooms;

use App\Events\AmazonS3_Upload_Event;
use App\Repositories\BaseRepository;

class RoomMediaRepository extends BaseRepository implements RoomMediaRepositoryInterface
{
    /**
     * RoomMedia model.
     * @var RoomMedia
     */
    protected $model;

    /**
     * RoomMediaRepository constructor.
     *
     * @param RoomMedia $roomMedia
     */
    public function __construct(RoomMedia $roomMedia)
    {
        $this->model = $roomMedia;
    }

    /**
     * Cập nhật ảnh cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
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

    /**
     * Lưu ảnh cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
     * @param array $data
     * @param array $list
     */
    public function storeRoomMedia($room, $data = [], $list = [])
    {
        foreach ($data['images'] as $img) {
            $name = rand_name();
            $img['room_id'] = $room->id;
            $img['image']   = $name.'.jpeg';
            $list[]         = $img;
            event(new AmazonS3_Upload_Event($name, $img['source']));
        }
        parent::storeArray($list);
    }
}
