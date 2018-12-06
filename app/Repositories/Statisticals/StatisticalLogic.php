<?php

namespace App\Repositories\Statisticals;

use App\Repositories\BaseLogic;
use App\Repositories\Bookings\BookingRepositoryInterface;
use Carbon\Carbon;

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
                $dayStart = Carbon::parse($data['date_start'])->day;
                $dayEnd = Carbon::parse($data['date_end'])->day;
                
                dd($dayStart,$dayEnd);
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

        $date_start = $data['date_start'];
        $date_end = $data['date_end'];
        // $bookings = $this->booking
        //     ->where('create_at');
        dd($date_start);
    }
}
