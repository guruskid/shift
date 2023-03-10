<?php

namespace App\Http\Controllers;

use App\Bank;
use App\Card;
use App\Notification;
use App\Rate;
use App\Transaction;
use App\User;
use App\Setting;
use Illuminate\Http\Request;
use App\Charts\UserChart;
use App\Events\TransactionUpdated;
use App\Mail\DantownNotification;
use App\NairaTransaction;
use App\Exports\DownloadUsers;
use App\Exports\UsdtTransactions;
use App\Exports\UsdtTransactionsExport;
use App\Exports\UserDataTransaction;
use App\Http\Controllers\Admin\BusinessDeveloperController;
use App\Http\Controllers\Admin\FlaggedTransactionsController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\SalesHelperController;
use App\NairaTrade;
use Excel;
use App\NairaWallet;
use App\Payout;
use App\TransactionType;
use App\UtilityTransaction;
use App\Verification;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use DB;

class AdminController extends Controller
{
    //All new functionalities will follow the conventional method, new controllers for each model and stored in the admin folder
    public function dashboard()
    {
        $page_limit = 1000;
        $buyCash = Transaction::where('status', 'success')->where('type', 'buy')->sum('amount_paid');
        $sellCash = Transaction::where('status', 'success')->where('type', 'sell')->sum('amount_paid');
        $buyCount = Transaction::where('status', 'success')->where('type', 'buy')->count();
        $sellCount = Transaction::where('status', 'success')->where('type', 'sell')->count();

        $pBuyCash = Transaction::where('status', 'success')->where('type', 'buy')->where('agent_id', Auth::user()->id)->sum('amount_paid');
        $pSellCash = Transaction::where('status', 'success')->where('type', 'sell')->where('agent_id', Auth::user()->id)->sum('amount_paid');
        $pBuyCount = Transaction::where('status', 'success')->where('type', 'buy')->where('agent_id', Auth::user()->id)->count();
        $pSellCount = Transaction::where('status', 'success')->where('type', 'sell')->where('agent_id', Auth::user()->id)->count();

        $s = Transaction::where('status', 'success')->count();
        $w = Transaction::where('status', 'waiting')->count();
        $d = Transaction::where('status', 'declined')->count();
        $f = Transaction::where('status', 'failed')->count();

        $borderColors = [
            "rgba(255, 99, 132, 1.0)",
            "rgba(22,160,133, 1.0)",
            "rgba(255, 205, 86, 1.0)",
            "rgba(51,105,232, 1.0)"
        ];
        $fillColors = [
            "rgba(255, 99, 132, 1.0)",
            "rgba(22,160,133, 1.0)",
            "rgba(255, 205, 86, 1.0)",
            "rgba(51,105,232, 1.0)"

        ];
        $usersChart = new UserChart;
        $usersChart->minimalist(true);
        $usersChart->labels(['Failed', 'Successful', 'Declined', 'Waiting']);
        $usersChart->dataset('Users by trimester', 'pie', [$f, $s, $d, $w])
            ->color($borderColors)
            ->backgroundcolor($fillColors);


        $transactions = Transaction::latest()->get()->take(5);
        $waiting_transactions = Transaction::where('status', 'waiting')->get()->take(5);
        $success_transactions = Transaction::where('status', 'success')->get()->take(5);
        $failed_transactions = Transaction::where('status', 'failed')->get()->take(5);
        $in_progress_transactions = Transaction::where('status', 'in progress')->get()->take(5);
        $approved_transactions = Transaction::where('status', 'approved')->get()->take(5);

        $users = User::latest()->get()->take(4);
        $verified_users = User::where('email_verified_at', '!=', null)->count();
        $notifications = Notification::where('user_id', 0)->latest()->get()->take(5);
        $users_count = User::all()->count();


        $rubies_balance = 0;
        $users_wallet_balance = NairaWallet::sum('amount');
        $company_balance = $rubies_balance - $users_wallet_balance;

        $transfer_charge = NairaWallet::where('account_number', 0000000001)->first()->amount;
        $sms_charge = NairaWallet::where('account_number', 0000000002)->first()->amount;

        $charges = $transfer_charge + $sms_charge;
        $old_charges = NairaTransaction::sum('charge');

        $withdraw_txns = NairaTransaction::where('transaction_type_id', 3)->sum('amount');
        $airtime_txns = NairaTransaction::where('transaction_type_id', 9)->sum('amount');
        $buy_txns_wallet = NairaTransaction::where('transaction_type_id', 5)->sum('amount');

        $g_txns = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->orderBy('updated_at', 'desc')->get()->take(4);

        $c_txns = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 1);
        })->orderBy('updated_at', 'desc')->get()->take(4);

        $n_txns = NairaTransaction::latest()->get()->take(4);

        /* Get count of transactions from when an agent was last activated */
        $au = Auth::user();
        $a_w_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->where('status', 'waiting')->count();
        $a_i_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->where('status', 'in progress')->count();
        $a_s_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->where('status', 'success')->count();
        $a_a_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->where('status', 'approved')->count();
        $all_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->count();

        $pendingNairaDeposits = NairaTrade::where(['type' => 'deposit', 'status' => 'waiting'])->count();
        $pendingNairaWithdrawals = NairaTrade::where(['type' => 'withdrawal', 'status' => 'waiting'])->count();


        if (Auth::user()->role == 999) { //Super admin
            return view(
                'admin.super_dashboard',
                compact([
                    'transactions', 'users', 'verified_users', 'users_count', 'notifications', 'usersChart',
                    'withdraw_txns', 'airtime_txns', 'buy_txns_wallet',
                    'g_txns', 'c_txns', 'n_txns',
                    'buyCash', 'sellCash', 'buyCount', 'sellCount',
                    'pBuyCash', 'pSellCash', 'pBuyCount', 'pSellCount',
                    'users_wallet_balance', 'rubies_balance', 'company_balance', 'charges', 'old_charges'
                ])
            );
        } else if (Auth::user()->role == 888) { // sales rep
            return view(
                'admin.dashboard',
                compact([
                    'transactions', 'waiting_transactions', 'in_progress_transactions',
                    'users', 'users_count', 'notifications', 'usersChart',
                    'a_w_c', 'a_s_c', 'a_a_c', 'a_i_c',
                    'buyCash', 'sellCash', 'buyCount', 'sellCount',
                    'pBuyCash', 'pSellCash', 'pBuyCount', 'pSellCount', 'users_wallet_balance', 'rubies_balance', 'company_balance', 'pendingNairaDeposits', 'pendingNairaWithdrawals'
                ])
            );
        } else if (Auth::user()->role == 889 || Auth::user()->role == 777 || Auth::user()->role == 775) { //Accountants
            $flaggedTransactionCount = FlaggedTransactionsController::count();
            return view(
                'admin.accountant_dashboard',
                compact([
                    'transactions', 'approved_transactions', 'users', 'users_count', 'notifications', 'usersChart',
                    'withdraw_txns', 'airtime_txns', 'buy_txns_wallet',
                    'g_txns', 'c_txns', 'n_txns',
                    'buyCash', 'sellCash', 'buyCount', 'sellCount',
                    'pBuyCash', 'pSellCash', 'pBuyCount', 'pSellCount',
                    'users_wallet_balance', 'rubies_balance', 'company_balance', 'charges', 'old_charges','flaggedTransactionCount'
                ])
            );
        } else if (Auth::user()->role == 559) { //Marketing
            $marketing_controller =  new MarketingController();
            return $marketing_controller->index();
        } else if (Auth::user()->role == 666) { //Manager
            $pending_deposits = NairaTrade::where('type', 'withdrawal')->where('status', 'waiting')->count();
            $pending_withdrawals = NairaTrade::where('type', 'deposit')->where('status', 'waiting')->count();
            return view(
                'admin.manager_dashboard',
                compact([
                    'transactions', 'users', 'pending_deposits', 'pending_withdrawals',
                    'g_txns', 'c_txns', 'n_txns'
                ])
            );
        } elseif (Auth::user()->role == 444 or Auth::user()->role == 449) { // Chinese Dashboard

            $twentyFourHrsTransactions = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->where("created_at", ">=", Carbon::now()->subDay())->where("created_at", "<=", Carbon::now())->where('status', 'success');
            $cardTwentyFourHrscount = $twentyFourHrsTransactions->count();
            $nairaTwentyFourHr = $twentyFourHrsTransactions->sum('amount_paid');
            $dollarTwentyFourHr = $twentyFourHrsTransactions->sum('amount');

            $nairaTwentyFourHrs = $nairaTwentyFourHr;
            $dollarTwentyFourHrs = $dollarTwentyFourHr;

            $countWaiting = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->where('status', 'waiting')->where("created_at", ">=", Carbon::now()->subDay())->where("created_at", "<=", Carbon::now())->count();
            $countProgreses = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->where('status', 'in progress')->where("created_at", ">=", Carbon::now()->subDay())->where("created_at", "<=", Carbon::now())->count();
            $countSuccess = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->where('status', 'success')->where("created_at", ">=", Carbon::now()->subDay())->where("created_at", "<=", Carbon::now())->count();
            $countApproved = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->where('status', 'approved')->where("created_at", ">=", Carbon::now()->subDay())->where("created_at", "<=", Carbon::now())->count();
            $declined = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->where('status', 'declined')->where("created_at", ">=", Carbon::now()->subDay())->where("created_at", "<=", Carbon::now())->count();
            $failed = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->where('status', 'failed')->where("created_at", ">=", Carbon::now()->subDay())->where("created_at", "<=", Carbon::now())->count();
            $failedAndDeclined = $failed + $declined;

            $waiting_transactions_chinese = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->with('asset')->where('status', 'waiting')->orderBy('id', 'desc')->get()->take(20);
            $success_transactions_chinese = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->where('status', 'success')->orderBy('id', 'desc')->get()->take(5);
            $failed_transactions_chinese = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->where('status', 'failed')->orderBy('id', 'desc')->get()->take(5);
            $in_progress_transactions_chinese = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->where('status', 'in progress')->orderBy('id', 'desc')->get()->take(5);
            $approved_transactions_chinese = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->where('status', 'approved')->orderBy('id', 'desc')->get()->take(5);

            // dd($cardTwentyFourHrs);

            return view(
                'admin.chinese_dashboard',
                compact([
                    'transactions', 'waiting_transactions', 'in_progress_transactions',
                    'users', 'users_count', 'notifications', 'usersChart',
                    'a_w_c', 'a_s_c', 'a_a_c', 'a_i_c',
                    'success_transactions_chinese', 'waiting_transactions_chinese', 'failed_transactions_chinese', 'in_progress_transactions_chinese', 'approved_transactions_chinese',
                    'buyCash', 'sellCash', 'buyCount', 'sellCount', 'pBuyCash',
                    'cardTwentyFourHrscount', 'nairaTwentyFourHrs', 'dollarTwentyFourHrs',
                    'countWaiting', 'countProgreses', 'countSuccess', 'countApproved', 'failedAndDeclined',
                    'success_transactions', 'failed_transactions',  'pSellCash', 'pBuyCount', 'pSellCount', 'users_wallet_balance', 'rubies_balance', 'company_balance'
                ])
            );
        } else if (Auth::user()->role == 557) { //business_Developer
            return (new SalesHelperController)->NewUserOldUserActiveUserFlow('old',NULL);
        } else if (Auth::user()->role == 556) { //Sales Personal
            return (new SalesController)->index();
        }
    }


    public function chineseDashboard()
    {

        $chineseDashboard = $this->dashboard();
        return view(
            'admin.chinese_dashboard',
            compact([
                'transactions', 'waiting_transactions', 'in_progress_transactions',
                'users', 'users_count', 'notifications', 'usersChart',
                'a_w_c', 'a_s_c', 'a_a_c', 'a_i_c',
                'success_transactions_chinese', 'waiting_transactions_chinese', 'failed_transactions_chinese', 'in_progress_transactions_chinese', 'approved_transactions_chinese',
                'buyCash', 'sellCash', 'buyCount', 'sellCount', 'pBuyCash',
                'cardTwentyFourHrscount', 'nairaTwentyFourHrs', 'dollarTwentyFourHrs',
                'countWaiting', 'countProgreses', 'countSuccess', 'countApproved', 'failedAndDeclined',
                'success_transactions', 'failed_transactions',  'pSellCash', 'pBuyCount', 'pSellCount', 'users_wallet_balance', 'rubies_balance', 'company_balance'
            ])
        );
    }


    public function payoutTransactions($type = '')
    {

        $buyCash = Transaction::where('status', 'success')->where('type', 'buy')->sum('amount_paid');
        $sellCash = Transaction::where('status', 'success')->where('type', 'sell')->sum('amount_paid');
        $buyCount = Transaction::where('status', 'success')->where('type', 'buy')->count();
        $sellCount = Transaction::where('status', 'success')->where('type', 'sell')->count();

        $pBuyCash = Transaction::where('status', 'success')->where('type', 'buy')->where('agent_id', Auth::user()->id)->sum('amount_paid');
        $pSellCash = Transaction::where('status', 'success')->where('type', 'sell')->where('agent_id', Auth::user()->id)->sum('amount_paid');
        $pBuyCount = Transaction::where('status', 'success')->where('type', 'buy')->where('agent_id', Auth::user()->id)->count();
        $pSellCount = Transaction::where('status', 'success')->where('type', 'sell')->where('agent_id', Auth::user()->id)->count();

        $s = Transaction::where('status', 'success')->count();
        $w = Transaction::where('status', 'waiting')->count();
        $d = Transaction::where('status', 'declined')->count();
        $f = Transaction::where('status', 'failed')->count();

        $borderColors = [
            "rgba(255, 99, 132, 1.0)",
            "rgba(22,160,133, 1.0)",
            "rgba(255, 205, 86, 1.0)",
            "rgba(51,105,232, 1.0)"
        ];
        $fillColors = [
            "rgba(255, 99, 132, 1.0)",
            "rgba(22,160,133, 1.0)",
            "rgba(255, 205, 86, 1.0)",
            "rgba(51,105,232, 1.0)"

        ];
        $usersChart = new UserChart;
        $usersChart->minimalist(true);
        $usersChart->labels(['Failed', 'Successful', 'Declined', 'Waiting']);
        $usersChart->dataset('Users by trimester', 'pie', [$f, $s, $d, $w])
            ->color($borderColors)
            ->backgroundcolor($fillColors);


        $transactions = Transaction::latest()->get()->take(5);
        $waiting_transactions = Transaction::where('status', 'waiting')->get()->take(5);
        $success_transactions = Transaction::where('status', 'success')->get()->take(5);
        $failed_transactions = Transaction::where('status', 'failed')->get()->take(5);
        $in_progress_transactions = Transaction::where('status', 'in progress')->get()->take(5);
        $approved_transactions = Transaction::where('status', 'approved')->get()->take(5);

        $users = User::latest()->get()->take(4);
        $verified_users = User::where('email_verified_at', '!=', null)->count();
        $notifications = Notification::where('user_id', 0)->latest()->get()->take(5);
        $users_count = User::all()->count();


        $rubies_balance = 0;
        $users_wallet_balance = NairaWallet::sum('amount');
        $company_balance = $rubies_balance - $users_wallet_balance;

        $transfer_charge = NairaWallet::where('account_number', 0000000001)->first()->amount;
        $sms_charge = NairaWallet::where('account_number', 0000000002)->first()->amount;

        $charges = $transfer_charge + $sms_charge;
        $old_charges = NairaTransaction::sum('charge');

        $withdraw_txns = NairaTransaction::where('transaction_type_id', 3)->sum('amount');
        $airtime_txns = NairaTransaction::where('transaction_type_id', 9)->sum('amount');
        $buy_txns_wallet = NairaTransaction::where('transaction_type_id', 5)->sum('amount');

        $g_txns = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->latest()->get()->take(4);

        $c_txns = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 1);
        })->latest()->get()->take(4);

        $n_txns = NairaTransaction::latest()->get()->take(4);

        /* Get count of transactions from when an agent was last activated */
        $au = Auth::user();
        $a_w_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->where('status', 'waiting')->count();
        $a_i_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->where('status', 'in progress')->count();
        $a_s_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->where('status', 'success')->count();
        $a_a_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->where('status', 'approved')->count();
        $all_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->count();

        $twentyFourHrsTransactions = Transaction::where("created_at", ">=", Carbon::now()->subDay())->where("created_at", "<=", Carbon::now())->where('status', 'success')
            ->whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->latest();
        $cardTwentyFourHrscount = $twentyFourHrsTransactions->count();
        $nairaTwentyFourHr = $twentyFourHrsTransactions->sum('amount_paid');
        $dollarTwentyFourHr = $twentyFourHrsTransactions->sum('amount');

        $nairaTwentyFourHrs = number_format($nairaTwentyFourHr);
        $dollarTwentyFourHrs = number_format($dollarTwentyFourHr);

        $countWaiting = Transaction::where('status', 'waiting')->count();
        $countProgreses = Transaction::where('status', 'in progress')->count();
        $countSuccess = Transaction::where('status', 'success')->count();
        $countApproved = Transaction::where('status', 'approved')->count();
        $failedAndDeclined = Transaction::where('status', 'failed')->where('status', 'declined')->count();

        // $waiting_transactions_chinese = Transaction::where('status', 'waiting')->where('card', '!=', 'BITCOIN')->get()->take(5);
        // $success_transactions_chinese = Transaction::where('status', 'success')->where('card', '!=', 'BITCOIN')->get()->take(5);
        // $failed_transactions_chinese = Transaction::where('status', 'failed')->where('card', '!=', 'BITCOIN')->get()->take(5);
        // $in_progress_transactions_chinese = Transaction::where('status', 'in progress')->where('card', '!=', 'BITCOIN')->get()->take(5);
        // $approved_transactions_chinese = Transaction::where('status', 'approved')->where('card', '!=', 'BITCOIN')->get()->take(5);


        // dd($assets->id);
        $assets = Payout::orderBy('id', 'desc')->first();

        if (!isset($assets->created_at)) {
            $payoutDate = '2020-01-13 10:03:52';
        } else {
            $payoutDate = $assets->created_at;
        }

        $payoutVolume = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->where("created_at", ">=", $payoutDate)->where('status', 'success')->sum('quantity');
        $assetsInNaira = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->where("created_at", ">=", $payoutDate)->where('status', 'success')->sum('amount_paid');

        $totalComm = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->where("created_at", ">=", $payoutDate)->where('status', 'success')->sum('commission');
        $countST = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->where("created_at", ">=", $payoutDate)->where('status', 'success')->count();
        $success_transactions = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->where("created_at", ">=", $payoutDate)->where('status', 'success')->latest()->get();
        $giftcard_tranx_count = $success_transactions->count();
        $total_traded_asset = 0;
        $total_chinese_amount = 0;
        foreach ($success_transactions as $st) {
            $total_traded_asset += ($st->amount * $st->quantity);
            $total_chinese_amount += ($st->amount_paid + $st->commission);
        }
        if ($type != 'all') {
            $success_transactions = $success_transactions->take(500);
        }

        $assetsInNaira = $assetsInNaira + $totalComm;

        // dd($assets->);


        // dd($cardTwentyFourHrs);

        return view(
            'admin.payout_transactions',
            compact([
                'payoutVolume', 'assetsInNaira', 'countST', 'totalComm',
                'giftcard_tranx_count', 'total_traded_asset', 'total_chinese_amount',
                'transactions', 'waiting_transactions', 'in_progress_transactions',
                'users', 'users_count', 'notifications', 'usersChart',
                'a_w_c', 'a_s_c', 'a_a_c', 'a_i_c',
                'buyCash', 'sellCash', 'buyCount', 'sellCount', 'pBuyCash',
                'cardTwentyFourHrscount', 'nairaTwentyFourHrs', 'dollarTwentyFourHrs',
                'countWaiting', 'countProgreses', 'countSuccess', 'countApproved', 'failedAndDeclined',
                'success_transactions', 'failed_transactions',  'pSellCash', 'pBuyCount', 'pSellCount', 'users_wallet_balance', 'rubies_balance', 'company_balance'
            ])
        );
    }

    public function payOutHistory(Request $request)
    {
        $s = Transaction::where('status', 'success')->count();
        $w = Transaction::where('status', 'waiting')->count();
        $d = Transaction::where('status', 'declined')->count();
        $f = Transaction::where('status', 'failed')->count();

        $borderColors = [
            "rgba(255, 99, 132, 1.0)",
            "rgba(22,160,133, 1.0)",
            "rgba(255, 205, 86, 1.0)",
            "rgba(51,105,232, 1.0)"
        ];
        $fillColors = [
            "rgba(255, 99, 132, 1.0)",
            "rgba(22,160,133, 1.0)",
            "rgba(255, 205, 86, 1.0)",
            "rgba(51,105,232, 1.0)"

        ];
        $usersChart = new UserChart;
        $usersChart->minimalist(true);
        $usersChart->labels(['Failed', 'Successful', 'Declined', 'Waiting']);
        $usersChart->dataset('Users by trimester', 'pie', [$f, $s, $d, $w])
            ->color($borderColors)
            ->backgroundcolor($fillColors);



        $assets = Payout::orderBy('created_at', 'desc')->first();

        $payoutHistory =  Payout::orderBy('id', 'desc');
        $segment = null;

        if ($request->start) {
            $segment .= Carbon::parse($request->start)->format('d-M-Y');
            $payoutHistory = $payoutHistory->whereDate('created_at', ">=", $request->start);
            if ($request->end) {
                $payoutHistory = $payoutHistory->whereDate('created_at', "<=", $request->end);
                $segment .= " to " . Carbon::parse($request->end)->format('d-M-Y');
            }
        }

        $payoutHistory = $payoutHistory->get();
        // dd($cardTwentyFourHrs);

        return view(
            'admin.payout_history',
            compact([
                'assets', 'usersChart', 'payoutHistory', 'segment'
            ])
        );
    }

    public function payout()
    {
        // $payoutVolume = Transaction::where("created_at",">=",Carbon::now()->subDay())->where("created_at","<=",Carbon::now())->where('status', 'success');

        $assets = Payout::orderBy('id', 'desc')->first();


        if (!isset($assets->created_at)) {
            $payoutDate = '2020-01-13 10:03:52';
        } else {
            $payoutDate = $assets->created_at;
        }

        $assetsInNaira = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->where("created_at", ">=", $payoutDate)->where('status', 'success')->sum('amount_paid');
        $countST = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->where("created_at", ">=", $payoutDate)->where('status', 'success')->count();

        $success_transactions = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->where("created_at", ">=", $payoutDate)->where('status', 'success')->get();
        $totalComm = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->where("created_at", ">=", $payoutDate)->where('status', 'success')->sum('commission');
        $giftcard_tranx_count = $success_transactions->count();
        $total_traded_asset = 0;
        $total_chinese_amount = 0;
        foreach ($success_transactions as $st) {
            $total_traded_asset += ($st->amount * $st->quantity);
            $total_chinese_amount += ($st->amount_paid + $st->commission);
        }

        // dd($countST);

        if ($countST < 1) {
            return redirect()->back()->with('error', 'Nothing to wipe');
        }

        $payout = Payout::create([
            'card_asset_volume' => $giftcard_tranx_count,
            'card_volume_in_naira' => $assetsInNaira,
            'success_transactions' => $countST,
            'traded_asset_amount' => $total_traded_asset,
            'total_chinese_amount' => $total_chinese_amount,
            'total_commission' => $totalComm,
            'wiped_by' => Auth::user()->id,
        ]);
        return redirect()->back()->with('success', 'Transactions was wipe successfully');
    }


    public function countTransaction()
    {
        $waiting_transactions = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->where('status', 'waiting')->count();
        $in_progress_transactions = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->where('status', 'in progress')->count();
        return response()->json([
            'waiting_transaction' => $waiting_transactions,
            'in_progress_transactions' => $in_progress_transactions
        ]);
    }





    /* TRANSACTIONS */

    public function transactions(Request $request)
    {
        $transactions = Transaction::with('user')->orderBy('updated_at', 'desc');
        $segment = 'All';

        $tranx =  Transaction::with('user')->WhereHas('asset', function ($q) {
            $q->where('is_crypto', 0);
        })->orderBy('updated_at', 'desc')->where('status', 'success');

        if (isset($request['start']) and isset($request['end'])) {
            $from = $request['start'];
            $to = $request['end'];
            $tranx = $tranx->whereBetween('created_at', [$from, $to])->latest();
            $transactions = $transactions->whereBetween('created_at', [$from, $to])->latest();
            // if(Auth::user()->role == 444 OR Auth::user()->role == 449){
            //     $transactions = $transactions->WhereHas('asset', function($q){
            //         $q->where('is_crypto', 0);
            //     });
            // }
            $segment = Carbon::parse($request['start'])->format('D d M y') . ' - ' . Carbon::parse($request['end'])->format('D d M Y') . ' Asset';
        }

        $totalTransactions = $tranx->count();
        $totalVol = $tranx->sum('amount');
        $totalComm = $tranx->sum(DB::raw('IFNULL(commission, 0)'));
        $totalChineseAmt = $tranx->sum('amount_paid') - $totalComm;

        $tt = $tranx->selectRaw('DATE(created_at) as date, count(id) as d_total')->groupBy('created_at')->pluck('d_total');

        $totalAvgPerToday = 0;

        // if ($totalAvgPerToday > 0) {
        $totalAvgPerToday = ceil($tt->sum() / $tt->count());
        // }

        if (Auth::user()->role == 444 or Auth::user()->role == 449) {
            $transactions = $transactions->whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            });
        }
        $card_price_total = $transactions->sum('card_price');
        $cash_value_total = $transactions->sum('amount_paid');
        $asset_value_total = $transactions->sum('amount');
        $total_transactions = $transactions->count();
        $transactions = $transactions->paginate(1000);
        // return $transactions;
        return view('admin.transactions', compact([
            'transactions', 'total_transactions', 'segment',
            'totalTransactions', 'totalVol', 'totalComm', 'totalChineseAmt',
            'totalAvgPerToday', 'card_price_total', 'cash_value_total', 'asset_value_total'
        ]));
    }

    public function gcTransactionsForHara($type = '') {
        $transactions =  Transaction::with(['user'])->WhereHas('asset', function ($q) {
            $q->where('is_crypto', 0);
        })->orderBy('updated_at', 'desc')->take(20);

        if ($type != '') {
            $transactions = $transactions->where('status',$type);
        }

        $transactions = $transactions->get();
        return response()->json([
            'data' => [
                'transactions' => $transactions
            ],
        ], 200);
    }

    public function gcTransactionForHara($id) {
        $transaction = Transaction::with(['batchPops' =>function ($query) {
            $query->with('user');
        }])->find($id);
        $transaction->batchPops->each(function ($item) {
            $item->path = asset('storage/pop/'.$item->path);
        });
        return response()->json([
            'data' => $transaction
        ], 200);
    }

    public function search_tnx(Request $request)
    {
        // dd($request);
        if ($request->segment == 'All') {
            $search = $request->search;
            $conditions = ['uid', 'card', 'type', 'country', 'card_type', 'status'];
            $transactions = Transaction::where(function ($query) use ($conditions, $search) {
                foreach ($conditions as $column)
                    $query->orWhere($column, 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('first_name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%');
                        });
            });
            if (Auth::user()->role == 444 or Auth::user()->role == 449) {
                $transactions = $transactions->WhereHas('asset', function ($q) {
                    $q->where('is_crypto', 0);
                })->paginate(100);
            }
            $transactions = $transactions->paginate(100);



            $segment = $request->segment;

            return view('admin.transactions', compact(['transactions', 'segment']));
        }
        if ($request->segment == 'Buy' || $request->segment == 'Sell') {
            $status = $request->segment;
            $search = $request->search;
            $conditions = ['uid', 'card', 'country', 'card_type', 'status'];
            $transactions = Transaction::where('type', $status)->latest()->where(function ($query) use ($conditions, $search) {
                foreach ($conditions as $column)
                    $query->orWhere($column, 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('first_name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%');
                        });
            });
            if (Auth::user()->role == 444 or Auth::user()->role == 449) {
                $transactions = $transactions->WhereHas('asset', function ($q) {
                    $q->where('is_crypto', 0);
                })->paginate(100);
            }
            $transactions = $transactions->paginate(100);


            $segment = $status;
            return view('admin.transactions', compact(['transactions', 'segment']));
        }

        if ($request->segment == 'Utility') {
            $search = $request->search;
            $conditions = ['reference_id', 'amount', 'type', 'status'];
            $transactions = UtilityTransaction::whereNotNull('id')->orderBy('created_at', 'desc')->where(function ($query) use ($conditions, $search) {
                foreach ($conditions as $column)
                    $query->orWhere($column, 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('first_name', 'like', '%' . $search . '%');
                        });
            });
            if (Auth::user()->role == 444 or Auth::user()->role == 449) {
                $transactions = $transactions->WhereHas('asset', function ($q) {
                    $q->where('is_crypto', 0);
                })->paginate(100);
            }
            $transactions = $transactions->paginate(100);
            return view('admin.utility-transactions', compact('transactions'));
        }
        if ($request->segment == 'Gift Card' || $request->segment == 'Crypto') {
            $id = $request->segment == 'Gift Card' ? 0 : 1;
            $search = $request->search;
            $conditions = ['uid', 'card', 'type', 'country', 'card_type', 'status'];
            $transactions = Transaction::whereHas('asset', function ($query) use ($id) {
                $query->where('is_crypto', $id);
            })->latest()->where(function ($query) use ($conditions, $search) {
                foreach ($conditions as $column)
                    $query->orWhere($column, 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('first_name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%');
                        });
            })->paginate(100);

            $segment = $request->segment;
            return view('admin.transactions', compact(['transactions', 'segment']));
        }

        if ($request->segment == 'All Wallet') {
            $search = $request->search;
            $conditions = ['reference', 'status'];
            $transactions = NairaTransaction::latest()->where(function ($query) use ($conditions, $search) {
                foreach ($conditions as $column)
                    $query->orWhere($column, 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('first_name', 'like', '%' . $search . '%');
                        })->orWhereHas('transactionType', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
            });
            if (Auth::user()->role == 444 or Auth::user()->role == 449) {
                $transactions = $transactions->WhereHas('asset', function ($q) {
                    $q->where('is_crypto', 0);
                })->paginate(100);
            }
            $transactions = $transactions->paginate(100);
            $segment = 'All Wallet';
            $total = NairaTransaction::latest()->sum('amount');


            return view('admin.naira_transactions', compact(['segment', 'transactions', 'total']));
        }
        if (
            $request->segment == 'success' || $request->segment == 'approved' || $request->segment == 'in progress'
            || $request->segment == 'waiting' || $request->segment == 'declined' || $request->segment == 'failed'
        ) {
            $search = $request->search;
            $conditions = ['uid', 'card', 'type', 'country', 'card_type'];
            $status = $request->segment;

            $transactions = Transaction::where('status', $status)->latest()->where(function ($query) use ($conditions, $search) {
                foreach ($conditions as $column)
                    $query->orWhere($column, 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('first_name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%');
                        });
            });
            if (Auth::user()->role == 444 or Auth::user()->role == 449) {
                $transactions = $transactions->WhereHas('asset', function ($q) {
                    $q->where('is_crypto', 0);
                })->paginate(100);
            }
            $transactions = $transactions->paginate(100);
            $segment = $status;
            return view('admin.transactions', compact(['transactions', 'segment']));
        }
    }

    public function buyTransac(Request $request)
    {

        $category = Transaction::with('asset')
            ->select('card_id')
            ->where('card_id', '!=', null)
            ->distinct('card_id')
            ->get();
        $accountant = Transaction::with('accountant')
            ->select('accountant_id')
            ->where('accountant_id', '!=', null)
            ->distinct('accountant_id')
            ->get();
        $status = Transaction::select('Status')->distinct('Status')->get();
        $transactions = Transaction::where('type', 'buy')->orderBy('updated_at', 'desc');
        $segment = 'Buy';
        if (isset($request['start']) and isset($request['end'])) {
            $from = $request['start'];
            $to = $request['end'];
            $transactions = $transactions->whereBetween('created_at', [$from, $to])->latest();
            if (Auth::user()->role == 444 or Auth::user()->role == 449) {
                $transactions = $transactions->WhereHas('asset', function ($q) {
                    $q->where('is_crypto', 0);
                });
            }
            $segment = Carbon::parse($request['start'])->format('D d M y') . ' - ' . Carbon::parse($request['end'])->format('D d M Y') . ' Asset';
        }

        $card_price_total = $transactions->sum('card_price');
        $cash_value_total = $transactions->sum('amount_paid');
        $asset_value_total = $transactions->sum('amount');
        $total_transactions = $transactions->count();
        if (Auth::user()->role == 444 or Auth::user()->role == 449) {
            $transactions = $transactions->with('user')->whereHas('asset', function ($query) {
                    $query->where('is_crypto', 0);
                });
        }
        $transactions = $transactions->paginate(1000);
        return view('admin.transactions', compact([
            'transactions', 'segment', 'accountant', 'status', 'category', 'total_transactions', 'asset_value_total', 'cash_value_total', 'card_price_total'
        ]));
    }

    public function sellTransac(Request $request)
    {
        $category = Transaction::with('asset')
            ->select('card_id')
            ->where('card_id', '!=', null)
            ->distinct('card_id')
            ->get();
        $accountant = Transaction::with('accountant')
            ->select('accountant_id')
            ->where('accountant_id', '!=', null)
            ->distinct('accountant_id')
            ->get();
        $segment = 'Sell';
        $status = Transaction::select('Status')->distinct('Status')->get();
        $transactions = Transaction::where('type', 'sell')->orderBy('updated_at', 'desc');
        if (isset($request['start']) and isset($request['end'])) {
            $from = $request['start'];
            $to = $request['end'];
            $transactions = $transactions->whereBetween('created_at', [$from, $to])->latest();
            if (Auth::user()->role == 444 or Auth::user()->role == 449) {
                $transactions = $transactions->WhereHas('asset', function ($q) {
                    $q->where('is_crypto', 0);
                });
            }
            $segment = Carbon::parse($request['start'])->format('D d M y') . ' - ' . Carbon::parse($request['end'])->format('D d M Y') . ' Asset';
        }
        $card_price_total = $transactions->sum('card_price');
        $cash_value_total = $transactions->sum('amount_paid');
        $asset_value_total = $transactions->sum('amount');
        $total_transactions = $transactions->count();
        if (Auth::user()->role == 444 or Auth::user()->role == 449) {
            $transactions = $transactions->with('user')->whereHas('asset', function ($query) {
                    $query->where('is_crypto', 0);
                });
        }
        $transactions = $transactions->paginate(1000);
        return view('admin.transactions', compact([
            'transactions', 'segment', 'accountant', 'status', 'category', 'total_transactions', 'asset_value_total', 'cash_value_total', 'card_price_total'
        ]));
    }

    public function currencyTransactions($card_id, $currency, Request $request)
    {
        $segment = null;
        $category = Transaction::with('asset')
            ->select('card_id')
            ->where('card_id', '!=', null)
            ->distinct('card_id')
            ->get();
        $accountant = Transaction::with('accountant')
            ->select('accountant_id')
            ->where('accountant_id', '!=', null)
            ->distinct('accountant_id')
            ->get();
        $status = Transaction::select('Status')->distinct('Status')->get();
        $transactions = Transaction::where('type', 'sell')->where('card_id', $card_id)->latest();

        $start = null;
        if(isset($request['start']))
        {
            $transactions = $transactions->where('created_at','>=',$request['start']." 00:00:00");
            $segment = Carbon::parse($request['start'])->format('D d M y');
            $start = $request['start'];
        }

        $end = null;
        if(isset($request['end']))
        {
            $transactions = $transactions->where('created_at','<=',$request['end']." 23:59:59");
            $segment .=' - ' . Carbon::parse($request['end'])->format('D d M y');
            $end = $request['end'];
        }
        $segment .= " ".$currency;

        $card_price_total = $transactions->sum('card_price');
        $cash_value_total = $transactions->sum('amount_paid');
        $asset_value_total = $transactions->sum('amount');
        $total_transactions = $transactions->count();
        if (Auth::user()->role == 444 or Auth::user()->role == 449) {
            $transactions = $transactions->with('user')->whereHas('asset', function ($query) {
                    $query->where('is_crypto', 0);
                });
        }

        if(isset($request['downloader']) AND $request['downloader'] == 'csv'){
            return (new UsdtTransactionsExport(143,$request['start'],$request['end']))->download('usdtTransactions.xlsx');
        }
        $transactions = $transactions->paginate(1000);
        return view('admin.transactions', compact([
            'transactions', 'segment', 'accountant', 'status', 'category', 'total_transactions', 'asset_value_total', 'cash_value_total', 'card_price_total',
            'start','end'
        ]));
    }

    public function txnByStatus($status, Request $request)
    {
        $type = Transaction::select('type')->distinct('type')->get();
        $category = Transaction::with('asset')
            ->select('card_id')
            ->where('card_id', '!=', null)
            ->distinct('card_id')
            ->get();
        $accountant = Transaction::with('accountant')
            ->select('accountant_id')
            ->where('accountant_id', '!=', null)
            ->distinct('accountant_id')
            ->get();
        $segment = $status;
        $transactions = Transaction::where('status', $status)->orderBy('updated_at', 'desc');
        if (isset($request['start']) and isset($request['end'])) {
            $from = $request['start'];
            $to = $request['end'];
            $transactions = $transactions->whereBetween('created_at', [$from, $to])->latest();
            if (Auth::user()->role == 444 or Auth::user()->role == 449) {
                $transactions = $transactions->WhereHas('asset', function ($q) {
                    $q->where('is_crypto', 0);
                });
            }
            $segment = Carbon::parse($request['start'])->format('D d M y') . ' - ' . Carbon::parse($request['end'])->format('D d M Y') . ' Asset';
        }
        $card_price_total = $transactions->sum('card_price');
        $cash_value_total = $transactions->sum('amount_paid');
        $asset_value_total = $transactions->sum('amount');
        $total_transactions = $transactions->count();
        if (Auth::user()->role == 444 or Auth::user()->role == 449) {
            $transactions = $transactions->with('user')->orderBy('updated_at', 'desc')
            ->whereHas('asset', function ($query) {
                    $query->where('is_crypto', 0);
                });
        }
        $transactions = $transactions->paginate(1000);
        return view('admin.transactions', compact([
            'transactions', 'segment', 'type', 'accountant', 'category', 'total_transactions', 'asset_value_total', 'cash_value_total', 'card_price_total'
        ]));
    }

    public function assignedTransac()
    {
        $transactions = Auth::user()->assignedTransactions()->where('status', 'waiting')->get();
        foreach ($transactions as $t) {
            if ($t->user->accounts->count() > 0) {
                $t->bank_name = $t->user->accounts()->first()['bank_name'];
                $t->acct_num = $t->user->accounts()->first()['account_number'];
            }
        }

        return view('admin.assigned-transactions', compact(['transactions']));
    }

    public function userVerificationTracking(Request $request){
        $verifications = Verification::orderBy('id','DESC');
        if($request->start){
            $verifications = $verifications->where('created_at','>=',$request->start." 00:00:00");
            if($request->end){
                $verifications = $verifications->where('created_at','<=',$request->end." 23:59:59");
            }
        }
        
        $verifications = $verifications->with('user','verifiedUserBy')->get();

        $waiting_verifications = $verifications->where('status','Waiting');
        $waiting_verifications_count = $waiting_verifications->count();

        $waiting_verifications_today = $verifications->where('status','Waiting')->where('created_at', '>=', Carbon::today());
        $waiting_verifications_today_count = $waiting_verifications_today->count();

        $avg_response = 0;

        $total = $verifications->count();
        foreach ($verifications as $v) {
            if($v->user){
                $v->name = $v->user->first_name." ".$v->user->last_name;
            }else{
                $v->user = 'Not Available';
            }

            if($v->verifiedUserBy){
                $v->verifiedBy = $v->verifiedUserBy->first_name." ".$v->verifiedUserBy->last_name;
            }else{
                $v->verifiedBy = 'Not Available';
            }

            if($v->status == 'Waiting'){
                $time = now()->diffInSeconds($v->created_at);
                $v->verifyTime = CarbonInterval::seconds($time)->cascade()->forHumans();
                $avg_response += $time;
            }else{
                $time = $v->updated_at->diffInSeconds($v->created_at);
                $v->verifyTime = CarbonInterval::seconds($time)->cascade()->forHumans();
                $avg_response += $time;
            }
        }

        if($total == 0){
            $average = 0;
        } else {
            $average = $avg_response/$total;
        }
        $verification_average = CarbonInterval::seconds($average)->cascade()->forHumans();
        $verifications = $verifications->paginate(100);
        return view('admin.verification_tracking',compact(['verifications','verification_average','total','waiting_verifications_count','waiting_verifications_today_count']));
    }

    public function verificationTimeData(){

    }

    public function assetTransac($id)
    {
        $type = Transaction::select('type')->distinct('type')->get();
        $category = Transaction::with('asset')
            ->select('card_id')
            ->where('card_id', '!=', null)
            ->distinct('card_id')
            ->get();

        $accountant = Transaction::with('accountant')
            ->select('accountant_id')
            ->where('accountant_id', '!=', null)
            ->distinct('accountant_id')
            ->get();

        $status = Transaction::select('Status')->distinct('Status')->get();

        $transactions = Transaction::whereHas('asset', function ($query) use ($id) {
            $query->where('is_crypto', $id);
        })->orderBy('updated_at', 'DESC');

        $card_price_total = $transactions->sum('card_price');
        $cash_value_total = $transactions->sum('amount_paid');
        $asset_value_total = $transactions->sum('amount');
        $total_transactions = $transactions->count();
        $transactions = $transactions->paginate(1000);

        $segment = 'Gift Card';
        if ($id == 1) {
            $segment = 'Crypto';
        }


        return view('admin.transactions', compact([
            'transactions', 'segment', 'type', 'accountant', 'status', 'category', 'total_transactions', 'asset_value_total', 'cash_value_total', 'card_price_total'
        ]));
    }

    public function assetTransactionsSortByDate(Request $request)
    {
        // dd($request);
        $type = Transaction::select('type')->distinct('type')->get();
        $category = Transaction::with('asset')
            ->select('card_id')
            ->where('card_id', '!=', null)
            ->distinct('card_id')
            ->get();
        $accountant = Transaction::with('accountant')
            ->select('accountant_id')
            ->where('accountant_id', '!=', null)
            ->distinct('accountant_id')
            ->get();
        $status = Transaction::select('Status')->distinct('Status')->get();
        $data = $request->validate([
            'start' => 'required|date|string',
            'end' => 'required|date|string',
        ]);
        $transactions = Transaction::where('created_at', '>=', $data['start'])->where('created_at', '<=', $data['end']);
        if ($request->type != 'null') {
            $transactions = $transactions->where('type', $request->type);
        }
        if ($request->category != 'null') {
            $transactions = $transactions->where('card_id', $request->category);
        }
        if ($request->Accountant != 'null') {
            $transactions = $transactions->where('accountant_id', $request->Accountant);
        }
        if ($request->status != 'null') {
            $transactions = $transactions->where('status', $request->status);
        }
        $card_price_total = $transactions->sum('card_price');
        $cash_value_total = $transactions->sum('amount_paid');
        $asset_value_total = $transactions->sum('amount');
        $total_transactions = $transactions->count();
        $transactions = $transactions->paginate(200);
        $segment = Carbon::parse($data['start'])->format('D d M y') . ' - ' . Carbon::parse($data['end'])->format('D d M Y') . ' Asset';

        return view('admin.transactions', compact([
            'segment', 'transactions', 'type', 'accountant', 'status', 'category', 'total_transactions', 'asset_value_total', 'cash_value_total', 'card_price_total'
        ]));
    }

    public function getTransac($id)
    {
        $transac = Transaction::find($id);
        return response()->json($transac);
    }




    public function viewTransac($id, $uid)
    {
        $transaction = Transaction::find($id);

        $cards = Card::all();

        return view('admin.transaction', compact(['transaction', 'cards']));
    }

    public function deleteTransac($id)
    {
        $rate = Transaction::find($id);
        return response()->json($rate->delete());
    }

    public function updateTransaction($id, $status) //to accept or decline a new transaction
    {
        $t = Transaction::find($id);
        $t->status = $status;
        $t->save();

        $user = User::where('email', $t->user_email)->first();
        $title = 'Transaction update';
        $body = 'The status of your transaction with id ' . $t->uid . ', has been updated to ' . $status;
        $not = Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
        ]);

        broadcast(new TransactionUpdated($user));
        /* Mail::to($user->email)->send(new DantownNotification($title, $body)); */
        return response()->json(['success' => true]);
    }


    public function walletTransactions($id = null)
    {
        $type = NairaTransaction::with('transactionType')
            ->select('transaction_type_id')
            ->where('transaction_type_id', '!=', null)
            ->distinct('transaction_type_id')
            ->get();
        $status = NairaTransaction::select('status')->distinct('status')->get();
        if ($id == null) {
            $transactions = NairaTransaction::latest()->orderBy('created_at', 'desc');
            $segment = 'All Wallet';
            $total = $transactions->sum('amount');
        } else {
            $transactions = NairaTransaction::where('transaction_type_id', $id)->orderBy('created_at', 'desc');
            $segment = TransactionType::find($id)->name;
            $total = $transactions->sum('amount');
        }
        $total_tnx = $transactions->count();
        $total_amount_paid = $transactions->sum('amount_paid');
        $total_charges = $transactions->sum('charge');
        $transactions = $transactions->paginate(1000);

        $complianceCheck = NairaTransaction::orderBy('id','DESC')->first();
        return view('admin.naira_transactions', compact([
            'segment', 'transactions', 'total', 'type', 'status', 'total_tnx', 'total_amount_paid', 'total_charges','complianceCheck'
        ]));
    }
    public function walletTransactionsSortByDate(Request $request)
    {
        $type = NairaTransaction::with('transactionType')
            ->select('transaction_type_id')
            ->where('transaction_type_id', '!=', null)
            ->distinct('transaction_type_id')
            ->get();
            
        $status = NairaTransaction::select('status')->distinct('status')->get();
        $data = $request->validate([
            'start' => 'required|date|string',
            'end' => 'required|date|string',
        ]);

        $transactions = NairaTransaction::where('created_at', '>=', $data['start'])->where('created_at', '<=', $data['end']);


        if ($request->status != 'null') {
            $transactions = $transactions
                ->where('status', $request->status);
            // ->paginate(1000);
        }
        if ($request->type != 'null') {
            $transactions = $transactions
                ->where('transaction_type_id', $request->type);
        }
        $total_tnx = $transactions->count();
        $total_amount_paid = $transactions->sum('amount_paid');
        $total_charges = $transactions->sum('charge');
        $transactions = $transactions->paginate(1000);

        $segment = Carbon::parse($data['start'])->format('D d M y') . ' - ' . Carbon::parse($data['end'])->format('D d M Y') . ' Wallet';
        $total = $transactions->sum('amount');

        $complianceCheck = NairaTransaction::orderBy('id','DESC')->first();
        return view('admin.naira_transactions', compact([
            'segment', 'transactions', 'total', 'type', 'status', 'total_tnx', 'total_amount_paid', 'total_charges','complianceCheck'
        ]));
    }

    public function adminWallet()
    {
        $n = Auth::user()->nairaWallet;
        if (!$n) {
            return redirect()->route('user.portfolio')->with(['error' => 'No Naira wallet associated to this account']);
        }
        $credit_txns = NairaTransaction::whereIn('transaction_type_id', [5, 16, 17])->latest()->paginate(1000);
        $debit_txns = NairaTransaction::whereIn('transaction_type_id', [4, 6])->latest()->paginate(1000);

        return view('admin.admin_wallet', compact(['n',  'credit_txns', 'debit_txns']));
    }


    /* Old transfer charges before the database was remodified to include seperate wallets for charges */
    public function oldTransferCharges(Request $r)
    {
        if (!$r->start || !$r->end) {
            $transactions = NairaTransaction::latest()->get();
            $total = $transactions->sum('charge');
        } else {

            $transactions = NairaTransaction::where('created_at', '>=', $r->start)->where('created_at', '<=', $r->end)->get();
            $total = $transactions->sum('charge');
        }


        return view('admin._charges', compact(['transactions', 'total']));
    }
    public function transferCharges(Request $r)
    {
        $transfer_charge = NairaWallet::where('account_number', 0000000001)->first();
        $sms_charge = NairaWallet::where('account_number', 0000000002)->first();

        $transfer_charges_txns = NairaTransaction::where('transfer_charge', '!=', 0)->latest()->paginate(1000);
        $sms_charges_txns = NairaTransaction::where('sms_charge', '!=', 0)->latest()->paginate(1000);


        return view('admin.charges', compact(['transfer_charge', 'sms_charge', 'transfer_charges_txns', 'sms_charges_txns']));
    }

    public function clearTransferCharges(Request $r)
    {
        if (Hash::check($r->password, Auth::user()->password) == false) {
            return redirect()->back()->with(['error' => 'Wrong password entered']);
        }

        $transfer_charge = NairaWallet::where('account_number', 0000000001)->first();
        $transfer_charge->amount = 0;
        $transfer_charge->save();

        return back()->with(['success' => 'Transfer charges cleared successfully']);
    }

    public function clearSmsCharges(Request $r)
    {
        if (Hash::check($r->password, Auth::user()->password) == false) {
            return redirect()->back()->with(['error' => 'Wrong password entered']);
        }

        $sms_charge = NairaWallet::where('account_number', 0000000002)->first();
        $sms_charge->amount = 0;
        $sms_charge->save();

        return back()->with(['success' => 'SMS charges cleared successfully']);
    }


    public function getUser($email)
    {
        $user = User::where('email', $email)->first();
        return response()->json($user);
    }

    public function users(Request $request)
    {
        $request->session()->forget('search');
        $users = User::with('nairaWallet')->orderBy('created_at', 'desc')->get();

        if(isset($request['downloader']) AND $request['downloader'] == 'csv'){
            return Excel::download(new UserDataTransaction($users), 'userData.xlsx');
        }

        $users = $users->paginate(1000);
        return view('admin.users', compact(['users']));
    }

    public function user_search(Request $request)
    {
        if ($request->search) {
            $request->session()->put('search', $request->search);
        }
        if ($request->session()->has('search')) {
            $search = $request->session()->get('search');
            $users = User::orderBy('created_at', 'desc')
                ->where('first_name', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('phone', 'LIKE', '%' . $search . '%')
                ->orWhere('phone', 'LIKE', '%' . $search . '%')
                ->orWhere('id', 'LIKE', '%' . $search . '%')
                ->get();
                if(isset($request['downloader']) AND $request['downloader'] == 'csv'){
                    return Excel::download(new UserDataTransaction($users), 'userData.xlsx');
                }

            $users = $users->paginate(1000);
            return view('admin.users', compact(['users']));
        }
    }

    public function verifiedUsers()
    {
        $users = User::where('email_verified_at', '!=', null)->get();
        return view('admin.users', compact(['users']));
    }

    public function user($id, $email)
    {
        $user = User::find($id);
        $transactions = $user->transactions;

        $wallet_txns = NairaTransaction::where('cr_user_id', $user->id)->orWhere('dr_user_id', $user->id)->latest()->paginate(2000);
        $ledger = UserController::ledgerBalance($id)->getData();

        $log = $user->nairaTransactions()->where('cr_user_id', '!=', $user->id)->where('dr_user_id', '!=', $user->id)->get();


        if ($user->btcWallet) {
            $btc_wallet = $user->btcWallet;

            $client = new Client();

            $url = env('TATUM_URL') . '/ledger/account/' . $btc_wallet->account_id;
            $res = $client->request('GET', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')]
            ]);
            $res = json_decode($res->getBody());
            $btc_wallet->balance = $res->balance->availableBalance;


            $url = env('TATUM_URL') . '/ledger/transaction/account?pageSize=50';
            $get_txns = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
                "json" => ["id" => $btc_wallet->account_id]
            ]);

            $btc_transactions = json_decode($get_txns->getBody());
            foreach ($btc_transactions as $t) {
                $x = \Str::limit($t->created, 10, '');
                $time = \Carbon\Carbon::parse((int)$x);
                $t->created = $time->setTimezone('Africa/Lagos');
            }
        } else {
            $btc_wallet = null;
            $btc_transactions = [];
        }


        $usdt_wallet = $user->usdtWallet;
        $usdt_transactions = [];
        if ($usdt_wallet) {
            $client = new Client();
            $url = env('TATUM_URL') . '/ledger/account/' . $usdt_wallet->account_id;
            $res = $client->request('GET', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
            ]);

            $accounts = json_decode($res->getBody());
            $rate = LiveRateController::usdtRate();

            $usdt_wallet->balance = $accounts->balance->availableBalance;
            $usdt_wallet->usd = $usdt_wallet->balance  * $rate;

            $url = env('TATUM_URL') . '/ledger/transaction/account?pageSize=50';
            $get_txns = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
                "json" => ["id" => $usdt_wallet->account_id]
            ]);

            $usdt_transactions = json_decode($get_txns->getBody());
            foreach ($usdt_transactions as $t) {
                $x = \Str::limit($t->created, 10, '');
                $time = \Carbon\Carbon::parse((int)$x);
                $t->created = $time->setTimezone('Africa/Lagos');

                if (!isset($t->senderNote)) {
                    $t->senderNote = 'Sending Tron';
                }
            }
        }

        $verifications = $user->verifications;

        return view('admin.user', compact([
            'user', 'transactions', 'wallet_txns', 'btc_wallet', 'log',
            'usdt_wallet', 'usdt_transactions',
            'btc_transactions', 'ledger', 'verifications'
        ]));
    }

    public function searchUser(Request $r)
    {
        $users = User::where('email', 'like', '%' . $r->q . '%')->orWhere('first_name', 'like', '%' . $r->q . '%')->orWhere('last_name', 'like', '%' . $r->q . '%')->paginate(20);
        /* dd($users); */
        return view('admin.users', compact(['users']));
    }


    public function walletId(Request $request)
    {
        $setting = Setting::updateOrCreate(
            [
                'name' => $request->name,
            ],
            [
                'value' => $request->value,
            ]
        );
        return redirect()->back()->with(['success' => 'Saved']);
    }

    public function notification()
    {
        $notifications = Notification::where('user_id', 0)->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.notification', compact(['notifications']));
    }


    public function transNotification(Request $request)
    {
        $month =  $request->input('month');
        if ($month) {
            $notifications = Auth::user()->notifications()->whereMonth('created_at', $month)->paginate(10);
        } else {
            $notifications = Auth::user()->notifications()->paginate(10);
        }

        return view('admin.transnotification', compact('notifications', 'month'));
    }


    public function readNot($id)
    {
        $n = Auth::user()->notifications->where('id', $id)->first();
        $n->is_seen = 1;
        $n->save();
        return response()->json(['success' => true]);
    }


    public function addNotification(Request $r)
    {
        $n = new Notification();
        $n->title = $r->title;
        $n->body = $r->body;
        $n->save();

        return redirect()->back()->with(['success' => 'Notification sent']);
    }

    public function editNotification(Request $r)
    {
        $n = Notification::find($r->id);
        $n->title = $r->title;
        $n->body = $r->body;
        $n->save();

        return redirect()->back()->with(['success' => 'Notification updated']);
    }

    public function getTopTraders(Request $request){
        //year is the current year
        $options = [
            'join' => ', ',
            'parts' => 2,
            'syntax' => CarbonInterface::DIFF_ABSOLUTE,
        ];
        $_users = array();
        if($request->start AND $request->end){
            $start = Carbon::parse($request->start)->startOfMonth();
            $end = Carbon::parse($request->end)->endOfMonth();
        } else{
            $start = now()->startOfYear();
            $end = now()->endOfYear();
        }

        $transactions = Transaction::with('user')
        ->where('status','success')
        ->where('created_at','>=', $start)
        ->where('created_at','<=', $end)
        ->get()
        ->groupBy('user_id');

        foreach($transactions as $t){
            $user = $t[0]->user;
            $user->transactionCount = $t->count();
            $user->transactionAmountUSD = $t->sum('amount');
            $user->transactionAmountNGN = $t->sum('amount_paid');
            $user->lastTranxDate = $user->transactions->first()->created_at->format('d M Y h:ia');
            $user->ltd_date = $user->transactions->first()->created_at->diffForHumans(now(),$options)." ago";
            
            $_users[] = $user;
        }

        $segment = $start->format("d M Y")." to ".$end->format("d M Y");
        $users = collect($_users)->sortByDesc('transactionAmountUSD')->take(1000);
        return view('admin.top_traders', compact('users','segment'));
    }

    public function getNotification($id)
    {
        $not = Notification::find($id);
        return response()->json($not);
    }

    public function deleteNotification($id)
    {
        $not = Notification::find($id);
        return response()->json($not->delete());
    }

    public function downloadUserDb()
    {
        return view("admin.userdb");
    }


    public function downloadUserDbsearch()
    {
        return Excel::download(new DownloadUsers, 'dantown_users.xlsx');
    }
}
