<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/28/2019
 * Time: 2:52 PM
 */

namespace App\Repositories\HostReviews;


use App\Repositories\BaseRepository;
use App\Repositories\Bookings\BookingConstant;
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

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param array $data
     * @return \App\Repositories\Eloquent
     * @throws \Exception
     */
    public function store($data = [])
    {
        $data_booking = $this->booking->getBookingById($data['booking_id']);
        $this->checkReview($data_booking);

        $data['customer_id']    = $data_booking->customer_id;
        $data['room_id']        = $data_booking->room_id;
        $data['merchant_id']    = $data_booking->merchant_id;
        $data['booking_id']     = $data_booking->id;
        $data['checkin']        = $data_booking->checkin;
        $data['checkout']       = $data_booking->checkout;
        $data_host_reviews      = parent::store($data);

        return $data_host_reviews;
    }

    public function updateStatus($id, $data, $excepts = [], $only = [])
    {
        return parent::update($id, $data, $excepts, $only);
    }

    /**
     * Kiểm tra xem có đủ điều kiện review hay không
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $data_booking
     * @throws \Exception
     */
    public function checkReview($data_booking)
    {
        $merchant_id = Auth::user()->id;
        if ($merchant_id != $data_booking->merchant_id)
        {
            throw new \Exception('Bạn không có quyền review về khách hàng này');
        }

        $data    = $this->model->where('booking_id',$data_booking->id  )->first();
        //dd($data);
        if (!empty($data)) {
            throw new \Exception('Bạn không thể reviews thêm về khách hàng này được nữa');
        }


    }


}