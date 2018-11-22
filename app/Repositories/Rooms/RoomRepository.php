<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;
use App\Repositories\Traits\Scope;

class RoomRepository extends BaseRepository implements RoomRepositoryInterface
{


    /**
     * RoomRepository constructor.
     *
     * @param Room $room
     */
    public function __construct(
        Room $room
    ) {
        $this->model = $room;
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

    /**
     * Lấy tất cả phòng trừ các phòng có ID trong danh sách
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $list
     * @param array $params
     * @param       $size
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function getAllRoomExceptListId(array $list, $params, $size)
    {
        $this->useScope($params, ['check_in', 'check_out']);
        return $this->model
            ->whereNotIn('rooms.id', $list)
            ->where('rooms.status', Room::AVAILABLE)
            ->paginate($size);
    }

    public function getRoom($id)
    {
       return $this->model
            ->join('room_translates', 'rooms.id', '=', 'room_translates.room_id')
            ->where('room_translates.lang','vi')
            ->where('rooms.id', $id)->first();
    }


    public function checkVaildRefund($refund)
    {
        $refund_map = array_map(function ($item) {
            return $item['days'];
        },$refund);
        $refund_uique = array_unique($refund_map);
        if(count($refund_map) > count($refund_uique)) throw new \Exception('Số ngày ở các nức hoàn tiền không thể giống nhau');
        return  json_encode($refund);
    }
}
