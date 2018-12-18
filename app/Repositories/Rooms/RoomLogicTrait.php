<?php

namespace App\Repositories\Rooms;

use App\Repositories\Bookings\BookingConstant;
use App\Repositories\Bookings\BookingRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\Exceptions\InvalidDateException;


/**
 * Share the Room
 * Trait RoomLogicTrait
 * @package App\Repositories\Rooms
 */
trait RoomLogicTrait
{

    protected $roomTranslate;
    protected $roomOptionalPrice;
    protected $roomMedia;
    protected $roomTimeBlock;
    protected $booking;
    protected $roomReview;
    protected $user;
    protected $room_model;

}
