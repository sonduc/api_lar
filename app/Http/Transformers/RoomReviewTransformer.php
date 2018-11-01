<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 30/10/2018
 * Time: 13:35
 */

namespace App\Http\Transformers;


use App\Repositories\Rooms\FilterRoomReviewTrait;
use App\Repositories\Rooms\RoomReview;
use League\Fractal\TransformerAbstract;

class RoomReviewTransformer extends TransformerAbstract
{
    use FilterRoomReviewTrait;
    protected $availableIncludes
        = [

        ];

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param RoomReview|null $room
     *
     * @return array
     */
    public function transform(RoomReview $room = null)
    {
        if (is_null($room)) {
            return [];
        }

        return [
            'id'              => $room->id,
            'booking_id'      => $room->booking_id,
            'room_id'         => $room->room_id,
            'user_id'         => $room->id,
            'status'          => $room->status,
            'status_txt'      => $room->reviewStatus(),
            'like'            => $room->like,
            'like_txt'        => $room->reviewLike(),
            'recommend'       => $room->recommend,
            'recommend_txt'   => $room->reviewRecommend(),
            'comment'         => $room->comment,
            'cleanliness'     => $room->cleanliness,
            'cleanliness_txt' => $room->reviewCleanliness(),
            'service'         => $room->service,
            'service_txt'     => $room->reviewService(),
            'quality'         => $room->quality,
            'quality_txt'     => $room->reviewQuality(),
            'valuable'        => $room->valuable,
            'valuable_txt'    => $room->reviewValuable(),
            'avg_rating'      => $room->avg_rating ?? "Chưa đánh giá",
            'created_at'      => $room->created_at->format('Y-m-d H:m:i'),
            'updated_at'      => $room->updated_at->format('Y-m-d H:m:i'),
            'rating_room'     => $room->avgRating(),
            'total_like'      => $room->sumLike(),
        ];
    }

}
