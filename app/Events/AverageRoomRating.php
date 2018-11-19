<?php

namespace App\Events;

class AverageRoomRating extends Event
{
    public $review;
    public $room_id;

    public function __construct($room_id, $review)
    {
        $this->room_id = $room_id;
        $this->review = $review;
    }
}
