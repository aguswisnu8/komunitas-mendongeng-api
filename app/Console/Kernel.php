<?php

namespace App\Console;

use App\Http\Controllers\API\MendongengController;
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
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->call(
        //     function () {
        //         info('test schedule');
        //     }
        //     // )->everyMinute()->timezone('Asia/Jakarta');
        // )->dailyAt('20:34')->timezone('Asia/Jakarta');
        $schedule->call('App\Http\Controllers\API\MendongengController@scheduleTest')->everyMinute();
        // $schedule->call('App\Http\Controllers\API\MendongengController@scheduleTest')->dailyAt('11:00')->timezone('Asia/Jakarta');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
