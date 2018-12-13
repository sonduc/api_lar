<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Repositories\Roomcalendars\RoomCalendarRepositoryInterface;
use App\Repositories\Roomcalendars\RoomCalendar;

class RoomCalendarController extends ApiController
{
    public function __construct(
        RoomCalendarRepositoryInterface $room
    ) {
        $this->model = $room;
    }

    /**
     * Gets the events data from the database
     * and populates the iCal object.
     *
     * @return void
     */
    public function getRoomCalendar($id)
    {
        $this->model->icalGenerator($id);
        // $this->model->icalGoogleCalendar($id);
    }
    
    public function updateCalendar($id)
    {
        \DB::enableQueryLog();
        $this->model->updateRoomCalendar($id);
    }
}
