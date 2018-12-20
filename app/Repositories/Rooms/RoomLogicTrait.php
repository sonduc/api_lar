<?php

namespace App\Repositories\Rooms;

use App\Repositories\Bookings\BookingConstant;
use App\Repositories\Bookings\BookingRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\Exceptions\InvalidDateException;

/**
 * Share the Room
 * Trait RoomLogicTrait
 * @package App\Repositories\Rooms
 */
trait RoomLogicTrait
{
    protected $roomTranslate;
    protected $roomOptionalPrice;
    protected $roomMedia;
    protected $roomTimeBlock;
    protected $booking;
    protected $roomReview;
    protected $user;
    protected $room_model;

    public function getBlockedScheduleByRoomId($id)
    {
        $data_booking = $this->booking->getFutureBookingByRoomId($id);
        $data_block   = $this->roomTimeBlock->getFutureRoomTimeBlockByRoomId($id);
        $list         = [];

        // Danh sách các ngày bị block do đã có booking
        foreach ($data_booking as $item) {
            $CI     = Carbon::createFromTimestamp($item->checkin);
            $CO     = Carbon::createFromTimestamp($item->checkout);
            $period = CarbonPeriod::between($CI, $CO);

            foreach ($period as $day) {
                $list[] = $day;
            }
        }
        // Danh sách các ngày block chủ động
        foreach ($data_block as $item) {
            $period = CarbonPeriod::between($item->date_start, $item->date_end);
            foreach ($period as $day) {
                $list[] = $day;
            }
        }

        $list = array_map(function (Carbon $item) {
            if ($item >= Carbon::now()) {
                return $item->toDateString();
            }
        }, $list);

        $list = array_filter($list);
        array_splice($list, 0, 0);
        return $list;
    }

    /**
     * Lưu comforts cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $data_room
     * @param $data
     */
    public function storeRoomComforts($data_room, $data)
    {
        if (!empty($data)) {
            if (isset($data['comforts'])) {
                $data_room->comforts()->sync($data['comforts']);
            }
        }
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $data
     * @return mixed
     */
    public function updateRoomOptionalPrice($data)
    {
        $room    = $this->model->getById($data['room_id']);
        $this->roomOptionalPrice->updateRoomOptionalPrice($room, $data);
        return $room;
    }


    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $data
     * @return \App\Repositories\Eloquent
     * @throws \Exception
     */
    public function updateRoomSettings($data)
    {
        $data['settings']= $this->model->checkValidRefund($data['settings']);
        return parent::update($data['room_id'], $data);
    }

    /**
     * Cập nhật khóa phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $data
     */
    public function updateRoomTimeBlock($data)
    {
        $data    = collect($data);
        $room_id = $data->get('room_id');
        $room    = $this->model->getById($room_id);
        $this->roomTimeBlock->updateRoomTimeBlock($room, $data->all());
        return $room;
    }
}


