<?php

namespace App\Repositories\Roomcalendars;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomCalendar extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    public $table = 'room_calendar';

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
        'room_id'
    ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];
}
