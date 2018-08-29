<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;

class RoomTranslateRepository extends BaseRepository
{
    /**
     * RoomTranslate model.
     * @var Model
     */
    protected $model;

    /**
     * RoomTranslateRepository constructor.
     * @param RoomTranslate $roomTranslate
     */
    public function __construct(RoomTranslate $roomTranslate)
    {
        $this->model    = $roomTranslate;
    }

    /**
     * Thêm dữ liệu vào roomTranslate
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $room
     * @param array $data
     * @param array $list
     */
    public function storeRoomTranslate($room, $data = [], $list = [])
    {
        if (!empty($data)) {
            if (isset($data['details']['data'])) {
                foreach ($data['details']['data'] as $obj) {
                    $obj['room_id']         = $room->id;
                    $obj['slug_name']       = $obj['name'];
                    $list[]                 = $obj;
                }
            }
        }

        parent::storeArray($list);
    }

    /**
     * Cập nhật thông tin phòng theo ngôn ngữ
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $room
     * @param array $data
     */
    public function updateRoomTranslate($room, $data = [])
    {
        $this->deleteRoomTranslateByRoomID($room);
        $this->storeRoomTranslate($room, $data);
    }

    /**
     * Xóa tất cả bản ghi theo room_id
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $room
     */
    public function deleteRoomTranslateByRoomID($room)
    {
        $this->model->where('room_id', $room->id)->forceDelete();
    }

}
