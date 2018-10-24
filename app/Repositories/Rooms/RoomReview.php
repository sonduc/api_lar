<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 23/10/2018
 * Time: 14:19
 */

namespace App\Repositories\Rooms;


use App\Repositories\Entity;

class RoomReview extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;



    protected $table = 'room_reviews';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'room_id', 'booking_id', 'user_id', 'status','avg_rating','cleanliness','quality','service','comment','recommend','valueble'
        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

}
