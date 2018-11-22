<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 23/11/2018
 * Time: 01:22
 */

namespace App\Repositories\Bookings;


use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingRefund extends Entity
{
    use SoftDeletes;
    protected $table = 'booking_refund';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'booking_id', 'days', 'refund',
    ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

}
