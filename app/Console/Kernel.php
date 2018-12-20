<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands
        = [
            'App\Console\Commands\ValidateCoupon',
            'App\Console\Commands\BookingUpdateStatus',
            'App\Console\Commands\BookingReviews',
            'App\Console\Commands\AirbnbCalendar',
            'App\Console\Commands\CreateReferralCoupon',
        ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('booking:status')->everyThirtyMinutes();
        $schedule->command('booking:status')->dailyAt('14:00:00');
        $schedule->command('coupon:validate')->dailyAt('23:59:59');
        $schedule->command('airbnb:sync')->twiceDaily(3, 14);
    }

    // protected function commands()
    // {
    //     $this->load(__DIR__.'/Commands');
    //     require base_path('routes/console.php');
    // }
}
