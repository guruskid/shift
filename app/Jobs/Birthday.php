<?php

namespace App\Jobs;

use App\Mail\Birthday as MailBirthday;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class Birthday implements ShouldQueue
{
    public $timeout = 60;

    protected $users;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($users)
    {
        $this->users = $users;

        foreach($users as $user){
            // $emails = $user->email;
             Mail::to($user->email)->send(new MailBirthday());


            }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
