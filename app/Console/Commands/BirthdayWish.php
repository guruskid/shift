<?php

namespace App\Console\Commands;

use App\Mail\Birthday;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class BirthdayWish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthday:wish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is a birthday wish';

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

        $today = Carbon::now()->format('d/m');

        $users = User::where('birthday', $today)->get();



        foreach($users as $user){
             $user->birthday_status = 1;
             Mail::to($user->email)->send(new Birthday());


            }

        // JobsBirthday::dispatch($users);

        //   return response()->json([
        //     'success' => true,
        //     'data' => 'Birthday sent',
        // ]);


        \Log::info("Cron is working fine!");
    }
}
