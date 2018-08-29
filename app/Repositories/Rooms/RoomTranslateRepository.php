<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;

class RoomTranslateRepository extends BaseRepository
{
    /**
     * RoomTranslate model.
     * @var Model
     */
    protected $model;

    /**
     * RoomTranslateRepository constructor.
     * @param RoomTranslate $room
     */
    public function __construct(RoomTranslate $roomTranslate)
    {
        $this->model    = $roomTranslate;
    }

    /**
     * Thêm dữ liệu vào roomTranslate
     *
     * @param $room
     * @param array $data
     *
     * @return void
     */
    public function storeRoomTranslate($room, $data = [], $list = [])
    {
        if (!empty($data)) {
            if (isset($data['details']['data'])) {
                foreach ($data['details']['data'] as $obj) {
                    $obj['room_id']         = $room->id;
                    $obj['slug_name']       = $obj['name'];
                    $list[] = $obj;
                }
            }
        }

        parent::storeArray($list);
    }

    /**
     * Cập nhật thông tin phòng theo ngôn ngữ
     *
     * @param $room
     * @param array $data
     *
     * @return void
     */
    public function updateRoomTranslate($room, $data = [])
    {

        if (!empty($data)) {
            if (isset($data['details']['data'])) {

                foreach ($data['details']['data'] as $obj) {
                    $obj['slug_name']   = $obj['name'];
                    unset($obj['room_id']);

                    $roomTrans = $this->getRoomTranslateByLangAndID($room->id, $obj['lang']);
                    parent::update($roomTrans->id, $obj);
                }
            }
        }
    }

    /**
     * Lấy ngôn ngữ của phòng theo ID và mã ngôn ngữ
     *
     * @param $id
     * @param $lang
     *
     * @return mixed
     */
    public function getRoomTranslateByLangAndID($id, $lang)
    {
        return $this->model->where([
            ['room_id', $id],
            ['lang', $lang]
        ])->first();
    }
}
