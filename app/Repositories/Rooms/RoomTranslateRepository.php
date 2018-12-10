<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class RoomTranslateRepository extends BaseRepository implements RoomTranslateRepositoryInterface
{
    /**
     * RoomTranslate model.
     * @var Model
     */
    protected $model;

    /**
     * RoomTranslateRepository constructor.
     *
     * @param RoomTranslate $roomTranslate
     */
    public function __construct(RoomTranslate $roomTranslate)
    {
        $this->model = $roomTranslate;
    }

    /**
     * Cập nhật thông tin phòng theo ngôn ngữ
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
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

    /**
     * Thêm dữ liệu vào roomTranslate
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $room
     * @param array $data
     * @param array $list
     */
    public function storeRoomTranslate($room, $data = [], $list = [])
    {
        if (!empty($data)) {
            if (isset($data['details']['data'])) {
                foreach ($data['details']['data'] as $obj) {
                    $obj['room_id']   = $room->id;
                    $obj['slug_name'] = $obj['name'];
                    $list[]           = $obj;
                }
            }
        }
        parent::storeArray($list);
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $idRooms
     *
     * @return array
     */
    public function getRoomByListId($idRooms)
    {
        $arrRoom =[];
        foreach ($idRooms as $k => $idRoom) {
            $getVal = $this->model->where('room_id', $idRoom)->where('lang', 'vi')->first();
            $valueRoom = [
                "id" => $getVal->room_id,
                "name" => $getVal->name,
            ];
            array_push($arrRoom, $valueRoom);
        }
        return $arrRoom;
        // return $this->model->where('room_id', $id)->where('lang', 'vi')->first();
    }

    /**
     * Lấy tên phòng theo id(mảng id)
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $idRooms
     *
     * @return array
     */
    public function getRoomByListIdIndex(array $idRooms): array
    {
        /** @var Collection $getVal */
        $getVal = $this->model->whereIn('room_id', $idRooms)->where('lang', 'vi')->get(['room_id', 'name']);
        return $getVal->map(function ($value) {
            return [
                'id' => $value->room_id,
                'name' => $value->name,
            ];
        })->toArray();
    }
}
