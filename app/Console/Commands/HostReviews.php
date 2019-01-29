<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/29/2019
 * Time: 3:42 AM
 */

namespace App\Console\Commands;


use App\Events\Host_Reviews_Event;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Repositories\HostReviews\HostReviewRepository;
use Illuminate\Console\Command;

class HostReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hosts:review';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send review notification for host';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(BookingRepositoryInterface $booking)
    {
        parent::__construct();
        $this->booking    = $booking;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bookings         = $this->booking->getAllBookingCheckoutOneDay()->toArray();
        event(new Host_Reviews_Event($bookings));

    }

}