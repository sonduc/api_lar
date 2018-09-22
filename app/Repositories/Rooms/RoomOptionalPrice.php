<?php

namespace App\Repositories\Rooms;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomOptionalPrice extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;
    
    
    const AVAILABLE   = 1;
    const UNAVAILABLE = 0;
    protected $table = 'room_optional_prices';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'room_id', 'weekday', 'day', 'price_day', 'price_hour', 'price_after_hour', 'price_charge_guest', 'status',
        ];
    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];
    
    
}
