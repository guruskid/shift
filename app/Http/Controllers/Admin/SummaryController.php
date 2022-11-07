<?php

namespace App\Http\Controllers\Admin;

use App\AccountantTimeStamp;
use App\BitcoinWallet;
use App\CryptoCurrency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Summary;
use App\Transaction;
use App\UtilityTransaction;
use App\NairaTransaction;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SummaryController extends Controller
{
    public function index($currency_id)
    {
        $today = Summary::whereDate('created_at', now())->where('crypto_currency_id', $currency_id)->first();
        if (!$today) {
            Summary::create([
                'crypto_currency_id' => $currency_id,
            ]);
        }
        $summaries = Summary::where('crypto_currency_id', $currency_id)->orderBy('id', 'desc')->paginate(30);
        $currency = CryptoCurrency::find($currency_id);
        switch ($currency_id) {
            case 1:
                $card_id = 102;
                break;
            case 2:
                $card_id = 137;
                break;
            case 5:
                $card_id = 141;
                break;
            case 7:
                $card_id = 143;
                break;
            default:
                return back()->with(['error' => 'There is an error with this page']);
                break;
        }

        return view('admin.summary.index', compact('summaries', 'currency', 'card_id'));
    }

    public function transactions(Summary $summary, $card_id)
    {
        $date = $summary->created_at;
        $sell_transactions = Transaction::where('card_id', $card_id)->where('type', 'sell')->whereDate('created_at', $date)->where('status', 'success')->get();
        $sell_btc = $sell_transactions->sum('quantity');
        $sell_usd = $sell_transactions->sum('amount');

        $ngn_sell_average = 0;
        $cumulative = 0;
        foreach ($sell_transactions as $t ) {
            $cumulative += ($t->quantity * $t->ngn_rate * $t->card_price );
        }
        // dd($cumulative);
        $ngn_sell_average = ($cumulative == 0 ? 1 : $cumulative) / ($sell_usd == 0 ? 1 : $sell_usd);

        try {
            $sell_average = $sell_usd / $sell_btc;
            // $sell_ngn_average = $sell_usd
        } catch (\Throwable $th) {
            $sell_average = 0;
        }

        $buy_transactions = Transaction::where('card_id', $card_id)->where('type', 'buy')->whereDate('created_at', $date)->where('status', 'success')->get();
        $buy_btc = $buy_transactions->sum('quantity');
        $buy_usd = $buy_transactions->sum('amount');
        try {
            $buy_average = $buy_usd / $buy_btc;
        } catch (\Throwable $th) {
            $buy_average = 0;
        }

        switch ($card_id) {
            case 102:
                $cur = 'BTC';
                break;
            case 137:
                $cur = 'ETH';
                break;
            case 141:
                $cur = 'TRX';
                break;
            case 143:
                $cur = 'USDT';
                break;
            default:
                return back()->with(['error' => 'There is an error with this page']);
                break;
        }

        return view('admin.summary.transactions', compact(
            'buy_transactions',
            'buy_btc',
            'buy_usd',
            'buy_average',
            'sell_transactions',
            'sell_btc',
            'sell_usd',
            'sell_average',
            'cur',
            'card_id',
            'ngn_sell_average'
        ));
    }

    public function sortTransactions(Request $request, $card_id)
    {
        $data = $request->validate([
            'start' => 'required|date|string',
            'end' => 'required|date|string',
        ]);


        $sell_transactions = Transaction::where('card_id', $card_id)->where('type', 'sell')
            ->where('created_at', '>=', $data['start'])->where('created_at', '<=', $data['end'])
            ->where('status', 'success')->get();


        $sell_btc = $sell_transactions->sum('quantity');
        $sell_usd = $sell_transactions->sum('amount');

        $ngn_sell_average = 0;
        $cumulative = 0;
        foreach ($sell_transactions as $t ) {
            $cumulative += ($t->quantity * $t->ngn_rate * $t->card_price);
        }
        $ngn_sell_average = ($cumulative == 0 ? 1 : $cumulative) / ($sell_usd == 0 ? 1 : $sell_usd);

        try {
            $sell_average = $sell_usd / $sell_btc;
        } catch (\Throwable $th) {
            $sell_average = 0;
        }

        $buy_transactions = Transaction::where('card_id', $card_id)->where('type', 'buy')
            ->where('created_at', '>=', $data['start'])->where('created_at', '<=', $data['end'])
            ->where('status', 'success')->get();

        $buy_btc = $buy_transactions->sum('quantity');
        $buy_usd = $buy_transactions->sum('amount');
        try {
            $buy_average = $buy_usd / $buy_btc;
        } catch (\Throwable $th) {
            $buy_average = 0;
        }

        switch ($card_id) {
            case 102:
                $cur = 'BTC';
                break;
            case 137:
                $cur = 'ETH';
                break;
            default:
                return back()->with(['error' => 'There is an error with this page']);
                break;
        }

        return view('admin.summary.transactions', compact(
            'buy_transactions',
            'buy_btc',
            'buy_usd',
            'buy_average',
            'sell_transactions',
            'sell_btc',
            'sell_usd',
            'sell_average',
            'cur',
            'card_id',
            'ngn_sell_average'
        ));
    }

    public function ledgerBalance()
    {
        $wallets = BitcoinWallet::all();

        foreach ($wallets as $wallet) {
            $transactions = $wallet->transactions()->where('status', 'success')->get();
            $wallet->in = $transactions->sum('credit');
            $wallet->out = $transactions->sum('debit');

            $wallet->lbal = $wallet->in - $wallet->out;
            $wallet->diff = $wallet->balance - $wallet->lbal;
        }

        return view('admin.bitcoin_wallet.wallet_balances', compact('wallets'));
    }


    public function summaryhomepage($month=null, $day = null, Request $request)
    {
        $request->session()->forget('RequestDetails');
        if($day)
        {
            $date = date('Y').'-'.$month.'-'.$day;
            $dates= Carbon::parse($date);

            $segment = $dates->format("M d");
            $showData = false;

            return view('admin.summary.JuniorAccountant.transaction',compact('day','month','showData','segment'));
        }
        elseif($month)
        {
            $days = cal_days_in_month(CAL_GREGORIAN,$month,date('Y'));
            $month_name = date("F",mktime(0,0,0,$month));

            $month_num = $month;
            return view('admin.summary.JuniorAccountant.index',compact('days','month_name','month_num'));
        }

        else{
            $month = [
                ['month'=>'january','number'=>1],
                ['month'=>'february','number'=>2],
                ['month'=>'march','number'=>3],
                ['month'=>'april','number'=>4],
                ['month'=>'may','number'=>5],
                ['month'=>'june','number'=>6],
                ['month'=>'july','number'=>7],
                ['month'=>'august','number'=>8],
                ['month'=>'september','number'=>9],
                ['month'=>'october','number'=>10],
                ['month'=>'november','number'=>11],
                ['month'=>'december','number'=>12],
            ];
            return view('admin.summary.JuniorAccountant.index',compact('month'));
        }
    }

    public function summary_tnx_category($month, $day, $category, Request $request)
    {
        if($request->session()->get('RequestDetails') != null){
            $sessionData = $request->session()->get('RequestDetails');
            $sessionData['category'] = $category;
            $request->session()->put('RequestDetails', $sessionData);
            return $this->sorting($request);
        }

        $summaryPage = new AccountSummaryController();
        return $summaryPage->index($month, $day, $category);
    }

    public function sorting(Request $request)
    {
        if(isset($request->all()['day'])|| isset($request->all()['month']))
        {
            $request->session()->put('RequestDetails', $request->all());
        }

        $requestDetails = session('RequestDetails');

        $summaryPage = new AccountSummaryController();
        return $summaryPage->sortTransactions($requestDetails);
    }

}
