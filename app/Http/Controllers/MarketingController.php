<?php

namespace App\Http\Controllers;

use App\Mail\GeneralTemplateOne;
use App\Transaction;
use App\User;
use App\UserTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class MarketingController extends Controller
{
    public function index()
    {
        $total_web_signed_up = User::where('platform','web')->count();
       $total_app_signed_up = User::where('platform','app')->count();
 
        $daily_web_signed_up = User::where("created_at",">=",Carbon::now()->subDay())
        ->where("created_at","<=",Carbon::now())->where('platform','web')->count();
 
        $daily_app_signed_up = User::where("created_at",">=",Carbon::now()->subDay())
        ->where("created_at","<=",Carbon::now())->where('platform','app')->count();
 
         $daily_app_transactions = Transaction::where("created_at",">=",Carbon::now()->subDay())
         ->where("created_at","<=",Carbon::now())->where('platform','app')->count();
 
         $daily_web_transactions = Transaction::where("created_at",">=",Carbon::now()->subDay())
         ->where("created_at","<=",Carbon::now())->where('platform','web')->count();
 
         $monthly_web_signed_up = User::whereMonth('created_at', Carbon::now()->month)
         ->whereYear('created_at', Carbon::now()->year)->where('platform','web')->count();
 
        $monthly_app_signed_up = User::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)->where('platform','app')->count();
 
         $monthly_app_transactions = Transaction::whereMonth('created_at', Carbon::now()->month)
         ->whereYear('created_at', Carbon::now()->year)->where('platform','app')->count();
         $monthly_web_transactions = Transaction::whereMonth('created_at', Carbon::now()->month)
         ->whereYear('created_at', Carbon::now()->year)->where('platform','web')->count();
 
         $recalcitrant_users_web = User::whereHas('userTracking', function($query){
            $query->where('Current_Cycle','Recalcitrant');
         })->where('platform','web')->count();

         $recalcitrant_users_app = User::whereHas('userTracking', function($query){
            $query->where('Current_Cycle','Recalcitrant');
         })->where('platform','app')->count();

       $table_data = User::orderBy('id', 'DESC')->get()->take(10);
       return view('admin.marketing.index',compact([
           'table_data','total_web_signed_up','total_app_signed_up','daily_web_signed_up',
           'daily_app_signed_up','monthly_web_signed_up','monthly_app_signed_up',
           'daily_app_transactions','daily_web_transactions',
           'monthly_app_transactions','monthly_web_transactions',
           'recalcitrant_users_web','recalcitrant_users_app'
       ]));
    }

    public function Category($type = null)
    {
       $total_web_signed_up = User::where('platform','web')->count();
       $total_app_signed_up = User::where('platform','app')->count();

       $daily_web_signed_up = User::where("created_at",">=",Carbon::now()->subDay())
       ->where("created_at","<=",Carbon::now())->where('platform','web')->count();

       $daily_app_signed_up = User::where("created_at",">=",Carbon::now()->subDay())
       ->where("created_at","<=",Carbon::now())->where('platform','app')->count();

        $daily_app_transactions = Transaction::where("created_at",">=",Carbon::now()->subDay())
        ->where("created_at","<=",Carbon::now())->where('platform','app')->count();

        $daily_web_transactions = Transaction::where("created_at",">=",Carbon::now()->subDay())
        ->where("created_at","<=",Carbon::now())->where('platform','web')->count();

        $monthly_web_signed_up = User::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)->where('platform','web')->count();

       $monthly_app_signed_up = User::whereMonth('created_at', Carbon::now()->month)
       ->whereYear('created_at', Carbon::now()->year)->where('platform','app')->count();

        $monthly_app_transactions = Transaction::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)->where('platform','app')->count();
        $monthly_web_transactions = Transaction::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)->where('platform','web')->count();

        $recalcitrant_users_web = User::whereHas('userTracking', function($query){
            $query->where('Current_Cycle','Recalcitrant');
         })->where('platform','web')->count();

         $recalcitrant_users_app = User::whereHas('userTracking', function($query){
            $query->where('Current_Cycle','Recalcitrant');
         })->where('platform','app')->count();


        $table_data = $this->tableDataForCategory($type);
        return view('admin.marketing.index',compact([
            'table_data','total_web_signed_up','total_app_signed_up','daily_web_signed_up',
            'daily_app_signed_up','monthly_web_signed_up','monthly_app_signed_up',
            'daily_app_transactions','daily_web_transactions',
            'monthly_app_transactions','monthly_web_transactions','type',
            'recalcitrant_users_web','recalcitrant_users_app'
        ]));
    }

    public function tableDataForCategory($type)
    {
        if($type == "All_Users_App")
        {
            return User::where('platform','app')->orderBy('id','desc')->get()->take(10);
        }
        if($type == "All_Users_Web")
        {
            return User::where('platform','web')->orderBy('id','desc')->get()->take(10);
        }

        if($type == "Daily_Users_App" )
        {
            return User::where("created_at",">=",Carbon::now()->subDay())
            ->where("created_at","<=",Carbon::now())->where('platform','app')->orderBy('id','desc')->limit(10)->get();
        }
        if($type == "Daily_Users_Web")
        {
            return User::where("created_at",">=",Carbon::now()->subDay())
            ->where("created_at","<=",Carbon::now())->where('platform','web')->orderBy('id','desc')->limit(10)->get();
        }

        if($type == "Daily_Transactions_App" )
        {
            return Transaction::where("created_at",">=",Carbon::now()->subDay())
            ->where("created_at","<=",Carbon::now())->where('platform','app')->orderBy('id','desc')->limit(10)->get();
        }
        if($type == "Daily_Transactions_Web")
        {
            return Transaction::where("created_at",">=",Carbon::now()->subDay())
            ->where("created_at","<=",Carbon::now())->where('platform','web')->orderBy('id','desc')->limit(10)->get();
        }

        if($type == "Monthly_Users_App")
        {
            return User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->where('platform','app')->orderBy('id','desc')->limit(10)->get();
        }

        if( $type == "Monthly_Users_Web")
        {
            return User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->where('platform','web')->orderBy('id','desc')->limit(10)->get();
        }

        if($type == "Monthly_Transactions_Web" )
        {
            return Transaction::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->where('platform','web')->orderBy('id','desc')->limit(10)->get();
        }

        if( $type == "Monthly_Transactions_App")
        {
            return Transaction::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->where('platform','app')->orderBy('id','desc')->limit(10)->get();
        }

        if($type == "Recalcitrant_App" )
        {
            return User::whereHas('userTracking', function($query){
                $query->where('Current_Cycle','Recalcitrant');
             })->where('platform','app')->orderBy('id','desc')->limit(10)->get();
        }
        if($type == "Recalcitrant_Web")
        {
            return User::whereHas('userTracking', function($query){
                $query->where('Current_Cycle','Recalcitrant');
             })->where('platform','web')->orderBy('id','desc')->limit(10)->get();
        }
        
    }

    public function viewTransactionsCategory($type = null)
    {
        
        if($type == "Daily_Transactions_App")
        {
            $data = Transaction::where("created_at",">=",Carbon::now()->subDay())
            ->where("created_at","<=",Carbon::now())->where('platform','web')->orderBy('id', 'DESC')->paginate(100);
            $segment = "Daily Transactions App";
        }

        if($type == "Daily_Transactions_Web")
        {
            $data = Transaction::where("created_at",">=",Carbon::now()->subDay())
            ->where("created_at","<=",Carbon::now())->where('platform','web')->orderBy('id', 'DESC')->paginate(100);
            $segment = "Daily Transactions Web";
        }

        if($type == "Monthly_Transactions_Web" )
        {
            $data = Transaction::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->where('platform','web')->orderBy('id', 'DESC')->paginate(100);
            $segment = "Monthly Transactions Web";
        }
        if($type == "Monthly_Transactions_App")
        {
            $data = Transaction::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->where('platform','app')->orderBy('id', 'DESC')->paginate(100);
            $segment = "Monthly Transactions App";
        }
        return view('admin.marketing.transactions',compact([
            'data','segment'
        ]));

    }

    public function viewUsersCategory($type = null)
    {
        $data = null;

        if($type == "All_Users_App")
        {
            $data =  User::where('platform','app')->orderBy('id','desc')->paginate(1000);
            $segment = "All User App";
        }
        if($type == "All_Users_Web")
        {
            $data = User::where('platform','web')->orderBy('id','desc')->paginate(1000);
            $segment = "All User Web";
        }

        if($type == "Daily_Users_App" )
        {
            $data =  User::where("created_at",">=",Carbon::now()->subDay())
            ->where("created_at","<=",Carbon::now())->where('platform','app')->orderBy('id','desc')->paginate(1000);
            $segment = "Daily User SignUp App";
        }
        if($type == "Daily_Users_Web")
        {
            $data =  User::where("created_at",">=",Carbon::now()->subDay())
            ->where("created_at","<=",Carbon::now())->where('platform','web')->orderBy('id','desc')->paginate(1000);
            $segment = "Daily User SignUp Web";
        }

        if($type == "Monthly_Users_App")
        {
            $data =  User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->where('platform','app')->orderBy('id','desc')->paginate(1000);
            $segment = "Monthly Users SignUp App";
        }

        if( $type == "Monthly_Users_Web")
        {
            $data =  User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->where('platform','web')->orderBy('id','desc')->paginate(1000);

            $segment = "Monthly Users SignUp Web";
        }

        if($type == "Recalcitrant_App" )
        {
            $data = User::whereHas('userTracking', function($query){
                $query->where('Current_Cycle','Recalcitrant');
             })->where('platform','app')->orderBy('id','desc')->paginate(1000);
             $segment = "Recalcitrant Users App";
        }
        if($type == "Recalcitrant_Web")
        {
            $data = User::whereHas('userTracking', function($query){
                $query->where('Current_Cycle','Recalcitrant');
             })->where('platform','web')->orderBy('id','desc')->paginate(1000);
             $segment = "Recalcitrant Users Web";
        }
        return view('admin.marketing.userCategory',compact([
            'data','segment'
        ]));

    }

    public function user_verification(Request $request)
    {
        $users = User::orderBy('id', 'desc')->get();
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
        if($request->status)
        {
            if($request->status == 'Pending')
            {
                $users = $users->where('verification_status','Pending');
            }

            if($request->status == 'Level 1')
            {
                $users = $users->where('verification_status','Level 1');
            }

            if($request->status == 'Level 2')
            {
                $users = $users->where('verification_status','Level 2');
            }            

            if($request->status == 'Level 3')
            {
                $users = $users->where('verification_status','Level 3');
            } 
        }

        $users = $users->paginate(100);


        $segment = "Verification Level";
        return view('admin.marketing.users',compact([
            'users','segment'
        ]));
    }

    public function user_birthday()
    {
        $users = User::orderBy('id', 'desc')->paginate(100);
        $segment = "Users Birthday";
        return view('admin.marketing.users',compact([
            'users','segment'
        ]));


    }

    public static function FollowUpMail()
    {
        $followUp_users = User::whereDate( 'created_at', now()->subDays(3))->get(); //?for users that Joined 3 Days Ago
        $Priming_users = User::whereDate( 'created_at', now()->subDays(7))->get(); //?for users that Joined 7 Days Ago
        foreach ($followUp_users as $u)
        {
            $title = 'Welcome To Dantown';
            $body = "You made a good decision!<br><br>";
            $body .="Welcome to the Dantown community, we're glad to have you here.<br><br>";
            $body .="Dantown is an African top Cryptocurrency platform founded to create a trustworthy and secure place to trade your Cryptocurrency conveniently."; 
            (new MarketingController)->sendMail($u,$title,$body);
        }

        foreach ($Priming_users as $u)
        {
            if($u->transactions()->count() == 0){
                $title = 'Check Up E-Mail';
                $body = "We noticed since you signed up on the Dantown platform, you haven’t performed transactions.<br><br>";
                
                $body .= "We'd like to know if you are experiencing any difficulty on our platform.<br><br>";
                
                $body .= "The good thing is, We are always available for you.<br><br>";
                $body .= "Kindly reach out to us if you need any assistance by responding to this mail.<br><br>";
                
                $body .= "We promise to always provide you with the best experience with Dantown.<br><br>";
                
                $body .= "Thank you for choosing Dantown now and always.";
                (new MarketingController)->sendMail($u,$title,$body);
            }
        }
    }

    public function sendMail(User $user, $title,$body)
    {
        $btn_text = '';
        $btn_url = '';
        $name = ($user->first_name == " ") ? $user->username : $user->first_name;
        $name = str_replace(' ', '', $name);
        $firstname = ucfirst($name);
        Mail::to($user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));
    }
}
