<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;
use App\Repositories\Roomcalendars\RoomCalendarRepositoryInterface;

class RoomTimeBlockRepository extends BaseRepository implements RoomTimeBlockRepositoryInterface
{
    use RoomTimeBlockTrait;
    /**
     * RoomTimeBlock model.
     * @var Model
     */
    protected $model;
    protected $room_calendar;

    /**
     * RoomTimeBlockRepository constructor.
     *
     * @param RoomTimeBlock $roomtimeblock
     */
    public function __construct(RoomTimeBlock $roomtimeblock, RoomCalendarRepositoryInterface $room_calendar)
    {
        $this->model            = $roomtimeblock;
        $this->room_calendar    = $room_calendar;
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
        // dd($data);
        $collection  = collect($data);
        $unlock_days = $collection->get('unlock_days', []);
        $blocks      = $this->minimizeBlock($data['room_time_blocks'], $unlock_days);
        // dd($blocks);
        foreach ($blocks as $block) {
            $arr['date_start'] = $block[0];
            $arr['date_end']   = array_key_exists(1, $block) ? $block[1] : $block[0];
            $arr['room_id']    = $room->id;
            $list[]            = $arr;
            $this->room_calendar->updateCalendarRoomBlock($room, $block);
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
