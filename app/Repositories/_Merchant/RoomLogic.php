<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 13/12/2018
 * Time: 13:46
 */

namespace App\Repositories\_Merchant;

use App\Repositories\BaseLogic;
use App\Repositories\Bookings\BookingRepository;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Repositories\Rooms\Room;
use App\Repositories\Rooms\RoomLogicTrait;
use App\Repositories\Rooms\RoomMediaRepositoryInterface;
use App\Repositories\Rooms\RoomOptionalPriceRepositoryInterface;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\Rooms\RoomReviewRepositoryInterface;
use App\Repositories\Rooms\RoomTimeBlockRepositoryInterface;
use App\Repositories\Rooms\RoomTranslateRepositoryInterface;
use App\Repositories\Users\UserRepository;
use App\Repositories\Users\UserRepositoryInterface;
use App\Events\GenerateWestayRoomCalendarEvent;
use Illuminate\Support\Facades\Auth;

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
    protected $roomReview;
    protected $user;
    protected $room_model;

    /**
     * RoomLogic constructor.
     *
     * @param RoomRepositoryInterface|RoomRepository                           $room
     * @param RoomTranslateRepositoryInterface|RoomTranslateRepository         $roomTranslate
     * @param RoomOptionalPriceRepositoryInterface|RoomOptionalPriceRepository $roomOptionalPrice
     * @param RoomMediaRepositoryInterface|RoomMediaRepository                 $roomMedia
     * @param RoomTimeBlockRepositoryInterface|RoomTimeBlockRepository         $roomTimeBlock
     * @param BookingRepositoryInterface|BookingRepository                     $booking
     * @param RoomReviewRepositoryInterface|RoomReviewRepository               $roomReview
     * @param UserRepositoryInterface|UserRepository                           $user
     */
    public function __construct(
        RoomRepositoryInterface $room,
        Room $room_model,
        RoomTranslateRepositoryInterface $roomTranslate,
        RoomOptionalPriceRepositoryInterface $roomOptionalPrice,
        RoomMediaRepositoryInterface $roomMedia,
        RoomTimeBlockRepositoryInterface $roomTimeBlock,
        BookingRepositoryInterface $booking,
        RoomReviewRepositoryInterface $roomReview,
        UserRepositoryInterface $user
    ) {
        $this->room_model        = $room_model;
        $this->model             = $room;
        $this->roomTranslate     = $roomTranslate;
        $this->roomOptionalPrice = $roomOptionalPrice;
        $this->roomMedia         = $roomMedia;
        $this->roomTimeBlock     = $roomTimeBlock;
        $this->booking           = $booking;
        $this->roomReview        = $roomReview;
        $this->user              = $user;
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $data
     * @param array $room
     * @return \App\Repositories\Eloquent
     */

    public function store($data, $room = [], $list = [])
    {
        if (isset($data['basic']) & !empty($data['basic'])) {
            $list = $data['basic'];
            $list['percent'] = $this->model->calculation_percent($data);
        }


        if (isset($data['prices']) & !empty($data['prices'])) {
            $list = array_merge($list, $data['prices']);
        }

        if (isset($data['details']) & !empty($data['details'])) {
            $list['city_id']     = $data['details']['city_id'];
            $list['district_id'] = $data['details']['district_id'];
            $list['longitude']   = $data['details']['longitude'];
            $list['latitude']    = $data['details']['latitude'];
        }
        $list['merchant_id']     = Auth::user()->id;
        $list['standard_point']  = 0;
        $list['status']          = Room::NOT_APPROVED;

        $list['settings']        = $this->model->checkValidRefund($data);

        $data_room = parent::store($list);

        if (isset($data['details']) & !empty($data['details'])) {
            $this->roomTranslate->storeRoomTranslate($data_room, $data);
        }

        if (isset($data['comforts']) & !empty($data['comforts'])) {
            $this->storeRoomComforts($data_room, $data);
        }


        if (isset($data['images']) & !empty($data['images'])) {
            $this->roomMedia->storeRoomMedia($data_room, $data);
        }

        if (isset($data['room_time_blocks']) & !empty($data['room_time_blocks'])) {
            $this->roomTimeBlock->storeRoomTimeBlock($data_room, $data);
        }

        if (isset($data['weekday_price']) & !empty($data['weekday_price']) || isset($data['optional_prices']) & !empty($data['optional_prices'])) {
            $this->roomOptionalPrice->storeRoomOptionalPrice($data_room, $data);
        }
        
        event(new GenerateWestayRoomCalendarEvent($data_room));

        return $data_room;
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
        if (isset($data['basic']) & !empty($data['basic'])) {
            $list = $data['basic'];
            $list['percent'] = $this->model->calculation_percent($data);
        }


        if (isset($data['prices']) & !empty($data['prices'])) {
            $list = array_merge($list, $data['prices']);
        }

        if (isset($data['details']) & !empty($data['details'])) {
            $list['city_id']     = $data['details']['city_id'];
            $list['district_id'] = $data['details']['district_id'];
            $list['longitude']   = $data['details']['longitude'];
            $list['latitude']    = $data['details']['latitude'];
        }
        $list['merchant_id']     = Auth::user()->id;
        $list['standard_point']  = 0;
        $list['status']          = Room::NOT_APPROVED;
        $list['settings']        = $this->model->checkValidRefund($data);

        // dd($data['settings']);
        $data_room = parent::update($id, $list);

        if (isset($data['details']) & !empty($data['details'])) {
            $this->roomTranslate->updateRoomTranslate($data_room, $data);
        }

        if (isset($data['comforts']) & !empty($data['comforts'])) {
            $this->storeRoomComforts($data_room, $data);
        }

        if (isset($data['weekday_price']) & !empty($data['weekday_price']) || isset($data['optional_prices']) & !empty($data['optional_prices'])) {
            $this->roomOptionalPrice->updateRoomOptionalPrice($data_room, $data);
        }
        if (isset($data['images']) & !empty($data['images'])) {
            $this->roomMedia->updateRoomMedia($data_room, $data);
        }

        if (isset($data['room_time_blocks']) & !empty($data['room_time_blocks'])) {
            $this->roomTimeBlock->updateRoomTimeBlock($data_room, $data);
        }

        return $data_room;
    }

    /**
     * Lấy tất cả các phòng thuộc quản lí của merchant
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param $pageSize
     * @return mixed
     */
    public function getRoom($id, $params, $pageSize)
    {
        $booking = $this->model->getRoomByMerchantId($id, $params, $pageSize);
        return $booking;
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
}
