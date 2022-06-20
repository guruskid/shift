<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\BusinessDeveloperController;
use Illuminate\Console\Command;

class PopulateUserTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:trackingTable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking for Users that are not in the system';

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
        return BusinessDeveloperController::QuarterlyInactiveFromOldUsersDB();
    }
}
