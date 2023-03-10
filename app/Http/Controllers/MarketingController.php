<?php

namespace App\Http\Controllers;

use App\Card;
use App\Http\Controllers\Admin\AccountSummaryController;
use App\Mail\GeneralTemplateOne;
use App\Transaction;
use App\User;
use App\UserTracking;
use App\UtilityTransaction;
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

        $daily_web_signed_up = User::whereDate("created_at",Carbon::now())->where('platform','web')->count();

        $daily_app_signed_up = User::whereDate("created_at",Carbon::now())->where('platform','app')->count();

        //?App total Transactions
        $Transaction_total_app_transactions = Transaction::where('platform','app')->where('status', 'success')->count();
        $total_app_transactions = $Transaction_total_app_transactions;

        //?Web Total Transactions
        $Transaction_total_web_transactions = Transaction::where('platform','web')->where('status', 'success')->count();
        $total_web_transactions = $Transaction_total_web_transactions;

        //?app Daily transaction
        $Transaction_daily_app_transactions = Transaction::whereDate("created_at",Carbon::now())->where('status', 'success')->where('platform','app')->count();
        $daily_app_transactions = $Transaction_daily_app_transactions;

        //?Web Daily transaction
        $Transaction_daily_web_transactions = Transaction::whereDate("created_at",Carbon::now())->where('status', 'success')->where('platform','web')->count();
        $daily_web_transactions = $Transaction_daily_web_transactions ;

         $monthly_web_signed_up = User::whereMonth('created_at', Carbon::now()->month)
         ->whereYear('created_at', Carbon::now()->year)->where('platform','web')->count();

        $monthly_app_signed_up = User::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)->where('platform','app')->count();

        //?App Monthly Transactions
        $Transaction_monthly_app_transactions = Transaction::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)->where('status', 'success')->where('platform','app')->count();

        $monthly_app_transactions = $Transaction_monthly_app_transactions ;

        //?Web Monthly Transactions
        $Transaction_monthly_web_transactions = Transaction::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)->where('status', 'success')->where('platform','web')->count();
        $monthly_web_transactions = $Transaction_monthly_web_transactions;

        //?monthly Utility Transactions
        $utility_monthly = UtilityTransaction::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)->where('status', 'success')->count();

        //?daily Utility Transactions
        $utility_daily = UtilityTransaction::whereDate("created_at",Carbon::now())
        ->where('status', 'success')->count();

       $table_data = User::orderBy('id', 'DESC')->get()->take(10);

        //?New Trading User %increase
        // User who traded six months ago
        $mtrading_users = User::whereHas('transactions', function ($tranx) {
            $tranx->where('status','success')->where('created_at', '<=', Carbon::now()->subMonths(6)->format('Y-m-d'));
        })->pluck('id')->toArray();

        // User who registered six months ago but no trade
        // $old_users_with_no_tranx = User::whereDoesntHave('transactions')->where('created_at', '<=', Carbon::now()->subMonths(6)->format('Y-m-d'))->pluck('id')->toArray();

        $new_trading_users =User::orWhereIn('id',$mtrading_users)
        // ->orWhereIn('id',$old_users_with_no_tranx)
        ->whereHas('transactions', function ($tranx) {
            $tranx->where('status','success')->whereDate('created_at', '=', Carbon::now()->format('Y-m-d'));
        })->count();

        // $firstTransactionDate = User::first()->created_at;
        // $fdate = Carbon::parse($firstTransactionDate);
        // $totalTransactionsDays = $fdate->diffInDays(Carbon::now()->format('y-m-d'));
        // $totalTransactions = Transaction::where('status','success')->sum('amount_paid');
        // $avgDailyTransactions = $totalTransactions / $totalTransactionsDays;
        // dd($new_trading_users);


       $revenueGrowth = AccountSummaryController::percentageRevenueGrowth()->getData();
       return view('admin.marketing.index',compact([
           'table_data','total_web_signed_up','total_app_signed_up','daily_web_signed_up',
           'daily_app_signed_up','monthly_web_signed_up','monthly_app_signed_up',
           'daily_app_transactions','daily_web_transactions',
           'monthly_app_transactions','monthly_web_transactions',
            'total_app_transactions','total_web_transactions','new_trading_users',
            'total_app_transactions','total_web_transactions','revenueGrowth','utility_monthly','utility_daily'
       ]));
    }

    public function Category($type = null)
    {
       $revenueGrowth = AccountSummaryController::percentageRevenueGrowth()->getData();
       $total_web_signed_up = User::where('platform','web')->count();
       $total_app_signed_up = User::where('platform','app')->count();

       $daily_web_signed_up = User::whereDate("created_at",Carbon::now())->where('platform','web')->count();

       $daily_app_signed_up = User::whereDate("created_at",Carbon::now())->where('platform','app')->count();

       //?App total Transactions
       $Transaction_total_app_transactions = Transaction::where('platform','app')->where('status', 'success')->count();
       $total_app_transactions = $Transaction_total_app_transactions;

       //?Web Total Transactions
       $Transaction_total_web_transactions = Transaction::where('platform','web')->where('status', 'success')->count();
       $total_web_transactions = $Transaction_total_web_transactions;

       //?app Daily transaction
        $Transaction_daily_app_transactions = Transaction::whereDate("created_at",Carbon::now())->where('status', 'success')->where('platform','app')->count();
        $daily_app_transactions = $Transaction_daily_app_transactions;

        //?Web Daily transaction
        $Transaction_daily_web_transactions = Transaction::whereDate("created_at",Carbon::now())->where('status', 'success')->where('platform','web')->count();
        $daily_web_transactions = $Transaction_daily_web_transactions;

        $monthly_web_signed_up = User::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)->where('platform','web')->count();

       $monthly_app_signed_up = User::whereMonth('created_at', Carbon::now()->month)
       ->whereYear('created_at', Carbon::now()->year)->where('platform','app')->count();

       //?App Monthly Transactions
        $Transaction_monthly_app_transactions = Transaction::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)->where('status', 'success')->where('platform','app')->count();
        $monthly_app_transactions = $Transaction_monthly_app_transactions;

        //?Web Monthly Transactions
        $Transaction_monthly_web_transactions = Transaction::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)->where('status', 'success')->where('platform','web')->count();
        $monthly_web_transactions = $Transaction_monthly_web_transactions;

        //?monthly Utility Transactions
        $utility_monthly = UtilityTransaction::whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)->where('status', 'success')->count();

        //?daily Utility Transactions
        $utility_daily = UtilityTransaction::whereDate("created_at",Carbon::now())
        ->where('status', 'success')->count();

        //?New Trading User %increase
        // User who traded six months ago
        $mtrading_users = User::whereHas('transactions', function ($tranx) {
            $tranx->where('status','success')->where('created_at', '<=', Carbon::now()->subMonths(6)->format('Y-m-d'));
        })->pluck('id')->toArray();

        // User who registered six months ago but no trade
        // $old_users_with_no_tranx = User::whereDoesntHave('transactions')->where('created_at', '<=', Carbon::now()->subMonths(6)->format('Y-m-d'))->pluck('id')->toArray();

        $new_trading_users = User::orWhereIn('id',$mtrading_users)
        // ->orWhereIn('id',$old_users_with_no_tranx)
        ->whereHas('transactions', function ($tranx) {
            $tranx->where('status','success')->whereDate('created_at', '=', Carbon::now()->format('Y-m-d'));
        })->count();

        $table_data = $this->tableDataForCategory($type);
        return view('admin.marketing.index',compact([
            'table_data','total_web_signed_up','total_app_signed_up','daily_web_signed_up',
            'daily_app_signed_up','monthly_web_signed_up','monthly_app_signed_up',
            'daily_app_transactions','daily_web_transactions',
            'monthly_app_transactions','monthly_web_transactions','type',
            'total_app_transactions','total_web_transactions','new_trading_users',
            'total_app_transactions','total_web_transactions','revenueGrowth','utility_monthly','utility_daily'
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
        if($type == "All_Transactions_App")
        {
            $daily_app_transaction = collect([]);
            $transaction = Transaction::where('platform','app')->where('status', 'success')->orderBy('id','desc')->limit(10)->get();

            foreach ($transaction as $t) {
                $t->tradename = $t->card;
            }
            $daily_app_transaction = $daily_app_transaction->concat($transaction);

            return $daily_app_transaction->sortByDesc('created_at');
        }
        if($type == "All_Transactions_Web")
        {
            $daily_web_transaction = collect([]);
            $transaction = Transaction::where('platform','web')->where('status', 'success')->orderBy('id','desc')->limit(10)->get();

            foreach ($transaction as $t) {
                $t->tradename = $t->card;
            }
            $daily_web_transaction = $daily_web_transaction->concat($transaction);

            return $daily_web_transaction->sortByDesc('created_at');

        }

        if($type == "Daily_Users_App" )
        {
            return User::whereDate("created_at",Carbon::now())->where('platform','app')->orderBy('id','desc')->limit(10)->get();
        }
        if($type == "Daily_Users_Web")
        {
            return User::whereDate("created_at",Carbon::now())->where('platform','web')->orderBy('id','desc')->limit(10)->get();
        }

        if($type == "Daily_Transactions_App" )
        {
            $daily_app_transaction = collect([]);
            $transaction = Transaction::whereDate("created_at",Carbon::now())->where('status', 'success')->where('platform','app')->orderBy('id','desc')->limit(10)->get();

            foreach ($transaction as $t) {
                $t->tradename = $t->card;
            }

            $daily_app_transaction = $daily_app_transaction->concat($transaction);

            return $daily_app_transaction->sortByDesc('created_at');
        }
        if($type == "Daily_Transactions_Web")
        {
            $daily_web_transaction = collect([]);
            $transaction = Transaction::whereDate("created_at",Carbon::now())->where('status', 'success')->where('platform','web')->orderBy('id','desc')->limit(10)->get();

            foreach ($transaction as $t) {
                $t->tradename = $t->card;
            }
            $daily_web_transaction = $daily_web_transaction->concat($transaction);

            return $daily_web_transaction->sortByDesc('created_at');
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
            $monthly_web_transaction = collect([]);
            $transaction = Transaction::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->where('status', 'success')->where('platform','web')->orderBy('id','desc')->limit(10)->get();

            foreach ($transaction as $t) {
                $t->tradename = $t->card;
            }
            $monthly_web_transaction = $monthly_web_transaction->concat($transaction);

            return $monthly_web_transaction->sortByDesc('created_at');
        }

        if( $type == "Monthly_Transactions_App")
        {
            $monthly_app_transaction = collect([]);
            $transaction = Transaction::with('user')->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->where('status', 'success')->where('platform','app')->orderBy('id','desc')->limit(10)->get();

            foreach ($transaction as $t) {
                $t->tradename = $t->card;
            }
            $monthly_app_transaction = $monthly_app_transaction->concat($transaction);

            return $monthly_app_transaction->sortByDesc('created_at');
        }

        if($type == 'Daily_Utility_Transactions'){
            $utility_daily = UtilityTransaction::with('user')->whereDate("created_at",Carbon::now())
            ->where('status', 'success')->orderBy('id','DESC')->limit(10)->get();

            foreach ($utility_daily as $t) {
                $t->tradename = $t->type;
            }
            return $utility_daily;
        }

        if($type == 'Monthly_Utility_Transactions'){
            $utility_daily = UtilityTransaction::with('user')->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->where('status', 'success')->orderBy('id','DESC')->limit(10)->get();

            foreach ($utility_daily as $t) {
                $t->tradename = $t->type;
            }
            return $utility_daily;
        }


        if ($type == 'Monthly_New_Tranding_Users') {
            // User who traded six months ago
            $mtrading_users = User::whereHas('transactions', function ($tranx) {
                $tranx->where('status','success')->where('created_at', '<=', Carbon::now()->subMonths(6)->format('Y-m-d'));
            })->pluck('id')->toArray();

            // User who registered six months ago but no trade
            $old_users_with_no_tranx = User::whereDoesntHave('transactions')->where('created_at', '<=', Carbon::now()->subMonths(6)->format('Y-m-d'))->pluck('id')->toArray();

            $new_trading_users = User::orWhereIn('id',$mtrading_users)
            // ->orWhereIn('id',$old_users_with_no_tranx)
            ->whereHas('transactions', function ($tranx) {
                $tranx->where('status','success')->whereDate('created_at', '=', Carbon::now()->format('Y-m-d'));
            })->latest()->get();

            return $new_trading_users;
        }

    }

    public function viewTransactionsCategory($type = null, Request $request)
    {
        if($type == "All_Transactions_App")
        {
            $segment = "All Transactions App";
            $collection =  $this->transactionMonthlyCards('app');
            $date_collection = $this->yearDropdownTransactions('app');
            $show_data = false;
            return view('admin.marketing.transacitonsMonthly',compact([
                'collection','segment','show_data','date_collection'
            ]));

        }
        if($type == "All_Transactions_Web")
        {
            $segment = "All Transactions Web";
            $collection =  $this->transactionMonthlyCards('web');
            $date_collection = $this->yearDropdownTransactions('web');
            $show_data = false;
            return view('admin.marketing.transacitonsMonthly',compact([
                'collection','segment','show_data','date_collection'
            ]));
        }
        if($type == "Daily_Transactions_App")
        {
            $daily_app_transaction = collect([]);
            $transaction = Transaction::whereDate("created_at",Carbon::now())->where('status', 'success')->where('platform','app');
            if($request->start)
            {
                $transaction = $transaction->whereDate('created_at','>=',$request->start);
            }
            if($request->end)
            {
                $transaction = $transaction->whereDate('created_at','<=',$request->end);
            }
            $transaction = $transaction->orderBy('id','desc')->get();

            foreach ($transaction as $t) {
                $t->tradename = $t->card;
            }
            $daily_app_transaction = $daily_app_transaction->concat($transaction);
            $count = $daily_app_transaction->count();
            $data = $daily_app_transaction->sortByDesc('created_at')->paginate(100);
            $segment = "Daily Transactions App";
        }

        if($type == "Daily_Transactions_Web")
        {
            $daily_web_transaction = collect([]);
            $transaction = Transaction::whereDate("created_at",Carbon::now())->where('status', 'success')->where('platform','web');
            if($request->start)
            {
                $transaction = $transaction->whereDate('created_at','>=',$request->start);
            }
            if($request->end)
            {
                $transaction = $transaction->whereDate('created_at','<=',$request->end);
            }
            $transaction = $transaction->orderBy('id','desc')->get();

            foreach ($transaction as $t) {
                $t->tradename = $t->card;
            }
            $daily_web_transaction = $daily_web_transaction->concat($transaction);
            $count = $daily_web_transaction->count();
            $data =  $daily_web_transaction->sortByDesc('created_at')->paginate(100);
            $segment = "Daily Transactions Web";
        }

        if($type == "Monthly_Transactions_Web" )
        {
            $monthly_web_transaction = collect([]);
            $transaction = Transaction::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->where('status', 'success')->where('platform','web');
            if($request->start)
            {
                $transaction = $transaction->whereDate('created_at','>=',$request->start);
            }
            if($request->end)
            {
                $transaction = $transaction->whereDate('created_at','<=',$request->end);
            }
            $transaction = $transaction->orderBy('id','desc')->get();

            foreach ($transaction as $t) {
                $t->tradename = $t->card;
            }
            $monthly_web_transaction = $monthly_web_transaction->concat($transaction);
            $count = $monthly_web_transaction->count();
            $data = $monthly_web_transaction->sortByDesc('created_at')->paginate(100);
            $segment = "Monthly Transactions Web";
        }
        if($type == "Monthly_Utility_Transactions")
        {
            $data = UtilityTransaction::with('user')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', 'success');

            if($request->start)
            {
                $data = $data->whereDate('created_at','>=',$request->start);
            }
            if($request->end)
            {
                $data = $data->whereDate('created_at','<=',$request->end);
            }

            $data = $data->orderBy('id','DESC')->get();
            $count = $data->count();
            foreach ($data as $t) {
                $t->tradename = $t->type;
            }
            $data = $data->paginate(100);
            $segment = "Monthly Utility Transactions";
        }

        if($type == "Daily_Utility_Transactions")
        {
            $data = UtilityTransaction::with('user')
            ->whereDate("created_at",Carbon::now())
            ->where('status', 'success');

            if($request->start)
            {
                $data = $data->whereDate('created_at','>=',$request->start);
            }
            if($request->end)
            {
                $data = $data->whereDate('created_at','<=',$request->end);
            }

            $data = $data->orderBy('id','DESC')->get();
            $count = $data->count();
            foreach ($data as $t) {
                $t->tradename = $t->type;
            }
            $data = $data->paginate(100);
            $segment = "Daily Utility Transactions";
        }

        if($type == "Monthly_Transactions_App")
        {
            $monthly_app_transaction = collect([]);
            $transaction = Transaction::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->where('status', 'success')->where('platform','app');

            if($request->start)
            {
                $transaction = $transaction->whereDate('created_at','>=',$request->start);
            }
            if($request->end)
            {
                $transaction = $transaction->whereDate('created_at','<=',$request->end);
            }
            $transaction = $transaction->orderBy('id','desc')->get();

            foreach ($transaction as $t) {
                $t->tradename = $t->card;
            }
            $monthly_app_transaction = $monthly_app_transaction->concat($transaction);
            $count = $monthly_app_transaction->count();
            $data = $monthly_app_transaction->sortByDesc('created_at')->paginate(100);
            $segment = "Monthly Transactions App";
        }
        return view('admin.marketing.transactions',compact([
            'data','segment','count'
        ]));

    }
    public function userMonthlyCards($type)
    {
        $collection = collect([]);
            //? months
            for ($i=1; $i <= 12; $i++) {
                $month_name = Carbon::parse("2020-$i-1")->format('F');
                $data =  User::where('platform',$type)->whereMonth('created_at', $i)
                ->whereYear('created_at', Carbon::now()->year)->orderBy('id','desc')->count();

                $collection_holder = array($i=>["month_name"=>$month_name,
                                "Month_number"=>$i,
                                "number_of_Transactions"=>$data,
                                "type"=>$type
                            ]);
            $collection = $collection->concat($collection_holder);
            }


            return $collection;

    }

    public function transactionMonthlyCards($type)
    {
        $collection = collect([]);
            for ($i=1; $i <= 12; $i++) {
                $month_name = Carbon::parse("2020-$i-1")->format('F');
                $transaction = Transaction::whereMonth('created_at', $i)
                ->whereYear('created_at', Carbon::now()->year)->where('status', 'success')->where('platform',$type)->orderBy('id','desc')->count();

                $data = $transaction;

                $collection_holder = array($i=>["month_name"=>$month_name,
                                "Month_number"=>$i,
                                "number_of_Transactions"=>$data,
                                "type"=>$type
                            ]);
            $collection = $collection->concat($collection_holder);
            }
            return $collection;
    }

    public function viewUsersCategory($type = null,Request $request)
    {
        $data = null;

        if($type == "All_Users_App")
        {
            $segment = "All User App";
            $collection =  $this->userMonthlyCards('app');
            $data = User::where('platform','app')->orderBy('id','desc')->get();
            $date_collection = $this->yearDropdownUser('app');
            $show_data = false;
            return view('admin.marketing.userMonthly',compact([
                'collection','segment','show_data','date_collection'
            ]));

        }
        if($type == "All_Users_Web")
        {
            $segment = "All User Web";
            $collection =  $this->userMonthlyCards('web');

            $date_collection = $this->yearDropdownUser('web');
            $show_data = false;
            return view('admin.marketing.userMonthly',compact([
                'collection','segment','show_data','date_collection'
            ]));
        }

        if($type == "Daily_Users_App" )
        {
            $data =  User::whereDate("created_at",Carbon::now())->where('platform','app');
            if($request->start)
            {
                $data = $data->whereDate('created_at','>=',$request->start);
            }
            if($request->end)
            {
                $data = $data->whereDate('created_at','<=',$request->end);
            }
            $count = $data->get()->count();
            $data = $data->orderBy('id','desc')->paginate(1000);
            $segment = "Daily User SignUp App";
        }
        if($type == "Daily_Users_Web")
        {
            $data =  User::whereDate("created_at",Carbon::now())->where('platform','web');
            if($request->start)
            {
                $data = $data->whereDate('created_at','>=',$request->start);
            }
            if($request->end)
            {
                $data = $data->whereDate('created_at','<=',$request->end);
            }
            $count = $data->get()->count();
            $data = $data->orderBy('id','desc')->paginate(1000);
            $segment = "Daily User SignUp Web";
        }

        if($type == "Monthly_Users_App")
        {
            $data =  User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->where('platform','app');
            if($request->start)
            {
                $data = $data->whereDate('created_at','>=',$request->start);
            }
            if($request->end)
            {
                $data = $data->whereDate('created_at','<=',$request->end);
            }
            $count = $data->get()->count();
            $data = $data->orderBy('id','desc')->paginate(1000);
            $segment = "Monthly Users SignUp App";
        }

        if( $type == "Monthly_Users_Web")
        {
            $data =  User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->where('platform','web');
            if($request->start)
            {
                $data = $data->whereDate('created_at','>=',$request->start);
            }
            if($request->end)
            {
                $data = $data->whereDate('created_at','<=',$request->end);
            }
            $count = $data->get()->count();
            $data = $data->orderBy('id','desc')->paginate(1000);

            $segment = "Monthly Users SignUp Web";
        }
        return view('admin.marketing.userCategory',compact([
            'data','segment','count'
        ]));

    }

    public function yearDropdownUser($type)
    {
        $data = User::where('platform',$type)->orderBy('id','desc')->get()
        ->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('Y');
        });

        return $data;
    }
    public function yearDropdownTransactions($type)
    {
        $transactions = collect([]);
        $transaction = Transaction::where('platform',$type)->where('status', 'success')->orderBy('id','desc')->get();

        foreach ($transaction as $t) {
            $t->tradename = $t->card;
        }
        $transactions = $transactions->concat($transaction);

        $data = $transactions->sortByDesc('created_at')
        ->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('Y');
        });
        return $data;
    }
    public function viewUsersByMonth($month,$type)
    {
        $data = User::whereMonth('created_at', $month)
        ->whereYear('created_at', Carbon::now()->year)->where('platform',$type)->orderBy('id','desc')->get();

        $collection = $this->userMonthlyCards($type);
        $segment = "All Users ".$type;
        $show_data = true;
        $date_collection = $data->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('Y');
        });
        $data = $data->paginate(1000);
        return view('admin.marketing.userMonthly',compact([
            'collection','segment','show_data','data','date_collection'
        ]));

    }

    public function viewTransactionsByMonth($month,$type)
    {
        $transactions = collect([]);
            $transaction = Transaction::whereMonth('created_at', $month)
            ->whereYear('created_at', Carbon::now()->year)->where('status', 'success')->where('platform',$type)->orderBy('id','desc')->get();

            foreach ($transaction as $t) {
                $t->tradename = $t->card;
            }
            $transactions = $transactions->concat($transaction);

            $data = $transactions->sortByDesc('created_at');
            $date_collection = $data->groupBy(function($val) {
                return Carbon::parse($val->created_at)->format('Y');
            });
            $data = $data->paginate(1000);
            $collection = $this->transactionMonthlyCards($type);
            $segment = "All Transactions ".$type;
            $show_data = true;
            return view('admin.marketing.transacitonsMonthly',compact([
                'collection','segment','show_data','data','date_collection'
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
                $body = "We noticed since you signed up on the Dantown platform, you haven???t performed transactions.<br><br>";

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
