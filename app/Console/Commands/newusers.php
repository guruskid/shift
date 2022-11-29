<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\SalesHelperController;
use Illuminate\Console\Command;

class newusers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:newusers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking new users for active and inactive users';

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
        SalesHelperController::checkNewUserForNewInactiveOrActive();
    }
}
