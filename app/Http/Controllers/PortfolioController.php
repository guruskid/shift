<?php

namespace App\Http\Controllers;

use App\Bank;
use App\Http\Controllers\Admin\BusinessDeveloperController;
use App\NairaTransaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use \App\Http\Controllers\GeneralSettings;
use App\WithdrawalQueueRange;

class PortfolioController extends Controller
{
    public function view()
    {
        if (!Auth::user()->btcWallet) {
            return back()->with(['error' => 'Please create a Bitcoin wallet to continue']);
        }
        $naira = Auth::user()->nairaWallet()->count();
        $nw = Auth::user()->nairaWallet;

        $client = new Client();
        // $url = env('TATUM_URL') . '/tatum/rate/BTC?basePair=USD';
        // $res = $client->request('GET', $url, ['headers' => ['x-api-key' => env('TATUM_KEY')]]);
        // $res = json_decode($res->getBody());
        $btc_rate = LiveRateController::btcRate();

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . Auth::user()->btcWallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $btc = json_decode($res->getBody());

        $btc_wallet = Auth::user()->btcWallet;
        $btc_wallet->balance = $btc->balance->availableBalance;
        $btc_wallet->usd = $btc_wallet->balance  * $btc_rate;

        $tron_wallet = null;
        if (Auth::user()->tronWallet) {
            $eth_url = env('TATUM_URL') . '/ledger/account/' . Auth::user()->tronWallet->account_id;
            $res = $client->request('GET', $eth_url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')]
            ]);
            $eth = json_decode($res->getBody());

            $tron_wallet = Auth::user()->tronWallet;
            $tron_wallet->balance = $eth->balance->availableBalance;
        }

        $usdt_wallet = null;
        if (Auth::user()->usdtWallet) {
            $eth_url = env('TATUM_URL') . '/ledger/account/' . Auth::user()->usdtWallet->account_id;
            $res = $client->request('GET', $eth_url, [
                'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
            ]);
            $eth = json_decode($res->getBody());

            $usdt_wallet = Auth::user()->usdtWallet;
            $usdt_wallet->balance = $eth->balance->availableBalance;
        }

        $banks = Bank::all();

        return view('newpages.choosewallet', compact(['naira', 'btc_wallet', 'usdt_wallet', 'tron_wallet', 'nw', 'banks']));
    }


    public function nairaWallet()
    {
        \Artisan::call('naira:limit');
        $n = Auth::user()->nairaWallet;
        $banks = Bank::all();
        if (Auth::user()->pin == '') {
            return redirect()->route('user.portfolio');
        }

        if (!$n) {
            return redirect()->route('user.portfolio')->with(['error' => 'No Naira wallet associated to this account']);
        }

        if ($n->status == 'paused') {
            return redirect()->route('user.dashboard')->with(['error' => 'Naia wallet currently froozen, please contact support for more info']);
        }

        
        $nts = NairaTransaction::where('cr_user_id', Auth::user()->id)->orWhere('dr_user_id', Auth::user()->id)->orderBy('id', 'desc')->with('transactionType')->paginate(5);
        $dr_total = 0;
        $cr_total = 0;
        foreach ($nts as $t) {
            if ($t->cr_user_id == Auth::user()->id) {
                $t->trans_type = 'Credit';
                $cr_total += $t->amount;
            } else {
                $t->trans_type = 'Debit';
                $dr_total += $t->amount;
            }

            $pendingOrders = NairaTransaction::where('status','waiting')->whereDate('created_at', '>', $t->created_at)->count();
            $minutes = 0;

            if ($pendingOrders <= 5) {
                $minutes = 30;
            } elseif(($pendingOrders > 5 and $pendingOrders <= 10) ) {
                $minutes = 40;
            } elseif(($pendingOrders > 10 and $pendingOrders <= 20) ) {
                $minutes = 50;
            } elseif(($pendingOrders > 20 and $pendingOrders <= 30) ) {
                $minutes = 60;
            } elseif(($pendingOrders > 30) ) {
                $minutes = 60;
            }

            $pay_time = 'payment in '.$minutes.' minutes';
            $t->pay_time = $pay_time;
        }

        // return $nts;

        $daily_total = Auth::user()->nairaTransactions()->whereDate('created_at', now())->whereIn('status',['success','pending'])->whereIn('transaction_type_id', [3, 2])->sum('amount');
        $daily_rem = Auth::user()->daily_max - $daily_total;

        //check Monthly
        $monthly_total = Auth::user()->nairaTransactions()->whereYear('created_at', now())->whereIn('status',['success','pending'])->whereMonth('created_at', now())->whereIn('transaction_type_id', [3, 2])->sum('amount');
        $monthly_rem = Auth::user()->monthly_max - $monthly_total;

        $ref = \Str::random(2) . time();

        $setting = GeneralSettings::getSetting('NAIRA_WALLET_WITHDRAWALS');
        // $naira_ = GeneralSettings::getSetting('NAIRA_TRANSACTION_CHARGE');

        $naira_charge = GeneralSettings::getSettingValue('NAIRA_TRANSACTION_CHARGE');
        $userTrackingFreeTransfers = null;
        $tranx = UserController::successFulNairaTrx();
        if($tranx == 0)
        {
            $tranx = BusinessDeveloperController::freeWithdrawals();
            $userTrackingFreeTransfers = 'active';
        }        

        return view('newpages.nairawallet', compact(['n', 'banks', 'nts', 'cr_total', 'dr_total', 'ref', 'daily_rem', 'monthly_rem', 'setting','tranx','naira_charge','userTrackingFreeTransfers']));
    }
}
