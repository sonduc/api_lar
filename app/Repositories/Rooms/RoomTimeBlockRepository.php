<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;

class RoomTimeBlockRepository extends BaseRepository
{
    /**
     * RoomTimeBlock model.
     * @var Model
     */
    protected $model;
    
    /**
     * RoomTimeBlockRepository constructor.
     *
     * @param RoomTimeBlock $roomtimeblock
     */
    public function __construct(RoomTimeBlock $roomtimeblock)
    {
        $this->model = $roomtimeblock;
    }
    
    /**
     * Cập nhật những ngày không cho đặt phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
     * @param array $data
     * @param array $list
     */
    public function updateRoomTimeBlock($room, $data = [], $list = [])
    {
        $this->deleteRoomTimeBlockByRoomID($room);
        $this->storeRoomTimeBlock($room, $data);
    }
    
    /**
     * Xóa những ngày không cho đặt phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $room
     */
    public function deleteRoomTimeBlockByRoomID($room)
    {
        $this->model->where('room_id', $room->id)->forceDelete();
    }
    
    /**
     * Lưu những ngày không cho đặt phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
     * @param array $data
     * @param array $list
     */
    public function storeRoomTimeBlock($room, $data = [], $list = [])
    {
        foreach ($data['room_time_blocks'] as $time_block) {
            $arr['time_block'] = $time_block;
            $arr['room_id']    = $room->id;
            $list[]            = $arr;
        }
        
        parent::storeArray($list);
    }
}
