<?php

namespace App\Repositories\Roomcalendars;

use App\Repositories\BaseLogic;

class RoomLogic extends BaseLogic
{
    protected $room_calendar;

    public function __construct(
        RoomCalendarRepositoryInterface $room_calendar
    ) {
        $this->model = $room_calendar;
    }

    public function store($data, $room = [])
    {
    }
}
