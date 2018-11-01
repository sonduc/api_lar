<?php

namespace App\Repositories\Rooms;


use App\Repositories\BaseLogic;
use App\Repositories\Bookings\BookingRepository;
use App\Repositories\Bookings\BookingRepositoryInterface;


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
    )
    {
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
    public function store($data,$list = [])
    {
        $check_review = $this->model->getBookingByID($data['booking_id']);
        if (!empty($check_review)) throw  new \Exception('Bạn đã từng đánh giá phòng này nên chỉ có quyền sửa đổi');
        $list_booking = $this->booking->getBookingByCheckout($data['booking_id']);
        if (!empty($data))
        {
            $data['user_id'] = $list_booking->customer_id;
            $data['room_id'] = $list_booking->room_id;
        }
        return parent::store($data);
    }

    /**
     * Update room_reviews
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param $data
     * @param array $excepts
     * @param array $only
     * @return \App\Repositories\Eloquent
     */
    public function update($id, $data, $excepts = [], $only = [])
    {
        return parent::update($id, $data);
    }


}
