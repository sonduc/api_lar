<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/28/2019
 * Time: 2:51 PM
 */

namespace App\Repositories\HostReviews;


use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class HostReview extends Entity
{
    use SoftDeletes;
    // Định nghĩa trạng thái seettings
//    const AVAILABLE    = 1;
//    const UNAVAILABLE  = 0;
//
//
//    const SETTING_STATUS    = [
//        self::AVAILABLE      => 'HIỂN THỊ',
//        self::UNAVAILABLE    => 'ẨN',
//    ];

    protected $table = 'host_review_customer';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'room_id','booking_id','customer_id','merchant_id','status','avg_rating',
            'cleanliness','friendly', 'comment', 'recommend','house_rules_observe','checkin','checkout'
        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

}