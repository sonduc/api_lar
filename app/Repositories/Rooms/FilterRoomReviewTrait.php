<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 31/10/2018
 * Time: 11:29
 */

namespace App\Repositories\Rooms;


use App\Repositories\GlobalTrait;

trait FilterRoomReviewTrait
{
    use GlobalTrait;

    /**
     * Room_ReView
     */


    /**
     * Scope Booking
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeBooking($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('room_reviews.booking_id', $q);
        }
        return $query;
    }

    /**
     * Scope Room
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeRoom($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('room_reviews.room_id', $q);
        }
        return $query;
    }


    /**
     * Scope User
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeUser($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('room_reviews.user_id', $q);
        }
        return $query;
    }

    /**
     * Scope Date
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */
    public function scopeDateStart($query, $q)
    {
        if ($q) {
            $query->where('room_reviews.created_at', '>=', $q);
        }
        return $query;

    }

    public function scopeDateEnd($query, $q)
    {
        if ($q) {
            $query->where('room_reviews.created_at', '<=', $q);
        }
        return $query;

    }

    /**
     * Scope theo cách tính năng
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */

    public function scopeCleanliness($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('room_reviews.cleanliness', '=', $q);
        }
        return $query;

    }

    public function scopeService($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('room_reviews.service', '=', $q);
        }
        return $query;

    }

    public function scopeValuable($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('room_reviews.valuable', '=', $q);
        }
        return $query;

    }

    public function scopeQuality($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('room_reviews.quality', '=', $q);
        }
        return $query;

    }

    /**
     * Scope theo avg_rating
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */

    public function scopeAvgRating($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('room_reviews.quality', '>=', $q);
        }
        return $query;

    }

    /**
     * Scrope Recommend
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */

    public function scopeRecommend($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('room_reviews.recommend', '=', $q);
        }
        return $query;

    }

    /**
     * Scope Like
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $query
     * @param $q
     *
     * @return mixed
     */

    public function scopeLike($query, $q)
    {
        if (is_numeric($q)) {
            $query->where('room_reviews.like', '=', $q);
        }
        return $query;

    }


}
