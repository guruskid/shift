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
use Excel;
use App\NairaWallet;
use App\Payout;
use App\TransactionType;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
class ChineseController extends Controller
{

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


        $transactions = Transaction::latest()->get()->take($page_limit);
        $waiting_transactions = Transaction::where('status', 'waiting')->get()->take($page_limit);
        $success_transactions = Transaction::where('status', 'success')->get()->take($page_limit);
        $failed_transactions = Transaction::where('status', 'failed')->get()->take($page_limit);
        $in_progress_transactions = Transaction::where('status', 'in progress')->get()->take($page_limit);
        $approved_transactions = Transaction::where('status', 'approved')->get()->take($page_limit);

        $users = User::latest()->get()->take($page_limit);
        $verified_users = User::where('email_verified_at', '!=', null)->count();
        $notifications = Notification::where('user_id', 0)->latest()->get()->take($page_limit);
        $users_count = User::all()->count();


        /*  $client = new Client();
        $url = env('RUBBIES_API') . "/balanceenquiry";

        $response = $client->request('POST', $url, [
            'json' => [
                "accountnumber" => "0140963171"
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents()); */


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
        })->latest()->get()->take($page_limit);

        $c_txns = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 1);
        })->latest()->get()->take($page_limit);

        $n_txns = NairaTransaction::latest()->get()->take($page_limit);

        /* Get count of transactions from when an agent was last activated */
        $au = Auth::user();
        $a_w_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->where('status', 'waiting')->count();
        $a_i_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->where('status', 'in progress')->count();
        $a_s_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->where('status', 'success')->count();
        $a_a_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->where('status', 'approved')->count();
        $all_c = $au->assignedTransactions()->where('created_at', '>=', $au->updated_at)->count();


            $twentyFourHrsTransactions = Transaction::
            // where('status', 'waiting')
            where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')
            ->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')
            ->where("created_at",">=",Carbon::now()->subDay())
            ->where("created_at","<=",Carbon::now())
            ->where('status', 'success');
            // return $twentyFourHrsTransactions->count();
            $cardTwentyFourHrscount = $twentyFourHrsTransactions->count();
            $nairaTwentyFourHr = $twentyFourHrsTransactions->sum('amount_paid');
            $dollarTwentyFourHr= $twentyFourHrsTransactions->sum('amount');

            $nairaTwentyFourHrs = $nairaTwentyFourHr;
            $dollarTwentyFourHrs = $dollarTwentyFourHr;

            $countWaiting = Transaction::where('status', 'waiting')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->count();
            $countProgreses = Transaction::where('status', 'in progress')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->count();
            $countSuccess = Transaction::where('status', 'success')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->count();
            $countApproved = Transaction::where('status', 'approved')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->count();
            $declined = Transaction::where('status', 'declined')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->count();
            $failed = Transaction::where('status', 'failed')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->count();
            $failedAndDeclined = $failed + $declined;

            $waiting_transactions_chinese = Transaction::with('asset')->where('status', 'waiting')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->orderBy('id', 'desc')->get()->take($page_limit);
            $success_transactions_chinese = Transaction::where('status', 'success')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->orderBy('id', 'desc')->get()->take($page_limit);
            $failed_transactions_chinese = Transaction::where('status', 'failed')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->orderBy('id', 'desc')->get()->take($page_limit);
            $in_progress_transactions_chinese = Transaction::where('status', 'in progress')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->orderBy('id', 'desc')->get()->take($page_limit);
            $approved_transactions_chinese = Transaction::where('status', 'approved')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->orderBy('id', 'desc')->get()->take($page_limit);

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
                ]));

    }


    public function transactionsChinese()
    {
        $transactions = Transaction::with('user')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->latest()->paginate(1000);
        $segment = 'All';

        return view('admin.transactions', compact(['transactions', 'segment']));
    }


    public function payoutHistory()
    {
        $transactions = Transaction::with('user')->where('card', '!=', 'BITCOIN')->where('card', '!=', 'BITCOINS')->where('card', '!=', 'etherum')->where('card', '!=', 'ETHER')->latest()->paginate(1000);
        $segment = 'All';

        return view('admin.transactions', compact(['transactions', 'segment']));
    }

    public function chineseAdminUser()
    {
        $users = User::whereIn('role', [444, 449])->latest()->get();

        return view('admin.chineseadmin', compact('users'));
    }

    public function addChineseAdmin(Request $r)
    {
        $user = User::find($r->id);
        $user->role = 444;
        $user->status = 'waiting';
        $user->save();

        return back()->with(['success'=>'Admin added successfully']);
    }
    public function action($id, $action)
    {
        $user = User::find($id);
        if ($action == 'remove') {
            $user->role = 1;
        }
        else{
            $user->status = $action;
        }
        $user->save();
        return back()->with(['success'=>'Action Successful']);
    }

}
