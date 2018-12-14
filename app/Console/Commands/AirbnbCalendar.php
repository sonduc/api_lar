<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Bookings\BookingConstant;
use App\Repositories\Roomcalendars\RoomCalendarRepositoryInterface;
use App\Events\Booking_Notification_Event;
use App\Events\Booking_Reviews_Event;
use App\Repositories\Rooms\Room;

use Carbon\Carbon;

class AirbnbCalendar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'airbnb:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Airbnb Calendar with Westay Calendar';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RoomCalendarRepositoryInterface $room_calendar, Room $room)
    {
        parent::__construct();
        $this->room_calendar    = $room_calendar;
        $this->room             = $room;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $room_list_id = $this->room::where('airbnb_calendar', '!=', '')->pluck('id');

        $this->room_calendar->updateRoomCalendar($room_list_id);
    }
}
