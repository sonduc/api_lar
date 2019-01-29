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
            'App\Console\Commands\CreateMerchantBonusTransaction',
            'App\Console\Commands\TransactionCombine',
            'App\Console\Commands\HostReviews',
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
        $schedule->command('booking:review')->dailyAt('14:00:00');
        $schedule->command('coupon:validate')->dailyAt('23:59:59');
        $schedule->command('referral:coupon')->dailyAt('02:00:00');
        $schedule->command('transaction:bonus')->dailyAt('02:30:00');
        $schedule->command('airbnb:sync')->twiceDaily(3, 14);
        $schedule->command('transaction:combine')->dailyAt('01:00:00');

        $schedule->command('hosts:review')->dailyAt('16:00:00');
    }

    // protected function commands()
    // {
    //     $this->load(__DIR__.'/Commands');
    //     require base_path('routes/console.php');
    // }
}
