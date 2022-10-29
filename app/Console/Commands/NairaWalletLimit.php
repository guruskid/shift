<?php

namespace App\Console\Commands;

use App\Http\Controllers\NariaLimitController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class NairaWalletLimit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'naira:limit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
       return NariaLimitController::nariaLimit(Auth::user());
    }
}
