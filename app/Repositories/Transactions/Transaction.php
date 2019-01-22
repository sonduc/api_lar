<?php

namespace App\Repositories\Transactions;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'credit',
        'debit',
        'date_create',
        'user_id',
        'booking_id',
        'room_id',
        'bonus',
        'comission',
    ];

    const PENDING        = 0;
    const COMBINED       = 1;

    const STATUS = [
        self::PENDING    => 'Đang chờ',
        self::COMBINED   => 'Đã tạo đối soát'
    ];


    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function booking()
    {
        return $this->belongsTo(\App\Repositories\Bookings\Booking::class, 'booking_id');
    }

    public function room()
    {
        return $this->belongsTo(\App\Repositories\Rooms\Room::class, 'room_id');
    }
}
