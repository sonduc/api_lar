<?php

namespace App\Repositories\Bookings;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingStatus extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    protected $table = 'booking_status';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    protected $fillable = [
        'staff_id', 'booking_id', 'note'
    ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];


}
