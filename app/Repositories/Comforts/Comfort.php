<?php

namespace App\Repositories\Comforts;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comfort extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id', 'icon'
    ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    // public function rooms()
    // {
    //     return $this->belongsTo(\App\Repositories\Rooms\Room::class, 'room_id', 'id');
    // }

    public function comfortTrans()
    {
        return $this->hasMany(\App\Repositories\Comforts\ComfortTranslate::class, 'comfort_id');
    }
}
