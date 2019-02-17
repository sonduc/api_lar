<?php

namespace App\Console\Commands;

use App\Repositories\Users\UserRepositoryInterface;
use Illuminate\Console\Command;
use App\Repositories\Bookings\BookingConstant;
use App\Repositories\Bookings\BookingRepositoryInterface;
use App\Events\Booking_Notification_Event;
use App\Events\Booking_Reviews_Event;
use Carbon\Carbon;
use phpDocumentor\Reflection\DocBlock\Description;

class BookingReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:review';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send review notification';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $user;
    public function __construct(BookingRepositoryInterface $booking,UserRepositoryInterface $user)
    {
        parent::__construct();
        $this->booking    = $booking;
        $this->user       = $user;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // reviews url : https://www.traveloka.com/r?id%3D1621330619039535728%26target%3Dhttp%253A%252F%252Fwww.traveloka.com%252Fvi-vn%252FhotelReview%252Funsubscribe%253FbookingId%253D385187202%2526authId%253D1617802102926503576&amp;source=gmail&amp;ust=1549965609563000&amp;usg=AFQjCNHkN1n8_l9u7PBTGVXQ9odpf9voDw
        // safe url : https://www.google.com/url?q= .$reviews_url
        Carbon::setLocale(getLocale());
        $bookings                   = $this->booking->getAllBookingCheckoutOneDay();

        $timeSubmit                = Carbon::now()->timestamp;
        $timeSubmit                = base64_encode($timeSubmit);
        // dd($bookings);
        $current_day                = Carbon::now()->timestamp;
        foreach ($bookings as $key => $booking) {
            $userToken              = $this->user->getUserToken($booking->customer_id);
            $checkin                = $booking->checkin;
            $checkout               = $booking->checkout;
            $checkout_timestamp     = Carbon::parse($checkout)->addHours(28)->timestamp;
            $checkin_date           = Carbon::parse($checkin);
            $checkout_date          = Carbon::parse($checkout);
            if (isset($booking)) {
                $booking_review_url = env('API_URL_CUSTOMER') . '/reviews/'. $booking->id . '?token=' . $userToken . '&time='.$timeSubmit;
                if ($booking->booking_type == BookingConstant::BOOKING_TYPE_DAY) {
                    $dataTime['read_timeCheckin']  = $checkin_date->isoFormat('LL');
                    $dataTime['read_timeCheckout'] = $checkout_date->isoFormat('LL');
                    $dataTime['count_bookingTime'] = $checkin_date->diffInDays($checkout_date)+1;
                    $dataTime['review_url']        = $booking_review_url;
                    $dataTime['safe_redirect']     = $booking_review_url;
                    $dataTime['valid_time']        = Carbon::now()->addDays(25)->isoFormat('LL');
                    event(new Booking_Reviews_Event($booking, $dataTime));
                    $booking->email_reviews = 1;
                    $booking->save();
                }
                if ($booking->booking_type == BookingConstant::BOOKING_TYPE_HOUR) {
                    $dataTime['read_timeBooking']  = $checkin_date->isoFormat('LL');
                    $dataTime['count_bookingTime'] = $checkin_date->diffInHours($checkout_date);
                    $dataTime['review_url']        = $booking_review_url;
                    $dataTime['safe_redirect']     = $booking_review_url;
                    $dataTime['valid_time']        = Carbon::now()->addDays(25)->isoFormat('LL');
                    event(new Booking_Reviews_Event($booking, $dataTime));
                    $booking->email_reviews = 1;
                    $booking->save();
                }
            }
        }
    }
}
