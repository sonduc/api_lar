<?php

namespace App\Repositories\Places;

use App\Repositories\BaseLogic;
use App\Repositories\Rooms\RoomRepositoryInterface;
use DB;

class PlaceLogic extends BaseLogic
{
    protected $model;
    protected $room;

    public function __construct(
        PlaceRepositoryInterface $place,
        RoomRepositoryInterface $room) {
        $this->model          = $place;
        $this->room           = $room;
    }

    /**
     * Thêm mới dữ liệu vào place
     * @author sonduc <ndson1998@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function store($data)
    {
        $place = $this->model->getValuePlace($data);
        if ($place == null) {
            $data_place = parent::store($data);
            $data_place->rooms()->sync($data["room_id"]);
        } else {
            $data_room = $this->room->getById($data["room_id"]);
            $data_room->places()->sync($place->id);
            $data_place = $place;
        }
        return $data_place;
    }

    /**
     * Cập nhật dữ liệu cho place
     * @author sonduc <ndson1998@gmail.com>
     *
     * @param int   $id
     * @param       $data
     * @param array $excepts
     * @param array $only
     *
     * @return \App\Repositories\Eloquent
     */

    public function update($id, $data, $excepts = [], $only = [])
    {
        $data_place = parent::update($id, $data);
        return $data_place;
    }

    /**
     * Cập nhật trường trạng thái status
     * @author sonduc <ndson1998@gmail.com>
     *
     * @param $id
     * @param $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function singleUpdate($id, $data)
    {
        $data_place = parent::update($id, $data);
        return $data_place;
    }
    public function editRoomPlace($data)
    {
        DB::table('room_places')->where('room_id',$data['room_id'])->delete();

        $data_room = $this->room->getById($data["room_id"]);
        $data_room->places()->sync($data['edit_place_id']);
        $dataReturn = [
            'message'        => "Cập nhật thành công",
        ];
        return $dataReturn;
    }
}
