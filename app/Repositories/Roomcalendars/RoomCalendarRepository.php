<?php

namespace App\Repositories\Roomcalendars;

use Carbon\Carbon;
use App\Repositories\BaseRepository;
use App\Repositories\Rooms\RoomTranslateRepositoryInterface;
use Illuminate\Support\Facades\Crypt;
use App\Repositories\Rooms\RoomRepository;
use ICal\ICal;
use App\Repositories\Bookings\BookingConstant;

class RoomCalendarRepository extends BaseRepository implements RoomCalendarRepositoryInterface
{
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
        RoomRepository $room
    ) {
        $this->model            = $roomcalendar;
        $this->room_translate   = $room_translate;
        $this->room             = $room;
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
            $icalObject .=
            "BEGIN:VEVENT
            SUMMARY:$event->summary
            DTSTART:" . date(ICAL_FORMAT, strtotime($event->starts)) . "
            DTEND:" . date(ICAL_FORMAT, strtotime($event->ends)) . "
            DTSTAMP:" . date(ICAL_FORMAT, strtotime($event->created_at)) . "
            UID:$event->uid
            STATUS: TENTATIVE" . "
            DESCRIPTION:" . strtoupper($event->description) . "
            LAST-MODIFIED:" . date(ICAL_FORMAT, strtotime($event->updated_at)) . "
            LOCATION:$event->location
            END:VEVENT\n";
        }

        $icalObject .= "END:VCALENDAR";

        // Set the headers
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="cal.ics"');
        
        $icalObject = str_replace(' ', '', $icalObject);
        // dd($icalObject);
        echo $icalObject;
    }

    // public function store($data_booking, $data)
    // {
    //     $room = $this->room->getRoomByListIdIndex([$data_booking->room_id]);
    //     $room_name = $room[0]['name'];
    //     $this->model->create([
    //         'name'       => $data_booking->name .' ('. $data_booking->code.')',
    //         'starts'     => Carbon::parse($data_booking->checkin)->startOfDay(),
    //         'ends'       => Carbon::parse($data_booking->checkout)->startOfDay(),
    //         'summary'    => $data_booking->name .' '. $data_booking->code,
    //         'status'     => $data_booking->status,
    //         'location'   => $room_name,
    //         'uid'        => Crypt::encrypt($data_booking->email),
    //         'room_id'    => $data_booking->room_id,
    //         'created_at' => $data_booking->created_at,
    //         'updated_at' => $data_booking->updated_at
    //     ]);
    // }

    public function updateRoomCalendar($id)
    {
        $room = $this->room->getById($id);
        // $ical_url = $room['airbnb_calendar'];
        $ical_url = 'https://calendar.google.com/calendar/ical/tuananhpham1402%40gmail.com/public/basic.ics';
        $room_id = $room['id'];
        // https://www.airbnb.com/calendar/ical/25523766.ics?s=e27df69e82dd1d530a25fa7dc9c3d5c5
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
        dd($ical);
        $now = Carbon::now();
        foreach ($events as $event) {
            $name       = $event->summary !== 'Not available' ? $event->summary : 'Room Block';
            $dt_start   = $ical->iCalDateToDateTime($event->dtstart_array[3], false);
            $dt_end     = $ical->iCalDateToDateTime($event->dtend_array[3], false);
            $summary    = $event->summary !== 'Not available' ? $event->summary : 'Room Block';
            $status     = ($dt_start > $now) ? BookingConstant::BOOKING_CONFIRM : ((($dt_start > $now) && ($dt_end < $now)) ? BookingConstant::BOOKING_USING : BookingConstant::BOOKING_COMPLETE);
            $location   = $event->location != null ? $event->location : '' ;
            $uid        = $event->uid;
            $created_at = Carbon::now()->toDateTimeString();
            $updated_at = Carbon::now()->toDateTimeString();
            $this->model::firstOrCreate([
                'name'       => $name,
                'starts'     => $dt_start,
                'ends'       => $dt_end,
                'summary'    => $summary,
                'status'     => $status,
                'location'   => $location,
                'uid'        => $uid,
                'room_id'    => $room_id
            ]);
        }
    }
}
