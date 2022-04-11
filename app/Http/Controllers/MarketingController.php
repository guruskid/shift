<?php

namespace App\Http\Controllers;

use App\Mail\GeneralTemplateOne;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class MarketingController extends Controller
{
    public function index()
    {
        //?Total signed up customers
       $total_web_signed_up = User::where('platform', 'web')->count();
       $total_app_signed_up = User::where('platform', 'app')->count();

       //? Daily signed up customers
       $daily_web_signed_up = User::where('platform', 'web')
       ->where("created_at",">=",Carbon::now()->subDay())
       ->where("created_at","<=",Carbon::now())->count();

       $daily_app_signed_up = User::where('platform', 'app')
       ->where("created_at",">=",Carbon::now()->subDay())
       ->where("created_at","<=",Carbon::now())->count();
       
       //?Daily Number of Transactions
        $daily_app_transactions = Transaction::where("created_at",">=",Carbon::now()->subDay())
        ->where("created_at","<=",Carbon::now())->count();

        $daily_web_transactions = Transaction::where("created_at",">=",Carbon::now()->subDay())
        ->where("created_at","<=",Carbon::now())->count();

       //?Monthly Nunmber of sign ups 
       $monthly_web_signed_up = User::where('platform', 'web')
       ->whereMonth('created_at', Carbon::now()->month)->count();

       $monthly_app_signed_up = User::where('platform', 'app')
       ->whereMonth('created_at', Carbon::now()->month)->count();

       //?Montly number of transaction
        $monthly_app_transactions = Transaction::whereMonth('created_at', Carbon::now()->month)->count();
        $monthly_web_transactions = Transaction::whereMonth('created_at', Carbon::now()->month)->count();

       //?Recalcitrant User App.
       $recalcitrant = 2700;

       //? Recalcitrant User Web.
       $recalcitrant = 2800;

       $table_data = User::orderBy('id', 'DESC')->get()->take(10);
       return view('admin.marketing.index',compact([
           'table_data','total_web_signed_up','total_app_signed_up','daily_web_signed_up',
           'daily_app_signed_up','monthly_web_signed_up','monthly_app_signed_up',
           'daily_app_transactions','daily_web_transactions',
           'monthly_app_transactions','monthly_web_transactions'
       ]));
    }

    public function user_verification()
    {
        $users = User::orderBy('id', 'desc')->paginate(100);
        foreach($users as $u)
        {
            $u->verification_status = ($u->	phone_verified_at == null) ? 'Pending' : 'Level 1';
            if($u->address_verified_at != null)
            {
                $u->verification_status = 'Level 2';
            }
            if($u->idcard_verified_at != null)
            {
                $u->verification_status = 'Level 3';
            }
        }

        $segment = "Verification Level";
        return view('admin.marketing.users',compact([
            'users','segment'
        ]));
    }

    public function user_birthday()
    {
        $r = Artisan::call('followup:mail');
        $users = User::orderBy('id', 'desc')->paginate(100);
        $segment = "Users Birthday";
        return view('admin.marketing.users',compact([
            'users','segment'
        ]));
    }

    public static function FollowUp_mail(){
        
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
            }
            if($totalDuration_day == 7) //? 7 days interval
            {
                $title = 'Day 7';
                $body = 'We have successfully received your document for level 3 verification.
                Your verification request is currently on-review, and you will get feedback from us within 24-48 hours.';
            }
            if($title && $body)
            {
                $btn_text = '';
                $btn_url = '';
                $name = ($u->first_name == " ") ? $u->username : $u->first_name;
                $name = str_replace(' ', '', $name);
                $firstname = ucfirst($name);
                Mail::to($u->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));
            }

        }
        return "Done";
        
    }


}
