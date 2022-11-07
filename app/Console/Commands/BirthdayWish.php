<?php

namespace App\Console\Commands;

use App\Mail\Birthday;
use App\Notification;
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
        User::where('birthday', $today)->update(['birthday_status' => 1]);
        User::where('birthday', '!=', $today)->update(['birthday_status' => 0]);

        foreach($users as $user){

            $title = 'Happy Birthday';
            $body = 'Happy Birthday to you. May the best of your birthdays be today, and today be the least of those yet to come';
            $not = Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
                'is_birthday' => 1,
            ]);

             Mail::to($user->email)->send(new Birthday());

            }


    }
}
