<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Carbon\Carbon;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\PaymentReminderEmails::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('payreminders:send');
////                 ->dailyAt('01:00')
//                 ->when(function() {
//                     $today = new Carbon();
//                     $dayOfMonth = (int) $today->format("d");
//                     $result = $dayOfMonth % 5;
//                     return true;
//                     if ($result == 0) {
//                         return true;
//                     } else {
//                         return false;
//                     }
//                 });
    }
}
