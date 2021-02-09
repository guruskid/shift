<?php

namespace App\Http\Controllers;

use App\Bank;
use App\NairaTransaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class PortfolioController extends Controller
{
    public function view()
    {
        $naira = Auth::user()->nairaWallet()->count();
        $nw = Auth::user()->nairaWallet;

        $res = json_decode(file_get_contents("https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd"));
        $btc_rate = $res->bitcoin->usd;
        $btc_wallet_bal  = Auth::user()->bitcoinWallet->balance ?? 0;
        $btc_usd = $btc_wallet_bal  * $btc_rate;

        //dd('holla');
        return view('newpages.choosewallet', compact(['naira', 'btc_usd', 'nw']));
    }


    public function nairaWallet()
    {

        $n = Auth::user()->nairaWallet;
        if (!$n) {
            return redirect()->route('user.portfolio')->with(['error' => 'No Naira wallet associated to this account']);
        }

        if ($n->status == 'paused') {
            return redirect()->route('user.dashboard')->with(['error' => 'Naia wallet currently froozen, please contact support for more info']);
        }

        switch (Auth::user()->v_progress) {
            case 25:
                Auth::user()->daily_max = 0;
                Auth::user()->monthly_max = 0;
                Auth::user()->save();
                break;

            case 50:
                Auth::user()->daily_max = 500000;
                Auth::user()->monthly_max = 5000000;
                Auth::user()->save();
                break;

            case 75:
                Auth::user()->daily_max = 2000000;
                Auth::user()->monthly_max = 60000000;
                Auth::user()->save();
                break;

            case 100:
                Auth::user()->daily_max = 5000000;
                Auth::user()->monthly_max = 90000000;
                Auth::user()->save();
                break;

            default:
                Auth::user()->daily_max = 30000;
                Auth::user()->monthly_max = 300000;
                Auth::user()->save();
                break;
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
        return view('newpages.nairawallet', compact(['n', 'banks', 'nts', 'cr_total', 'dr_total', 'ref', 'daily_rem', 'monthly_rem']));
    }
}
