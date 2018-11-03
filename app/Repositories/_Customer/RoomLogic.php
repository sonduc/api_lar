<?php

namespace App\Repositories\_Customer;


use App\Repositories\BaseLogic;
use App\Repositories\Bookings\BookingRepository;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Repositories\Rooms\RoomRepository;
use App\Repositories\Rooms\RoomRepositoryInterface;

class RoomLogic extends BaseLogic
{
    public $booking;

    /**
     * RoomLogic constructor.
     *
     * @param RoomRepositoryInterface|RoomRepository       $model
     * @param BookingRepositoryInterface|BookingRepository $booking
     */
    public function __construct(
        RoomRepositoryInterface $model,
        BookingRepositoryInterface $booking
    )
    {
        $this->model   = $model;
        $this->booking = $booking;
    }

    /**
     * Lấy danh sách phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param     $params
     * @param int $pageSize
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function getRooms($params, $pageSize = 5)
    {
        $booking = $this->booking->getAllBookingInPeriod($params['check_in'], $params['check_out']);

        $list_room_id = $booking->map(function ($item) {
            return $item->room_id;
        })->all();

        $rooms = $this->model->getAllRoomExceptListId($list_room_id, $params, $pageSize);

        return $rooms;
    }
}