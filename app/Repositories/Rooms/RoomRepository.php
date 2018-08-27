<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;

class RoomRepository extends BaseRepository
{
    /**
     * Role model.
     * @var Model
     */
    protected $model;

    /**
     * RoomRepository constructor.
     * @param Room $room
     * @param RoomTranslateRepository $roomTranslate
     */
    public function __construct(Room $room, RoomTranslateRepository $roomTranslate)
    {
        $this->model = $room;
        $this->roomTranslate = $roomTranslate;
    }

    /**
     * Lưu trữ bản ghi của phòng vào bảng room và room_translates
     * @param $data
     * @return \App\Repositories\Eloquent
     */
    public function store($data)
    {
//        dd($data);
        $data_room      = parent::store($data);

//        $this->roomTranslate->storeRoomTranslate($data, $data_room->id);
        return $data_room;
    }

    /**
     * Update room
     * @param int $id
     * @param $data
     * @param array $except
     * @param array $only
     * @return bool
     */
    public function update($id, $data, $except = [], $only = [])
    {
        $data['rules']  = json_encode($data['rules']);
        $data_room      = parent::update($id, $data);

        $this->roomTranslate->updateRoomTranslate($data, $data_room->id);

        return $data_room;
    }

    /**
     * Xóa phòng trong bảng room và room_translate
     * @param $id
     */
    public function deleteRoom($id)
    {
        $list_id = $this->roomTranslate->getByRoomID($id);

        foreach ($list_id as $room) {
            $this->roomTranslate->delete($room->id);
        }

        parent::delete($id);
    }



}
