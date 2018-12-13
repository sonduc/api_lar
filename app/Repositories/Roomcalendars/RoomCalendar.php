<?php

namespace App\Repositories\Roomcalendars;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomCalendar extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    public $table = 'room_calendar';

    const BLOCKED = 0;
    const BOOKED  = 1;

    const CALENDAR_TYPE = [
        self::BLOCKED   => 'Ngày bị khóa',
        self::BOOKED    => 'Ngày có booking',
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'starts',
        'ends',
        'status',
        'summary',
        'location',
        'uid',
        'room_id',
        'type'
    ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];
}
