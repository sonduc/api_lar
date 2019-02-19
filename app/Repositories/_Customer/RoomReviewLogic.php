<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 2/1/2019
 * Time: 12:13 AM
 */

namespace App\Repositories\_Customer;


use App\Repositories\BaseLogic;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Repositories\Rooms\Room;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Rooms\RoomReviewRepositoryInterface;
use Illuminate\Http\Request;

class RoomReviewLogic extends BaseLogic
{
    protected $room;
    protected $booking;

    /**
     * RoomReviewLogic constructor.
     *
     * @param RoomReviewRepositoryInterface|RoomReviewRepository $roomReview
     * @param RoomRepositoryInterface|RoomRepository             $room
     * @param BookingRepositoryInterface|BookingRepository       $booking
     */
    public function __construct(
        RoomReviewRepositoryInterface $roomReview,
        RoomRepositoryInterface $room,
        BookingRepositoryInterface $booking
    ) {
        $this->model   = $roomReview;
        $this->room    = $room;
        $this->booking = $booking;
    }

    /**
     * Thêm mới bản ghi cho Room_ReView
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $data
     * @param array $list
     * @return \App\Repositories\Eloquent
     * @throws \Exception
     */
    public function store($data)
    {
        $data_booking=$this->booking->checkBooking($data['booking_id'])->toArray();

        if (!empty($data)) {
            $data['user_id']        = $data_booking['customer_id'];
            $data['room_id']        = $data_booking['room_id'];
            $data['booking_id']     = $data_booking['id'];
        }
        $room_review = parent::store($data);

        // Cập nhậttrạng thái đã review của booking
        $data_booking['status_reviews'] = 1;
        $this->booking->updateBooking($room_review->booking_id,$data_booking);
        return $room_review;
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     */
    public function getRoom($booking_id,$user)
    {
        $booking_id = (int)$booking_id;
        $data_booking=$this->booking->checkBooking($booking_id);

        $this->model->checkReview($data_booking,$user);
        $data_room = $this->room->getRoomForReview($data_booking->room_id)->toArray();
        $data_room['room_type_text']=Room::ROOM_TYPE[$data_room['room_type']] ? Room::ROOM_TYPE[$data_room['room_type']] : 'Không xác định';
        $data_room['booking_id']    = $booking_id;
        return $data_room;
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $booking_id
     * @return mixed
     */
    public function show($booking_id)
    {
       return $this->model->getRoomReviewByBookingId($booking_id);
    }

    public function getRoomForShowReview($room_id)
    {
        $data_room = $this->room->getRoomForReview($room_id)->toArray();
        $data_room['room_type_text']=Room::ROOM_TYPE[$data_room['room_type']] ? Room::ROOM_TYPE[$data_room['room_type']] : 'Không xác định';
        return $data_room;
    }

}