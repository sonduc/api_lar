<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 31/10/2018
 * Time: 12:02
 */

namespace App\Repositories\Rooms;

trait PresentationRoomReviewTrait
{

    /**
     * Tính tổng rating theo phòng
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $room
     * @param $id
     * @return mixed
     */
    public function avgRating()
    {
        return $this->where('room_id', '=', $this->room_id)->avg('avg_rating');
    }

    public function sumLike() :int
    {
        return $this->where('room_id', '=', $this->room_id)->sum('like');
    }

    public function reviewQuality()
    {
        return isset(self::QUALITY[$this->quality]) ? self::QUALITY[$this->quality] : 'Chưa đánh giá';
    }

    public function reviewService()
    {
        return isset(self::SERVICE[$this->service]) ? self::SERVICE[$this->service] : 'Chưa đánh giá';
    }

    public function reviewCleanliness()
    {
        return isset(self::CLEANLINESS[$this->cleanliness]) ? self::CLEANLINESS[$this->cleanliness] : 'Chưa đánh giá';
    }

    public function reviewValuable()
    {
        return isset(self::VALUABLE[$this->valuable]) ? self::VALUABLE[$this->valuable] : 'Chưa đánh giá';
    }

    public function reviewStatus()
    {
        return self::ROOM_REVIEW_STATUS[$this->status ?? self::UNAVAILABLE];
    }

    public function reviewLike()
    {
        return self::LIKE[$this->status ?? self::UNAVAILABLE];
    }

    public function reviewRecommend()
    {
        return self::RECOMMEND[$this->status ?? self::UNAVAILABLE];
    }
}
