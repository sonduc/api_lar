<?php

namespace App\Repositories\Roomcalendars;

use Carbon\Carbon;
use App\Repositories\BaseRepository;
use App\Repositories\Rooms\RoomTranslateRepositoryInterface;
use Illuminate\Support\Facades\Crypt;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Rooms\RoomTimeBlockTrait;
use App\Repositories\Rooms\RoomTimeBlock;
use ICal\ICal;
use App\Repositories\Bookings\BookingConstant;

class RoomCalendarRepository extends BaseRepository implements RoomCalendarRepositoryInterface
{
    use RoomTimeBlockTrait;

    /**
     * RoomCalendar model.
     * @var Model
     */
    protected $model;
    protected $room_translate;
    protected $room;

    /**
     * RoomCalendarRepository constructor.
     * @param RoomCalendar $roomcalendar
     */
    public function __construct(
        RoomCalendar $roomcalendar,
        RoomTranslateRepositoryInterface $room_translate,
        RoomRepositoryInterface $room
        // RoomTimeBlockRepositoryInterface $room_time_block
    ) {
        $this->model            = $roomcalendar;
        $this->room_translate   = $room_translate;
        $this->room             = $room;
        // $this->room_time_block  = $room_time_block;
    }

    public function icalGenerator($id)
    {
        $events = $this->model->where('room_id', $id)->get();

        define('ICAL_FORMAT', 'Ymd\THis\Z');

        $icalObject = "BEGIN:VCALENDAR
        VERSION:2.0
        METHOD:PUBLISH
        PRODID:-//Westay//Calendar//EN\n";
        
        foreach ($events as $event) {
            // dump($event->location == null);
            if ($event->location == null) {
                $icalObject .=
                "BEGIN:VEVENT
                SUMMARY:$event->summary
                DTSTART:".date(ICAL_FORMAT, strtotime($event->starts))."
                DTEND:". date(ICAL_FORMAT, strtotime($event->ends)) . "
                UID:$event->uid
                END:VEVENT\n";
            } else {
                $icalObject .=
                "BEGIN:VEVENT
                SUMMARY:$event->summary
                DTSTART:" . date(ICAL_FORMAT, strtotime($event->starts)) . "
                DTEND:" . date(ICAL_FORMAT, strtotime($event->ends)) . "
                DTSTAMP:" . date(ICAL_FORMAT, strtotime($event->created_at)) . "
                UID:$event->uid
                STATUS:$event->status
                DESCRIPTION:" . strtoupper($event->description) . "
                LAST-MODIFIED:" . date(ICAL_FORMAT, strtotime($event->updated_at)) . "
                LOCATION:$event->location
                END:VEVENT\n";
            }
        }

        $icalObject .= "END:VCALENDAR";
        
        // Set the headers
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="cal.ics"');
        $icalObject = str_replace(' ', '', $icalObject);
        
        echo $icalObject;
    }

    // public function icalGoogleCalendar($id)
    // {
    //     // dd('s');
    //     $calendar = new Calendar();
    //     define('ICAL_FORMAT', 'Ymd\THis\Z');

    //     $calendar->setProdId('-//Westay Calendar');
    //     $timezone = config('app.timezone');
    //     $timeZone = new \DateTimeZone($timezone);
    //     $calendar->setTimezone($timeZone);
    //     $calendar->setCustomHeaders([
    //         'X-WR-TIMEZONE' => $timezone,
    //         'X-WR-CALNAME' => 'Westay Calendar',
    //         'X-PUBLISHED-TTL' => 'PT5M'
    //     ]);

    //     // $events = RoomCalendar::all();
    //     $events = $this->model->where('room_id', $id)->get();
    //     // dd($events);
    //     foreach ($events as $event) {
    //         // dd($event);
    //         // dd(date(ICAL_FORMAT, strtotime($event->starts)));
    //         $calendarEvent = new CalendarEvent();
    //         $calendarEvent
    //                         ->setStart(Carbon::parse($event->starts))
    //                         ->setEnd(Carbon::parse($event->ends))
    //                         // ->setStart(date(ICAL_FORMAT, strtotime($event->starts)))
    //                         // ->setEnd(date(ICAL_FORMAT, strtotime($event->ends)))
    //                         ->setSummary($event->summary)
    //                         ->setUid($event->uid);
    //         $calendar->addEvent($calendarEvent);
    //     }
    //     // dd($calendar);

    //     $calendarExport = new CalendarExport(new CalendarStream, new Formatter());
    //     // dd($calendar);
    //     $calendarExport->addCalendar($calendar);
    //     // $this->updateRoomCalendar($id);
    //     //output .ics formatted text
    //     // dd($calendarExport);
    //     echo $calendarExport->getStream();
    // }

    public function storeRoomCalendar($data_booking, $data)
    {
        $room = $this->room_translate->getRoomByListIdIndex([$data_booking->room_id]);
        $room_name = $room[0]['name'];
        $start  = Carbon::parse($data_booking->checkin)->startOfDay();
        if (BookingConstant::BOOKING_TYPE_HOUR == $data_booking->booking_type) {
            $end    = Carbon::parse($data_booking->checkout)->addDay()->startOfDay();
        } else {
            $end    = Carbon::parse($data_booking->checkout)->startOfDay();
        }

        $this->model->create([
            'name'       => $data_booking->name .' ('. $data_booking->code.')',
            'starts'     => $start,
            'ends'       => $end,
            'summary'    => $data_booking->name .' '. $data_booking->code,
            'status'     => 'CONFIRMED',
            'location'   => $room_name,
            'uid'        => Crypt::encrypt($data_booking->email).'@westay.org',
            'room_id'    => $data_booking->room_id,
            'created_at' => $data_booking->created_at,
            'updated_at' => $data_booking->updated_at
        ]);
    }

    public function updateRoomCalendar($list_id)
    {
        $room = $this->room->getListCalendar($list_id);
        // dd($room);
        foreach ($room as $key => $ical_url) {
            // $ical_url = 'https://www.airbnb.com/calendar/ical/30510881.ics?s=6154494651e5aeabdccf0d4152d14ef6';
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
        }
    }

    public function updateCalendarRoomBlock($room, $data)
    {
        // dd(Carbon::parse($data[0])->addDay());
        $room_name = $this->room_translate->getRoomByListIdIndex([$room['id']]);
        // dd($room_name[0]['name']);
        $start = Carbon::parse($data[0])->startOfDay()->toDateTimeString();
        $end = isset($data[1]) ? Carbon::parse($data[1])->startOfDay()->toDateTimeString() : Carbon::parse($data[0])->addDay()->toDateTimeString();
        $room_name      = $room[0]['name'];
        $room_blocked   = $this->model->where('room_id', $room['id'])->where('type', RoomCalendar::BLOCKED)->where('starts', $start)->where('ends', $end)->get();
        foreach ($room_blocked as $k => $val) {
            $val->forceDelete();
        }
        $this->model->firstOrCreate([
            'name'       => 'Not available',
            'starts'     => $start,
            'ends'       => $end,
            'summary'    => 'Not available',
            'status'     => null,
            'location'   => null,
            'type'       => RoomCalendar::BLOCKED,
            'room_id'    => $room['id'],
            // 'created_at' => $data_booking->created_at,
            // 'updated_at' => $data_booking->updated_at
        ], ['uid'        => Crypt::encrypt($room_name[0]['name']).'@westay.org',]);
    }
}
