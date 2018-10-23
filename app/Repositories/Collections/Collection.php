<?php

namespace App\Repositories\Collections;

use App\Repositories\Entity;
use App\Repositories\Rooms\Room;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;
    // Định nghĩa trạng thái collection
    const AVAILABLE    = 1;
    const UNAVAILABLE  = 0;
    const COLLECTION_STATUS    = [
        self::AVAILABLE      => 'ĐÃ DUYỆT',
        self::UNAVAILABLE    => 'ĐANG CHỜ DUYỆT',
    ];
    const COLLECTION_HOT    = [
        self::AVAILABLE      => 'NỔI BẬT',
        self::UNAVAILABLE    => 'KHÔNG NỔI BẬT',
    ];
    const COLLECTION_NEW    = [
        self::AVAILABLE      => 'MỚI',
        self::UNAVAILABLE    => 'CŨ',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hot','new','image','status',
    ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];


    /**
     * relation ship voi blogs_translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function CollectionTrans()
    {
        return $this->hasMany(CollectionTranslate::class, 'collection_id');
    }

    /**
     * ralation ship với rooms
     * @author ducchien612 <0ducchien612@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'collection_rooms', 'collection_id', 'room_id');
    }
}
