<?php

namespace App\Console\Commands;
use App\Http\Controllers\MarketingController;
use Illuminate\Console\Command;
use App\User;
use Illuminate\Support\Carbon;
use App\Mail\GeneralTemplateOne;
use Illuminate\Support\Facades\Mail;

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
        $users = User::all();
        
        foreach ($users as $u)
        {
            $startTime = $u->created_at;
            $endTime = Carbon::now();
            $totalDuration_day =  (int)($startTime->diffInDays($endTime));
            $title = null;
            $body = null;
            if($totalDuration_day == 3) //? 3 days interval
            {
                $title = 'Day 3';
                $body = 'We have successfully received your document for level 3 verification.
                Your verification request is currently on-review, and you will get feedback from us within 24-48 hours.'; 
                $btn_text = '';
                $btn_url = '';
                $name = ($u->first_name == " ") ? $u->username : $u->first_name;
                $name = str_replace(' ', '', $name);
                $firstname = ucfirst($name);
                Mail::to($u->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));
            }
            if($totalDuration_day == 7) //? 7 days interval
            {
                $title = 'Day 7';
                $body = 'We have successfully received your document for level 3 verification.
                Your verification request is currently on-review, and you will get feedback from us within 24-48 hours.';
                $btn_text = '';
                $btn_url = '';
                $name = ($u->first_name == " ") ? $u->username : $u->first_name;
                $name = str_replace(' ', '', $name);
                $firstname = ucfirst($name);
                Mail::to($u->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));
            }

            dd(Mail::failures());

        }
    }
}
