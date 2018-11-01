<?php

namespace App\Repositories\_Customer;


use App\Repositories\BaseLogic;
use App\Repositories\BaseRepository;
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

    public function getRooms($params)
    {
        $data = $this->model->getMostPopularRooms($params);
        return $data;
    }
}