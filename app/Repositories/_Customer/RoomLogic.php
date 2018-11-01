<?php

namespace App\Repositories\_Customer;


use App\Repositories\BaseLogic;
use App\Repositories\Rooms\RoomRepository;
use App\Repositories\Rooms\RoomRepositoryInterface;

class RoomLogic extends BaseLogic
{
    /**
     * RoomLogic constructor.
     *
     * @param RoomRepositoryInterface|RoomRepository $model
     */
    public function __construct(
        RoomRepositoryInterface $model
    )
    {
        $this->model = $model;
    }

    /**
     * Lấy danh sách phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param     $params
     * @param int $pageSize
     *
     * @return \App\Repositories\Illuminate\Pagination\Paginator
     */
    public function getRooms($params, $pageSize = 5)
    {
        $data = $this->model->getByQuery($params, $pageSize);
        return $data;
    }
}