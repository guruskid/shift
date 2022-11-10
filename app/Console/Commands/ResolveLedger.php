<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\LedgerController;
use Illuminate\Console\Command;

class ResolveLedger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ledger:resolve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This checks for users with wrong ledger balances and resolves them';

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
        // LedgerController::resolve();

        return true;
    }
}
