<?php

namespace App\Console\Commands;

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
        $v_progress = 0;
        if (Auth::user()->email_verified_at) {
            $v_progress += 25;
        }
        if (Auth::user()->phone_verified_at) {
            $v_progress += 25;
        }
        if (Auth::user()->address_verified_at) {
            $v_progress += 25;
        }
        if (Auth::user()->idcard_verified_at) {
            $v_progress += 25;
        }

        Auth::user()->v_progress = $v_progress;

        switch ($v_progress) {
            case 25:
                Auth::user()->daily_max = 0;
                Auth::user()->monthly_max = 0;
                Auth::user()->save();
                break;

            case 50:
                Auth::user()->daily_max = 500000;
                Auth::user()->monthly_max = 5000000;
                Auth::user()->save();
                break;

            case 75:
                Auth::user()->daily_max = 2000000;
                Auth::user()->monthly_max = 60000000;
                Auth::user()->save();
                break;

            case 100:
                Auth::user()->daily_max = 10000000;
                Auth::user()->monthly_max = 99000000;
                Auth::user()->save();
                break;

            default:
                Auth::user()->daily_max = 30000;
                Auth::user()->monthly_max = 300000;
                Auth::user()->save();
                break;
        }

        Auth::user()->save();

        return true;
    }
}
