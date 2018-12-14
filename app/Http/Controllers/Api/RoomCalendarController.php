<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Repositories\Roomcalendars\RoomCalendarRepositoryInterface;
use App\Repositories\Roomcalendars\RoomCalendar;
use ICal\ICal;

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
        // \DB::enableQueryLog();
        // $this->model->updateRoomCalendar($id);
        // $room = $this->room->getListCalendar($list_id);
        // dd($room);
        // foreach ($room as $key => $ical_url) {
        $ical_url = 'https://www.airbnb.com/calendar/ical/30510881.ics?s=6154494651e5aeabdccf0d4152d14ef6';
        try {
            $ical = new ICal($ical_url, array(
                    'defaultSpan'                 => 2,     // Default value
                    'defaultTimeZone'             => 'UTC',
                    'defaultWeekStart'            => 'MO',  // Default value
                    'disableCharacterReplacement' => false, // Default value
                    'filterDaysAfter'             => null,  // Default value
                    'filterDaysBefore'            => null,  // Default value
                    'replaceWindowsTimeZoneIds'   => false, // Default value
                    'skipRecurrence'              => false, // Default value
                    'useTimeZoneWithRRules'       => false, // Default value
                ));
        } catch (\Exception $e) {
            die($e);
        }
        $events = $ical->events();
        // dd($events);
        $now = Carbon::now()->startOfDay();
        foreach ($events as $event) {
            $time_block_start   = Carbon::parse($event->dtstart_array[3])->toDateString();
            $time_block_end     = Carbon::parse($event->dtend_array[3])->toDateString();
            $blocks[] = [
                    $time_block_start,$time_block_end
                ];
               
            $dt_start   = $ical->iCalDateToDateTime($event->dtstart_array[3], false);
            // dd($dt_start > $now);
            if ($dt_start >= $now) {
                $dt_end     = $ical->iCalDateToDateTime($event->dtend_array[3], false);
                $name       = $event->summary;
                $summary    = $event->summary;
                $status     = $event->status;
                $location   = $event->location;
                $uid        = $event->uid;
                $type       = $event->location != null ? RoomCalendar::BOOKED : RoomCalendar::BLOCKED;
                $this->model::firstOrCreate([
                        'name'       => $name,
                        'starts'     => $dt_start,
                        'ends'       => $dt_end,
                        'summary'    => $summary,
                        'status'     => $status,
                        'location'   => $location,
                        'uid'        => $uid,
                        'type'       => $type,
                        'room_id'    => $key
                    ]);
            }
        }
        $current_time_block = RoomTimeBlock::where('room_id', $key)->where('date_start', '>=', $now->toDateString())->get(['date_start', 'date_end']);

        // \DB::beginTransaction();
        try {
            foreach ($current_time_block as $k => $v) {
                $blocks[] = [$v->date_start, $v->date_end];
            }
            $blocks = $this->minimizeBlock($blocks);
            // dd($blocks);
            // RoomTimeBlock::where('room_id', $key)->forceDelete();
            foreach ($blocks as $block) {
                RoomTimeBlock::firstOrCreate([
                        "date_start"    => $block[0],
                        "date_end"      => array_key_exists(1, $block) ? $block[1] : $block[0],
                        "room_id"       => $key
                    ]);
            }
            // \DB::commit();
        } catch (\Throwable $th) {
            // \DB::rollBack();
        }
        // }
    }
}
