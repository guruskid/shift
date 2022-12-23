<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\SalesHelperController;
use Illuminate\Console\Command;

class moveActivetoNewUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:moveActive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'moving new users who are in active role to the new users role';

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
        SalesHelperController::changingRecentlyJoinedUsersFromActiveToNewUsers();
        return true;
    }
}
