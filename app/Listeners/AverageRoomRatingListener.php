<?php
namespace App\Listeners;

use App\Events\AverageRoomRating;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\Rooms\RoomLogic;
use App\Repositories\Rooms\RoomReviewLogic;

class AverageRoomRatingListener implements ShouldQueue
{
    public function __construct()
    {
    }

    public function handle(AverageRoomRating $event)
    {
        $this->room->ratingCalculate($event->room_id, $event->review);
    }
}
