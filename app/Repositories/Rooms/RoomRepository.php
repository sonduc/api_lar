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
    )
    {
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
}
