<?php

namespace App\Events;

class GenerateWestayRoomCalendarEvent extends Event
{
    public $room;

    public function __construct($room)
    {
        $this->room = $room;
    }
}
