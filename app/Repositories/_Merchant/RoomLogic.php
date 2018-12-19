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

    public function store($data, $room = [],$list = [])
    {

        if (isset($data['basic']) & !empty($data['basic']))
        {
            $list = $data['basic'];
            $list['percent'] = $this->model->calculation_percent($data);
        }


        if (isset($data['prices']) & !empty($data['prices']))
        {
            $list = array_merge($list,$data['prices']);
        }

        if (isset($data['details']) & !empty($data['details']))
        {
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

       if (isset($data['details']) & !empty($data['details']))
       {
           $this->roomTranslate->storeRoomTranslate($data_room, $data);
       }

        if (isset($data['comforts']) & !empty($data['comforts']))
        {
            $this->storeRoomComforts($data_room, $data);

        }


        if (isset($data['images']) & !empty($data['images']))
        {
            $this->roomMedia->storeRoomMedia($data_room, $data);
        }

        if (isset($data['room_time_blocks']) & !empty($data['room_time_blocks']))
        {
            $this->roomTimeBlock->storeRoomTimeBlock($data_room, $data);
        }

        if (isset($data['weekday_price']) & !empty($data['weekday_price']) || isset($data['optional_prices']) & !empty($data['optional_prices']))
        {
            $this->roomOptionalPrice->storeRoomOptionalPrice($data_room, $data);
        }

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
        if (!empty($data)) {
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
        if (isset($data['basic']) & !empty($data['basic']))
        {
            $list = $data['basic'];
            $list['percent'] = $this->model->calculation_percent($data);
        }


        if (isset($data['prices']) & !empty($data['prices']))
        {
            $list = array_merge($list,$data['prices']);
        }

        if (isset($data['details']) & !empty($data['details']))
        {
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

        if (isset($data['details']) & !empty($data['details']))
        {
            $this->roomTranslate->updateRoomTranslate($data_room, $data);
        }

        if (isset($data['comforts']) & !empty($data['comforts']))
        {
            $this->storeRoomComforts($data_room, $data);

        }

        if (isset($data['weekday_price']) & !empty($data['weekday_price']) || isset($data['optional_prices']) & !empty($data['optional_prices']))
        {
            $this->roomOptionalPrice->updateRoomOptionalPrice($data_room, $data);
        }
        if (isset($data['images']) & !empty($data['images']))
        {
            $this->roomMedia->updateRoomMedia($data_room, $data);
        }

        if (isset($data['room_time_blocks']) & !empty($data['room_time_blocks']))
        {
            $this->roomTimeBlock->updateRoomTimeBlock($data_room, $data);
        }



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
        $room = parent::getById($id);
        return $this->getBlockedScheduleByRoomId($room->id);
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


    public function updateRoomOptionalPrice($data)
    {
        $room    = $this->model->getById($data['room_id']);
        $this->roomOptionalPrice->updateRoomOptionalPrice($room, $data);
        return $room;
    }





    /**
     * Tính toán rating trung bình cho từng phòng
     * @author tuananh1402 <tuananhpham1402@gmail.com>
     *
     * @param  $params
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function ratingCalculate($room_id, $reviews)
    {
        \DB::enableQueryLog();
        $room           = $this->room_model->where('id', $room_id)->with('reviews')->first();
        $denominator    = sizeof($room['reviews']);

        $current_cleanliness    = $room->avg_cleanliness * $denominator;
        $current_service        = $room->avg_service * $denominator;
        $current_quality        = $room->avg_quality * $denominator;
        $current_avg_rating     = $room->avg_avg_rating * $denominator;
        $current_valuable       = $room->avg_valuable * $denominator;
        $current_recommend      = $room->total_recommend;

        foreach ($reviews as $key => $value) {
            $current_cleanliness    += $value->cleanliness;
            $current_service        += $value->service;
            $current_quality        += $value->quality;
            $current_avg_rating     += $value->avg_rating;
            $current_valuable       += $value->valuable;
            $current_recommend      += $value->recommend;
        }
        \DB::beginTransaction();
        try {
            $room->update([
                'avg_cleanliness'   => round(($current_cleanliness / $denominator), 2),
                'avg_service'       => round(($current_service / $denominator), 2),
                'avg_quality'       => round(($current_quality / $denominator), 2),
                'avg_avg_rating'    => round(($current_avg_rating / $denominator), 2),
                'avg_valuable'      => round(($current_valuable / $denominator), 2),
                'total_review'      => $denominator + 1,
                'total_recommend'   => $current_recommend
            ]);
            \DB::commit();
        } catch (\Throwable $t) {
            \DB::rollback();
            throw $t;
        }
    }

    public function getRoomLatLong($data, $size)
    {
        return $this->model->getRoomLatLong($data, $size);
    }

    public function getRoomRecommend($size, $id)
    {
        return $this->model->getRoomRecommend($size, $id);
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param array $data
     * @return \App\Repositories\Eloquent
     * @throws \Exception
     */
    public function minorRoomUpdate($id, $data = [])
    {
        // $data['settings']= $this->model->checkValidRefund($data['settings']);
        return parent::update($id, $data);
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
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param $pageSize
     * @return mixed
     */
    public function getRoom($id,$params, $pageSize)
    {
        $booking = $this->model->getRoomById($id,$params, $pageSize);
        return $booking;
    }

}
