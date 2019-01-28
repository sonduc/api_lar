<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/28/2019
 * Time: 2:52 PM
 */

namespace App\Repositories\HostReviews;


use App\Repositories\BaseRepository;
use App\Repositories\Bookings\BookingRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class HostReviewRepository extends BaseRepository implements HostReviewRepositoryInterface
{
    /**
     * @var Setting
     */
    protected $model;
    protected $booking;

    /**
     * HostReviewRepository constructor.
     * @param HostReview $hostReview
     */
    public function __construct(
        HostReview $hostReview,
        BookingRepositoryInterface $booking
    ) {
        $this->model          = $hostReview;
        $this->booking        = $booking;
    }

    public function store($data = [])
    {
        $merchant_id = Auth::user()->id;
        $data_booking = $this->booking->getBookingById($data['booking_id']);
//        if ($merchant_id != $data_booking->merchant_id)
//        {
//            throw new \Exception('Bạn không có quyền review về khách hàng này');
//        }
        $data['customer_id']    = $data_booking->customer_id;
        $data['room_id']        = $data_booking->room_id;
        $data['merchant_id']    = $data_booking->merchant_id;
        $data['booking_id']     = $data_booking->booking_id;
        $data['checkin']        = $data_booking->booking_id;
        $data['checkout']       = $data_booking->booking_id;
        return parent::store($data);
    }

    public function checkReview()
    {

    }

}