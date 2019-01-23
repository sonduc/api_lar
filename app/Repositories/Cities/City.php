<?php

namespace App\Repositories\Cities;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    // Vùng miền
    const NORTH_REGION   = 1;
    const MIDDLE_REGION  = 2;
    const SOUTH_REGION   = 3;
    const UNKNOWN_REGION = 0;
    const REGION
                         = [
            self::NORTH_REGION   => 'Miền Bắc',
            self::MIDDLE_REGION  => 'Miền Trung',
            self::SOUTH_REGION   => 'Miền Nam',
            self::UNKNOWN_REGION => 'Không xác định',
        ];
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

    const SEARCH_SUGGESTIONS = 6;
    protected $fillable
        = [
            'region_id', 'name', 'short_name', 'code', 'longitude', 'latitude', 'priority', 'hot', 'status',
        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    public function districts()
    {
        return $this->hasMany(\App\Repositories\Districts\District::class, 'city_id');
    }

    public function rooms()
    {
        return $this->hasMany(\App\Repositories\Rooms\Room::class, 'city_id');
    }

    public function users()
    {
        return $this->hasMany(\App\User::class, 'city_id');
    }

}
