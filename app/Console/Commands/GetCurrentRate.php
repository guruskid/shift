<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetCurrentRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the current BTC rate';

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
        \Log::info("I was called");
    }
}
