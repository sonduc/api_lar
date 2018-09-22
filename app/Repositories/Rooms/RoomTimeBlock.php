<?php

namespace App\Repositories\Rooms;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomTimeBlock extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;
    
    
    protected $table = 'room_time_blocks';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'room_id', 'time_block', 'status',
        ];
    
    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];
    
}
