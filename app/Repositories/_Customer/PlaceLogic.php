<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 18/12/2018
 * Time: 14:39
 */

namespace App\Repositories\_Customer;

use App\Repositories\BaseLogic;
use App\Repositories\Places\PlaceLogicTrait;
use App\Repositories\Places\PlaceRepositoryInterface;
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
