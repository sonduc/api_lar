<?php

namespace App\Repositories\EmailCustomers;

use App\Repositories\BaseLogic;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Repositories\Users\UserRepositoryInterface;

class EmailCustomerLogic extends BaseLogic
{
    protected $model;
    protected $booking;
    protected $user;

    public function __construct(
        EmailCustomersRepositoryInterface $emailcustomer,
        BookingRepositoryInterface $booking,
        UserRepositoryInterface $user
    ) {
        $this->model   = $emailcustomer;
        $this->booking = $booking;
        $this->user    = $user;
    }

    /**
     *
     * @author sonduc <ndson1998@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function getBookingSuccess($params = [])
    {
        return $this->booking->getBookingSuccess($params);
    }

    public function getUserOwner($params = [])
    {   
        return $this->user->getUserOwner($params);
    }

    public function getBookingCheckout($params = [])
    {
        return $this->booking->getBookingCheckout($params);
    }
}
