<?php

namespace App\Repositories\Bookings;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingCancel extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    const OTHER               = 2;
    const HOST_DECLINE        = 5;
    const ROOM_FULL           = 6;
    const UNQUALIFIED         = 7;
    const LOOK_AROUND         = 8;
    const GUEST_BUSY          = 10;
    const NO_RESPONSE         = 11;
    const STAFF_SLOW_RESPONSE = 14;
    const RUSHING             = 15;


    protected $table = 'booking_cancel';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'booking_id', 'code', 'note',
    ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    /**
     * Lấy các mã hủy phòng cố định
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return array
     */
    public static function getBookingCancel()
    {
        return [
            self::OTHER               => trans2('booking/cancel.other'),
            self::HOST_DECLINE        => trans2('booking/cancel.host_decline'),
            self::ROOM_FULL           => trans2('booking/cancel.room_full'),
            self::UNQUALIFIED         => trans2('booking/cancel.unqualified'),
            self::LOOK_AROUND         => trans2('booking/cancel.look_around'),
            self::GUEST_BUSY          => trans2('booking/cancel.guest_busy'),
            self::NO_RESPONSE         => trans2('booking/cancel.no_response'),
            self::STAFF_SLOW_RESPONSE => trans2('booking/cancel.staff_slow'),
            self::RUSHING             => trans2('booking/cancel.rushing'),
        ];
    }
}
