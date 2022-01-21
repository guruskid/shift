<?php

namespace App\Http\Controllers;

use App\Bank;
use App\NairaTransaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use \App\Http\Controllers\GeneralSettings;

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
        $url = env('TATUM_URL') . '/tatum/rate/BTC?basePair=USD';
        $res = $client->request('GET', $url, ['headers' => ['x-api-key' => env('TATUM_KEY')]]);
        $res = json_decode($res->getBody());
        $btc_rate = $res->value;

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

        return view('newpages.choosewallet', compact(['naira', 'btc_wallet', 'usdt_wallet', 'tron_wallet', 'nw']));
    }


    public function nairaWallet()
    {
        \Artisan::call('naira:limit');
        $n = Auth::user()->nairaWallet;
        if (!$n) {
            return redirect()->route('user.portfolio')->with(['error' => 'No Naira wallet associated to this account']);
        }

        if ($n->status == 'paused') {
            return redirect()->route('user.dashboard')->with(['error' => 'Naia wallet currently froozen, please contact support for more info']);
        }



        $banks = Bank::all();
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
        }

        $daily_total = Auth::user()->nairaTransactions()->whereDate('created_at', now())->whereIn('transaction_type_id', [3, 2])->sum('amount');
        $daily_rem = Auth::user()->daily_max - $daily_total;

        //check Monthly
        $monthly_total = Auth::user()->nairaTransactions()->whereYear('created_at', now())->whereMonth('created_at', now())->whereIn('transaction_type_id', [3, 2])->sum('amount');
        $monthly_rem = Auth::user()->monthly_max - $monthly_total;

        $ref = \Str::random(2) . time();

        $setting = GeneralSettings::getSetting('NAIRA_WALLET_WITHDRAWALS');

        return view('newpages.nairawallet', compact(['n', 'banks', 'nts', 'cr_total', 'dr_total', 'ref', 'daily_rem', 'monthly_rem', 'setting']));
    }
}
