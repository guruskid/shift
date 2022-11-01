<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\BusinessDeveloperController;
use Illuminate\Console\Command;

class CheckRecalcitrantUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:Recalcitrant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to check Recalcitrant users in a system';

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
        return BusinessDeveloperController::CheckRecalcitrantUsersForResponded();
    }
}
