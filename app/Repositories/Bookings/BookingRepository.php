<?php

namespace App\Repositories\Bookings;

use App\Repositories\BaseRepository;
use App\Repositories\Payments\PaymentHistoryRepository;
use App\Repositories\Users\UserRepository;
use App\User;

class BookingRepository extends BaseRepository
{

    protected $model;
    protected $status;
    protected $payment;
    protected $user;

    /**
     * BookingRepository constructor.
     *
     * @param Booking $booking
     */
    public function __construct(Booking $booking, BookingStatusRepository $status, PaymentHistoryRepository $payment, UserRepository $user)
    {
        $this->model   = $booking;
        $this->status  = $status;
        $this->payment = $payment;
        $this->user    = $user;
    }

    /**
     * Thêm booking mới
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function store($data = [])
    {
        $this->checkUserExist($data);
        $data = $this->priceCaculator($data);
        $data = $this->dateToTimestamp($data);

        $data_booking = parent::store($data);
        $this->status->storeBookingStatus($data_booking, $data);
        $this->payment->storePaymentHistory($data_booking, $data);
        return $data_booking;
    }

    /**
     * Tính toán giá tiền cho booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $data
     *
     * @return array
     */
    public function priceCaculator($data = [])
    {
        $price =
            $data['price_original'] + (array_key_exists('service_fee', $data) ? $data['service_fee'] : 0)
            - (array_key_exists('price_discount', $data) ? $data['price_discount'] : 0);

        $data['total_fee'] = $price;
        return $data;
    }

    /**
     * Chuyển ngày giờ thành UNIX timestamp
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $data
     *
     * @return array
     */
    public function dateToTimestamp($data = [])
    {
        $data['checkin']  = strtotime($data['checkin']);
        $data['checkout'] = strtotime($data['checkout']);

        return $data;
    }

    /**
     * Cập nhật booking
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param int   $id
     * @param       $data
     * @param array $excepts
     * @param array $only
     *
     * @return bool
     */
    public function update($id, $data, $excepts = [], $only = [])
    {
        $data = $this->priceCaculator($data);
        $data = $this->dateToTimestamp($data);

        $data_booking = parent::update($id, $data);
        $this->status->updateBookingStatus($data_booking, $data);
        return $data_booking;
    }

    /**
     * Kiểm tra xem có user tồn tại
     * Nếu không tồn tại thì tự động thêm user mới
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $data
     */
    private function checkUserExist($data = [])
    {
        $user = $this->user->getUserByEmailOrPhone($data);

        if (!$user) {
            $data['password'] = $data['phone'];
            $data['type'] = User::USER;
            $data['owner'] = User::NOT_OWNER;
            $data['status'] = User::DISABLE;
            $this->user->store($data);
        }
    }
}

