<?php

namespace App\Repositories\Places;

use App\Repositories\BaseLogic;
use App\Repositories\Rooms\RoomRepositoryInterface;

class PlaceLogic extends BaseLogic
{
    use PlaceLogicTrait;


    public function __construct(
        PlaceRepositoryInterface $place,
        RoomRepositoryInterface $room
    ) {
        $this->model          = $place;
        $this->room           = $room;
    }
}
