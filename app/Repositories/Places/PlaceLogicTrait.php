<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 18/12/2018
 * Time: 14:27
 */

namespace App\Repositories\Places;

use Illuminate\Support\Facades\DB;
trait PlaceLogicTrait
{
    protected $model;
    protected $room;

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
        $arrPlace = [];
        $arrPlaceId = [];
        foreach ($data['places'] as $key => $value) {
            if(!isset($value['id'])){
                $arrPlace[] = $value;
            }
            if(isset($value['id'])){
                $arrPlaceId[] = $value['id'];
            }
        }

        foreach ($arrPlace as $key => $value) {
            $place = $this->model->getValuePlace($value);
            if ($place == null) {
                $data_place = parent::store($value);
                array_push($arrPlaceId,$data_place->id);
            } else {
                array_push($arrPlaceId,$place->id);
            }
        }
        DB::table('room_places')->where('room_id',$data['room_id'])->delete();

        $data_room = $this->room->getById($data["room_id"]);
        $data_room->places()->sync($arrPlaceId);
        $dataReturn = [
            'message'        => "Cập nhật thành công",
        ];
        return $dataReturn;
    }

}
