<?php

namespace App\Repositories\Districts;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    // Trạng thái
    const UNAVAILABLE = 0;
    const AVAILABLE   = 1;
    const STATUS
                      = [
            self::UNAVAILABLE => 'Không khả dụng',
            self::AVAILABLE   => 'Khả dụng',
        ];
    // Độ ưu tiên
    const NO_PRIORITY   = 0;
    const PRIORITY      = 1;
    const MORE_PRIORITY = 2;
    const MOST_PRIORITY = 3;
    const PRIORITIES
                        = [
            self::NO_PRIORITY   => 'Không ưu tiên',
            self::PRIORITY      => 'Được ưu tiên',
            self::MORE_PRIORITY => 'Ưu tiên khá',
            self::MOST_PRIORITY => 'Ưu tiên cao nhất',
        ];
    protected $fillable
        = [
            'city_id', 'name', 'short_name', 'code', 'priority', 'hot', 'status',
        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];


    public function rooms()
    {
        return $this->hasMany(\App\Repositories\Rooms\Room::class, 'district_id');
    }

    public function city()
    {
        return $this->belongsTo(\App\Repositories\Cities\City::class, 'city_id');
    }

    public function users()
    {
        return $this->hasMany(\App\User::class, 'district_id');
    }

}
