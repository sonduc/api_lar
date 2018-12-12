<?php

namespace App\Repositories\Roomcalendars;

use Carbon\Carbon;
use App\Repositories\BaseRepository;
use App\Repositories\Rooms\RoomTranslateRepositoryInterface;
use Illuminate\Support\Facades\Crypt;
use App\Repositories\Rooms\RoomRepositoryInterface;
use ICal\ICal;
use App\Repositories\Bookings\BookingConstant;
use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\Relationship\Attendee;
use Jsvrcek\ICS\Model\Relationship\Organizer;
use Jsvrcek\ICS\Utility\Formatter;
use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\CalendarExport;

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
        RoomRepositoryInterface $room
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
        
        echo $icalObject;
    }

    public function icalGoogleCalendar($id)
    {
        $calendar = new Calendar();
        $calendar->setProdId('-//Westay Calendar');
        $timezone = config('app.timezone');
        $timeZone = new \DateTimeZone($timezone);
        $calendar->setTimezone($timeZone);
        $calendar->setCustomHeaders([
            'X-WR-TIMEZONE' => $timezone,
            'X-WR-CALNAME' => 'Westay Calendar',
            'X-PUBLISHED-TTL' => 'PT5M'
        ]);

        // $events = RoomCalendar::all();
        $events = $this->model->where('room_id', $id)->get();
        // dd($events);
        foreach ($events as $event) {
            $calendarEvent = new CalendarEvent();
            $calendarEvent->setStart(Carbon::parse($event->starts))
                            ->setEnd(Carbon::parse($event->ends))
                            ->setSummary($event->summary)
                            ->setStatus($event->status)
                            ->setUid("event.{$event->id}");
            $calendar->addEvent($calendarEvent);
        }

        $calendarExport = new CalendarExport(new CalendarStream, new Formatter());
        $calendarExport->addCalendar($calendar);
        $this->updateRoomCalendar($id);
        //output .ics formatted text
        echo $calendarExport->getStream();
    }

    public function storeRoomCalendar($data_booking, $data)
    {
        $room = $this->room->getRoomByListIdIndex([$data_booking->room_id]);
        $room_name = $room[0]['name'];
        $this->model->create([
            'name'       => $data_booking->name .' ('. $data_booking->code.')',
            'starts'     => Carbon::parse($data_booking->checkin)->startOfDay(),
            'ends'       => Carbon::parse($data_booking->checkout)->startOfDay(),
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
            // $room_id = $room['id'];
            // dd($key);
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
            // dd($ical);
            $now = Carbon::now();
            foreach ($events as $event) {
                $dt_start   = $ical->iCalDateToDateTime($event->dtstart_array[3], false);
                // dd($dt_start > $now);
                if ($dt_start > $now) {
                    $dt_end     = $ical->iCalDateToDateTime($event->dtend_array[3], false);
                    $name       = $event->summary !== 'Not available' ? $event->summary : 'Room Block';
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
                        'status'     => 'CONFIRMED',
                        'location'   => $location,
                        'uid'        => $uid,
                        'room_id'    => $key
                    ]);
                }
            }
        }
    }
}
