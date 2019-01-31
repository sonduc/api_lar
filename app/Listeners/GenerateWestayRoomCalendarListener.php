<?php
namespace App\Listeners;

use App\Events\GenerateWestayRoomCalendarEvent;
use App\Repositories\Rooms\RoomLogic;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateWestayRoomCalendarListener implements ShouldQueue
{
    protected $room;
    public function __construct(RoomLogic $room)
    {
        $this->room = $room;
    }

    public function handle(GenerateWestayRoomCalendarEvent $event)
    {
        $this->room->generateWestayRoomCalendar($event->room);
    }
}
