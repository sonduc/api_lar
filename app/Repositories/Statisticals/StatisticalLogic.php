<?php

namespace App\Repositories\Statisticals;

use App\Repositories\BaseLogic;
use App\Repositories\Bookings\BookingRepositoryInterface;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class StatisticalLogic extends BaseLogic
{
    protected $model;
    protected $booking;

    public function __construct(
        StatisticalRepositoryInterface $statistical,
        BookingRepositoryInterface $booking) 
    {
        $this->model   = $statistical;
        $this->booking = $booking;
    }

    public function bookingMonth($data)
    {
        switch ($data['view']) {
            case 'day':
                $period = CarbonPeriod::create($data['date_start'],$data['date_end']);
                foreach ($period as $key => $value) {
                    // dump($value->format('Y-m-d'));
                }
                break;

            case 'week':
                # code...
                break;

            case 'month':
                # code...
                break;

            case 'year':
                # code...
                break;
            default:
                # code...
                break;
        }

        $bookings = $this->booking->getAll();
        foreach ($bookings as $key => $value) {
            dd($value['created_at']->format('Y-m-d'));
        }
        dd('$bookings');
    }
}
