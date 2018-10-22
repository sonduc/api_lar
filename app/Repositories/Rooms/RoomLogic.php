<?php

namespace App\Repositories\Rooms;


use App\Repositories\BaseLogic;
use App\Repositories\Bookings\BookingRepository;
use App\Repositories\Bookings\BookingRepositoryInterface;

class RoomLogic extends BaseLogic
{
    use RoomLogicTrait;
    /**
     * Room model.
     * @var Room
     */
    protected $roomTranslate;
    protected $roomOptionalPrice;
    protected $roomMedia;
    protected $roomTimeBlock;
    protected $booking;

    /**
     * RoomLogic constructor.
     *
     * @param RoomRepositoryInterface|RoomRepository                           $room
     * @param RoomTranslateRepositoryInterface|RoomTranslateRepository         $roomTranslate
     * @param RoomOptionalPriceRepositoryInterface|RoomOptionalPriceRepository $roomOptionalPrice
     * @param RoomMediaRepositoryInterface|RoomMediaRepository                 $roomMedia
     * @param RoomTimeBlockRepositoryInterface|RoomTimeBlockRepository         $roomTimeBlock
     * @param BookingRepositoryInterface|BookingRepository                     $booking
     */
    public function __construct(
        RoomRepositoryInterface $room,
        RoomTranslateRepositoryInterface $roomTranslate,
        RoomOptionalPriceRepositoryInterface $roomOptionalPrice,
        RoomMediaRepositoryInterface $roomMedia,
        RoomTimeBlockRepositoryInterface $roomTimeBlock,
        BookingRepositoryInterface $booking
    )
    {
        $this->model             = $room;
        $this->roomTranslate     = $roomTranslate;
        $this->roomOptionalPrice = $roomOptionalPrice;
        $this->roomMedia         = $roomMedia;
        $this->roomTimeBlock     = $roomTimeBlock;
        $this->booking           = $booking;
    }

    /**
     * Lưu trữ bản ghi của phòng vào bảng rooms, room_translates, room_optional_prices, room_comfort
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function store($data)
    {
        $data_room = parent::store($data);

        $this->roomTranslate->storeRoomTranslate($data_room, $data);
        $this->roomOptionalPrice->storeRoomOptionalPrice($data_room, $data);
        $this->roomMedia->storeRoomMedia($data_room, $data);
        $this->roomTimeBlock->storeRoomTimeBlock($data_room, $data);
        $this->storeRoomComforts($data_room, $data);


        return $data_room;
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
        if (!empty ($data)) {
            if (isset($data['comforts'])) {
                $data_room->comforts()->sync($data['comforts']);
            }
        }
    }

    /**
     * Cập nhật cho phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param int   $id
     * @param       $data
     * @param array $excepts
     * @param array $only
     *
     * @return \App\Repositories\Eloquent
     */
    public function update($id, $data, $excepts = [], $only = [])
    {
        $data_room = parent::update($id, $data);

        $this->roomTranslate->updateRoomTranslate($data_room, $data);
        $this->roomOptionalPrice->updateRoomOptionalPrice($data_room, $data);
        $this->roomMedia->updateRoomMedia($data_room, $data);
        $this->roomTimeBlock->updateRoomTimeBlock($data_room, $data);
        $this->storeRoomComforts($data_room, $data);

        return $data_room;
    }

    /**
     * Cập nhật riêng lẻ các thuộc tính của phòng
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $id
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function minorRoomUpdate($id, $data = [])
    {
        return parent::update($id, $data);
    }

    /**
     * Lấy ra những ngày không hợp lệ
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     *
     * @return array
     */
    public function getFutureRoomSchedule($id)
    {
        return $this->getBlockedScheduleByRoomId($id);
    }
}
