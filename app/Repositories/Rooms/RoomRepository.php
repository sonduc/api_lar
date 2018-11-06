<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;

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
     * [getRoomByListId description]
     * @param  array  $lists [description]
     * @return [type]        [description]
     */
    public function getRoomByListId(array $lists)
    {
        foreach ($lists as $key => $value) {
            $nameRoom = parent::getById($value);
        }
    }
}
