<?php

namespace App\Console;

use App\Console\Commands\BirthdayWish;
use App\Console\Commands\FollowUpMail;
use App\Console\Commands\CheckActiveUsers;
use App\Console\Commands\CheckCalledUsers;
use App\Console\Commands\checkingUnresponsiveNewUsers;
use App\Console\Commands\checkNewCalledUsers;
use App\Console\Commands\CheckRecalcitrantUsers;
use App\Console\Commands\CheckRespondedUsers;
use App\Console\Commands\GetCurrentRate;
use App\Console\Commands\inactiveusersplit;
use App\Console\Commands\moveActivetoNewUsers;
use App\Console\Commands\newusers;
use App\Console\Commands\NoResponseCheck;
use App\Console\Commands\ResolveLedger;
use App\Console\Commands\salesOldDailyConfig;
use App\Http\Controllers\MarketingController;
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
        FollowUpMail::class,
        CheckActiveUsers::class,
        CheckCalledUsers::class,
        CheckRespondedUsers::class,
        CheckRecalcitrantUsers::class,
        NoResponseCheck::class,
        BirthdayWish::class,
        ResolveLedger::class,
        salesOldDailyConfig::class,
        checkNewCalledUsers::class,
        inactiveusersplit::class,
        moveActivetoNewUsers::class,
        newusers::class,
        checkingUnresponsiveNewUsers::class,
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
        $schedule->command('sales:unresponsive')->daily();
        $schedule->command('sales:newusers')->daily();
        $schedule->command('sales:checkactive')->daily();
        $schedule->command('sales:moveActive')->daily();
        $schedule->command('sales:inactiveSplit')->daily(); 
        $schedule->command('check:active')->daily();
        $schedule->command('check:called')->daily();
        $schedule->command('check:Responded')->daily();
        $schedule->command('check:Recalcitrant')->daily();
        $schedule->command('check:quarterlyInactive')->daily();
        $schedule->command('noResponse:check')->daily();
        $schedule->command('sales:config')->daily();
        $schedule->command('birthday:wish')->dailyAt('08:00');
        $schedule->command('ledger:resolve')->everyFiveMinutes();
        $schedule->command('followup:mail')->daily();
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
