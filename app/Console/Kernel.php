<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

    
    protected $commands = [
        Commands\CronJobs::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('fetch:apidata')->twiceDaily(1, 13); // update every 12hours at 1 and 13
        // $schedule->command('update:authors')->twiceDaily('13:01'); // update every 12hours at 1:01pm immediately after the first cron

        $schedule->command('fetch:apidata')->everyMinute();
        $schedule->command('update:authors')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
