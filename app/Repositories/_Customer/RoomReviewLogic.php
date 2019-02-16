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
        $data_booking=$this->booking->checkBooking($data['booking_id']);
        //$this->model->checkReview($data_booking);

        if (!empty($data)) {
            $data['user_id']        = $data_booking->customer_id;
            $data['room_id']        = $data_booking->room_id;
            $data['booking_id']     = $data_booking->id;
        }
        return parent::store($data);
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     */
    public function getRoom($booking_id)
    {
        $booking_id = (int)$booking_id;
        $data_booking=$this->booking->checkBooking($booking_id);
        $this->model->checkReview($data_booking);
        $data_room = $this->room->getRoomForReview($data_booking->room_id)->toArray();
        $data_room['room_type_text']=Room::ROOM_TYPE[$data_room['room_type']] ? Room::ROOM_TYPE[$data_room['room_type']] : 'Không xác định';
        $data_room['booking_id']    = $booking_id;
        return $data_room;

    }

}