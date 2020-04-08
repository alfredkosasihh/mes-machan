<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('raw-data:get')->withoutOverlapping(60);
        $schedule->command('machine-data:get')->withoutOverlapping(60);
        // $schedule->call('App\Http\Controllers\api\ResourceController@fixmachinedatabase')->name('machinedata')->withoutOverlapping(60); //每小時一次
        $schedule->call('App\Http\Controllers\DayPerformanceStatisticsController@getmachineperformance')->withoutOverlapping(60); //抓日績效
        $schedule->call('App\Http\Controllers\OEEperformanceController@getOEEperformance')->withoutOverlapping(60); //當日OEE
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
