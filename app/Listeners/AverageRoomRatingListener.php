<?php
namespace App\Listeners;

use App\Events\AverageRoomRating;
use App\Repositories\Rooms\RoomLogic;
use App\Repositories\Rooms\RoomReviewLogic;
use Illuminate\Contracts\Queue\ShouldQueue;

class AverageRoomRatingListener implements ShouldQueue
{
    protected $room;
    public function __construct(RoomLogic $room)
    {
        $this->room = $room;
    }

    public function handle(AverageRoomRating $event)
    {
        $this->room->ratingCalculate($event->room_id, $event->review);
    }
}
