<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 23/10/2018
 * Time: 14:20
 */

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;

class RoomReviewRepository extends BaseRepository implements RoomReviewRepositoryInterface
{
    use PresentationRoomReviewTrait;

    protected $model;

    public function __construct(RoomReview $roomReview)
    {
        $this->model = $roomReview;
    }

    /**
     * Kiểm tra xem booking đã từng đánh giá review hay chưa
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @return mixed
     */
    public function getBookingByID($id)
    {
        $data    = $this->model->where([
            ['booking_id',$id],
        ])->first();
        return $data;
    }
}
