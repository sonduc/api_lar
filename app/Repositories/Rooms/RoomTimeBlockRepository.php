<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;
use Carbon\Carbon;

class RoomTimeBlockRepository extends BaseRepository implements RoomTimeBlockRepositoryInterface
{
    use RoomTimeBlockTrait;
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
        $blocks = $this->minimizeBlock($data['room_time_blocks']);
//        dd($blocks);
        foreach ($blocks as $block) {
            $arr['date_start'] = $block[0];
            $arr['date_end']   = array_key_exists(1, $block) ? $block[1] : $block[0];
            $arr['room_id']    = $room->id;
            $list[]            = $arr;
        }

        parent::storeArray($list);
    }

    /**
     * Lấy các ngày bị block theo mã phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     *
     * @return mixed
     */
    public function getFutureRoomTimeBlockByRoomId($id)
    {
        return $this->model->where([
            ['room_id', $id],
        ])->get();
    }
}
