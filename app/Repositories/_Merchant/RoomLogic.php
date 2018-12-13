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
