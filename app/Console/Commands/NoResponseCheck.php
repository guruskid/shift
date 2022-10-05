<?php

namespace App\Console\Commands;

use App\UserTracking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class NoResponseCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'noResponse:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'checking not responded users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        UserTracking::where('noResponse_streak','>',3)->where('Current_Cycle','!=','DeadUser')
            ->update([
                'Previous_Cycle' =>'NoResponse',
                'current_cycle_count_date' => now(),
                'Current_Cycle' => "DeadUser",
            ]);
    }
}
