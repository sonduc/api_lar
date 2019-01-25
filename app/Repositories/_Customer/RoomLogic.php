<?php

namespace App\Repositories\_Customer;

use App\Repositories\BaseLogic;
use App\Repositories\Bookings\BookingRepository;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Repositories\Rooms\RoomLogicTrait;
use App\Repositories\Rooms\RoomRepository;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Rooms\RoomTimeBlockRepositoryInterface;

class RoomLogic extends BaseLogic
{
    use RoomLogicTrait;
    protected $booking;
    protected $roomTimeBlock;

    /**
     * RoomLogic constructor.
     *
     * @param RoomRepositoryInterface|RoomRepository       $model
     * @param BookingRepositoryInterface|BookingRepository $booking
     */
    public function __construct(
        RoomRepositoryInterface $model,
        BookingRepositoryInterface $booking,
        RoomTimeBlockRepositoryInterface $roomTimeBlock
    ) {
        $this->model         = $model;
        $this->booking       = $booking;
        $this->roomTimeBlock = $roomTimeBlock;
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
    public function getRooms($params, $pageSize = null,$count= null)
    {
        $collect_params = collect($params);
        $check_in       = $collect_params->get('check_in');
        $check_out      = $collect_params->get('check_out');
        $booking        = $this->booking->getAllBookingInPeriod($check_in, $check_out);
        $list_room_id   = $booking->map(function ($item) {
            return $item->room_id;
        })->all();

        if ($count === 'standard_point')
        {
            $rooms = $this->model->getAllRoomExceptListId($list_room_id, $params, $pageSize,$count);
        }elseif ($count ==='comfort_lists')
        {
            $rooms = $this->model->getAllRoomExceptListId($list_room_id, $params, $pageSize,$count);
        }
        elseif($count === null)
        {
            $rooms = $this->model->getAllRoomExceptListId($list_room_id, $params, $pageSize,$count);
        }
        return $rooms;
    }

    public function getRoomsByStandardPoint($params, $pageSize = 10)
    {
        $collect_params = collect($params);
        $check_in       = $collect_params->get('check_in');
        $check_out      = $collect_params->get('check_out');
        $booking        = $this->booking->getAllBookingInPeriod($check_in, $check_out);
        $list_room_id   = $booking->map(function ($item) {
            return $item->room_id;
        })->all();

        $rooms = $this->model->getAllRoomExceptListId($list_room_id, $params, $pageSize);

        return $rooms;
    }


    /**Lấy ra những ngày đã bị block của một phòng
     * Lấy ra những ngày
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     *
     * @return array
     */

    public function getFutureRoomSchedule($id)
    {
        $room = parent::getById($id);
        return $this->getBlockedScheduleByRoomId($room->id);
    }

    /**
     * Lấy ra những khoảng giờ không hợp lệ
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     *
     * @return array
     */
    public function getFutureRoomScheduleByHour($id)
    {
        $room = parent::getById($id);
        return $this->getBlockedScheduleByHour($room->id);
    }

    public function getRoomLatLong($data, $size)
    {
        return $this->model->getRoomLatLong($data, $size);
    }

    public function getRoomRecommend($size, $id)
    {
        return $this->model->getRoomRecommend($size, $id);
    }

    public function countNumberOfRoomByCity($limit = null)
    {
        return $this->model->countNumberOfRoomByCity($limit);

    }
}
