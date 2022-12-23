<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\NewUsersSalesController;
use Illuminate\Console\Command;

class checkingActiveForUnresponsice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:newactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking new active users for unresponsive new users';

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
        NewUsersSalesController::newActiveForUnresponsive();
    }
}
