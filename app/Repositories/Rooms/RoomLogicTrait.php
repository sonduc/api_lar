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
    /**
     * @var BookingRepository
     */
    protected $booking;
    /**
     * @var RoomTimeBlockRepository
     */
    protected $roomTimeBlock;

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
}
