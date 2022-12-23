<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\NewUsersSalesController;
use Illuminate\Console\Command;

class checkingUnresponsiveNewUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:unresponsive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to check the unresponsive new Users';

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
        NewUsersSalesController::unresponsiveForNewInactive();
    }
}
