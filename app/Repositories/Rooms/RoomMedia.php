<?php

namespace App\Repositories\Rooms;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomMedia extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    const ARTICLE_IMG = 1;
    const THUMBNAIL   = 2;
    const COVER       = 3;
    const AVATAR      = 4;

    const IMAGE_TYPE
        = [
            self::ARTICLE_IMG => 'Ảnh cho bài viết',
            self::THUMBNAIL   => 'Ảnh thumbnail',
            self::COVER       => 'Ảnh bìa',
            self::AVATAR      => 'Ảnh đại diện',
        ];

    protected $table = 'room_medias';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'room_id', 'image', 'type', 'status',
        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];
}
