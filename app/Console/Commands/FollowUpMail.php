<?php

namespace App\Console\Commands;
use App\Http\Controllers\MarketingController;
use Illuminate\Console\Command;

class FollowUpMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'followup:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending automated mails to new users';

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
        return MarketingController::FollowUpMail();
    }
}
