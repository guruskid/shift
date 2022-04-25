<?php

namespace App\Console;

use App\Console\Commands\CheckActiveUsers;
use App\Console\Commands\CheckCalledUsers;
use App\Console\Commands\CheckRecalcitrantUsers;
use App\Console\Commands\CheckRespondedUsers;
use App\Console\Commands\GetCurrentRate;
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
        GetCurrentRate::class,
        CheckActiveUsers::class,
        CheckCalledUsers::class,
        CheckRespondedUsers::class,
        CheckRecalcitrantUsers::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('get:rate')->everyMinute();
        $schedule->command('check:active')->daily();
        $schedule->command('check:called')->daily();
        $schedule->command('check:Responded')->daily();
        $schedule->command('check:Recalcitrant')->daily();
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
