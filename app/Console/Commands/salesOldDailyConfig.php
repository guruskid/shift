<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\SalesHelperController;
use Illuminate\Console\Command;

class salesOldDailyConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to run and automate the sales dashboard';

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
        $salesHelper = new SalesHelperController();
        $salesHelper->runConfigData();
        return true;
    }
}
