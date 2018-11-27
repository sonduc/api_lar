<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 23/10/2018
 * Time: 14:19
 */

namespace App\Repositories\Rooms;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomReview extends Entity
{
    use PresentationTrait, FilterRoomReviewTrait,PresentationRoomReviewTrait, SoftDeletes;
    // Thang điểm đánh giá các tiêu chí phòng
    const DISGUSTING        = 1;
    const BAD               = 2;
    const MEDIUM            = 3;
    const VERY_GOOD         = 4;
    const GREAT             = 5;

    // Chất lượng dịch vụ
    const SERVICE
        = [
            self::DISGUSTING        => 'Không hài lòng',
            self::BAD               => 'Không được như mong muốn',
            self::MEDIUM            => 'Tạm ổn',
            self::VERY_GOOD         => 'Tốt',
            self::GREAT             => 'Tuyệt vời',
        ];
    // Độ sạch sẽ
    const CLEANLINESS
        = [
            self::DISGUSTING        => 'Không hài lòng',
            self::BAD               => 'Không được như mong muốn',
            self::MEDIUM            => 'Tạm ổn',
            self::VERY_GOOD         => 'Tốt',
            self::GREAT             => 'Tuyệt vời',
        ];

    // Tiện nghi và cơ sở vật chất

    const QUALITY
        = [
            self::DISGUSTING        => 'Không hài lòng',
            self::BAD               => 'Không được như mong muốn',
            self::MEDIUM            => 'Tạm ổn',
            self::VERY_GOOD         => 'Tốt',
            self::GREAT             => 'Tuyệt vời',
        ];

    // Xứng đáng với giá tiền.
    const VALUABLE
        = [
            self::DISGUSTING        => 'Rất không xứng đáng',
            self::BAD               => 'Không xứng đáng ',
            self::MEDIUM            => 'Tạm ổn',
            self::VERY_GOOD         => 'Xứng đáng',
            self::GREAT             => 'Rất xứng đáng',
        ];
    //
    const AVG_RATING
        = [
            self::DISGUSTING        => 'Không hài lòng',
            self::BAD               => 'Không được như mong muốn',
            self::MEDIUM            => 'Tạm ổn',
            self::VERY_GOOD         => 'Tốt',
            self::GREAT             => 'Tuyệt vời',
        ];


    const AVAILABLE    = 1;
    const UNAVAILABLE  = 0;

    // Trang thái đánh gia.
    const ROOM_REVIEW_STATUS    = [
        self::AVAILABLE      => 'HIỆN',
        self::UNAVAILABLE    => 'ẨN',
    ];

    // Trang thái giới thieu
    const RECOMMEND    = [
        self::AVAILABLE      => 'GIỚI THIỆU',
        self::UNAVAILABLE    => 'KHÔNG GIỚI THIỆU',
    ];

    //// Trang thái like
    const LIKE   = [
        self::AVAILABLE      => 'THÍCH',
        self::UNAVAILABLE    => 'KHÔNG THÍCH',
    ];


    protected $table = 'room_reviews';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'room_id', 'booking_id', 'user_id', 'status','avg_rating','cleanliness','quality','service','comment','recommend','valuable','like'
        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }
}
