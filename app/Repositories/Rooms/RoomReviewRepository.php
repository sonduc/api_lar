<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 23/10/2018
 * Time: 14:20
 */

namespace App\Repositories\Rooms;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class RoomReviewRepository extends BaseRepository implements RoomReviewRepositoryInterface
{
    use PresentationRoomReviewTrait;

    protected $model;

    public function __construct(RoomReview $roomReview)
    {
        $this->model = $roomReview;
    }

    /**
     * Kiểm tra xem người này có đươch quyền review phòng hay không
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @return mixed
     */
    public function checkReview($data_booking,$user)
    {
        $data    = $this->model->where([
            ['booking_id',$data_booking->id],
        ])->first();

        $customer_id = $user->id;


        if ($customer_id != $data_booking->customer_id)
        {
            throw new \Exception('Bạn không có quyền review về phòng này');
        }

        if (!empty($data)) {
            throw  new \Exception('Phòng này bạn đã đánh giá rồi !!!');
        }

    }


}
