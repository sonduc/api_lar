<?php

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;

class RoomTranslateRepository extends BaseRepository
{
    /**
     * Role model.
     * @var Model
     */
    protected $model;

    /**
     * RoleRepository constructor.
     * @param Role $role
     */
    public function __construct(RoomTranslate $room)
    {
        $this->model = $room;
    }

    /**
     * Thêm data vào roomTranslate
     * @param $data
     * @param $id
     */
    public function storeRoomTranslate($data, $id)
    {
        $data['slug_name']      = to_slug($data['name']);
        $data['slug_address']   = to_slug($data['address']);
        $data['room_id']        = $id;

        parent::store($data);
    }

    /**
     * Update room_translate. Nếu không có bản ghi nào phù hợp thì tạo translate mới.
     * @param $data
     * @param $id
     */
    public function updateRoomTranslate($data, $id)
    {
        $data['slug_name']      = to_slug($data['name']);
        $data['slug_address']   = to_slug($data['address']);
        $data['room_id']        = $id;

        $count = $this->model->where([
            ['room_id', $id],
            ['lang_id', $data['lang_id']]
        ])->first();

        $count ? parent::update($id, $data) : parent::store($data);
    }

    public function getByRoomID($id)
    {
        return $this->model->where('room_id', $id)->select('id')->get();
    }
}
