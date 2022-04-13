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
        $total_web_signed_up = User::count();
        $total_app_signed_up = User::count();
 
        $daily_web_signed_up = User::where("created_at",">=",Carbon::now()->subDay())
        ->where("created_at","<=",Carbon::now())->count();
 
        $daily_app_signed_up = User::where("created_at",">=",Carbon::now()->subDay())
        ->where("created_at","<=",Carbon::now())->count();
 
         $daily_app_transactions = Transaction::where("created_at",">=",Carbon::now()->subDay())
         ->where("created_at","<=",Carbon::now())->count();
 
         $daily_web_transactions = Transaction::where("created_at",">=",Carbon::now()->subDay())
         ->where("created_at","<=",Carbon::now())->count();
 
         $monthly_web_signed_up = User::whereMonth('created_at', Carbon::now()->month)
         ->whereYear('created_at', Carbon::now()->year)->count();
 
        $monthly_app_signed_up = User::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)->count();
 
         $monthly_app_transactions = Transaction::whereMonth('created_at', Carbon::now()->month)
         ->whereYear('created_at', Carbon::now()->year)->count();
         $monthly_web_transactions = Transaction::whereMonth('created_at', Carbon::now()->month)
         ->whereYear('created_at', Carbon::now()->year)->count();
 
         $recalcitrant = 2700;
 
         $recalcitrant = 2800; 

       $table_data = User::orderBy('id', 'DESC')->get()->take(10);
       return view('admin.marketing.index',compact([
           'table_data','total_web_signed_up','total_app_signed_up','daily_web_signed_up',
           'daily_app_signed_up','monthly_web_signed_up','monthly_app_signed_up',
           'daily_app_transactions','daily_web_transactions',
           'monthly_app_transactions','monthly_web_transactions'
       ]));
    }

    public function Category($type = null)
    {
       $total_web_signed_up = User::count();
       $total_app_signed_up = User::count();

       $daily_web_signed_up = User::where("created_at",">=",Carbon::now()->subDay())
       ->where("created_at","<=",Carbon::now())->count();

       $daily_app_signed_up = User::where("created_at",">=",Carbon::now()->subDay())
       ->where("created_at","<=",Carbon::now())->count();

        $daily_app_transactions = Transaction::where("created_at",">=",Carbon::now()->subDay())
        ->where("created_at","<=",Carbon::now())->count();

        $daily_web_transactions = Transaction::where("created_at",">=",Carbon::now()->subDay())
        ->where("created_at","<=",Carbon::now())->count();

        $monthly_web_signed_up = User::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)->count();

       $monthly_app_signed_up = User::whereMonth('created_at', Carbon::now()->month)
       ->whereYear('created_at', Carbon::now()->year)->count();

        $monthly_app_transactions = Transaction::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)->count();
        $monthly_web_transactions = Transaction::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)->count();

        $recalcitrant = 2700;

        $recalcitrant = 2800;


        $table_data = $this->tableDataForCategory($type);
        return view('admin.marketing.index',compact([
            'table_data','total_web_signed_up','total_app_signed_up','daily_web_signed_up',
            'daily_app_signed_up','monthly_web_signed_up','monthly_app_signed_up',
            'daily_app_transactions','daily_web_transactions',
            'monthly_app_transactions','monthly_web_transactions','type'
        ]));
    }

    public function tableDataForCategory($type)
    {
        if($type == "All_Users_App" || $type == "All_Users_Web")
        {
            return User::all()->take(10);
        }

        if($type == "Daily_Users_App" || $type == "Daily_Users_Web")
        {
            return User::where("created_at",">=",Carbon::now()->subDay())
            ->where("created_at","<=",Carbon::now())->limit(10)->get();
        }

        if($type == "Daily_Transactions_App" || $type == "Daily_Transactions_Web")
        {
            return Transaction::where("created_at",">=",Carbon::now()->subDay())
            ->where("created_at","<=",Carbon::now())->limit(10)->get();
        }

        if($type == "Monthly_Users_App" || $type == "Monthly_Users_Web")
        {
            return User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->limit(10)->get();
        }

        if($type == "Monthly_Transactions_Web" || $type == "Monthly_Transactions_App")
        {
            return Transaction::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->limit(10)->get();
        }

        if($type == "Recalcitrant_App" || $type == "Recalcitrant_Web")
        {
            return User::limit(10)->get();
        }
        
    }

    public function viewTransactionsCategory($type = null)
    {
        
        if($type == "Daily_Transactions_App" || $type == "Daily_Transactions_Web")
        {
            $data = Transaction::where("created_at",">=",Carbon::now()->subDay())
            ->where("created_at","<=",Carbon::now())->orderBy('id', 'DESC')->paginate(100);
            $segment = "Daily Transactions";
        }

        if($type == "Monthly_Transactions_Web" || $type == "Monthly_Transactions_App")
        {
            $data = Transaction::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->orderBy('id', 'DESC')->paginate(100);
            $segment = "Monthly Transactions";
        }
        return view('admin.marketing.transactions',compact([
            'data','segment'
        ]));

    }

    public function viewUsersCategory($type = null)
    {
        $data = null;
        if($type == "All_Users_App" || $type == "All_Users_Web" || $type == "Recalcitrant_App" 
            || $type == "Recalcitrant_Web" || $type == 'all_users')
        {
            $data = User::orderBy('id', 'DESC')->paginate(100);
            $segment = "All Users";
        }

        if($type == "Daily_Users_App" || $type == "Daily_Users_Web")
        {
            $data = User::where("created_at",">=",Carbon::now()->subDay())
            ->where("created_at","<=",Carbon::now())->orderBy('id', 'DESC')->paginate(100);
            $segment = "Daily Signed Up Users";
        }
        if($type == "Monthly_Users_App" || $type == "Monthly_Users_Web")
        {
            $data = User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->orderBy('id', 'DESC')->paginate(100);
            $segment = "Monthly Signed Up Users";
        }
        return view('admin.marketing.userCategory',compact([
            'data','segment'
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
