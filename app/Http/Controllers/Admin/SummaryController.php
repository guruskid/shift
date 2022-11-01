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
            $show_data = false;

            return view('admin.summary.JuniorAccountant.transaction',compact('day','month','show_data','segment'));
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

    public function roundUpAmount($collection)
    {
        foreach($collection as $col)
        {
            $col->amount = round($col->amount);
        }
    }

    public function CryptoGiftCardTransactions($all_tnx,$bitcoin_total_tnx,$giftcards_totaltnx_buy
    ,$giftcards_totaltnx_sell,$USDTranx,$data)
    {
        $segment = $data['segment'];
        $accountant= $data['accountant'];

        $show_data= $data['show_data'];
        $show_category= $data['show_category'];

        $day= $data['day'];
        $month= $data['month'];

        $show_summary= $data['show_summary'];
        $accountant_name = $data['accountant_name'];

        //*All Transactions
        $all_tnx = $all_tnx->unique('id')->sortByDesc('updated_at');

        $this->roundUpAmount($all_tnx);

        $all_tnx_count = $all_tnx->where('status', 'success')->count();
        $allCountBuy = $all_tnx->where('status', 'success')->where('type', 'buy')->count();
        $allCountSell = $all_tnx->where('status','success')->where('type', 'sell')->count();

        $allNairaAmountBuy = $all_tnx->where('status', 'success')->where('type', 'buy')->sum('amount_paid');
        $allNairaAmountSell = $all_tnx->where('status','success')->where('type', 'sell')->sum('amount_paid');

        //*Bitcoin Transaction
        $bitcoin_total_tnx = $all_tnx->where('status', 'success')->where('card_id',102);

        $bitcoin_total_tnx_buy = $bitcoin_total_tnx->where('type', 'buy');
        $bitcoin_total_tnx_sell = $bitcoin_total_tnx->where('type', 'sell');

        $BTCbuyQuantity = $bitcoin_total_tnx_buy->sum('quantity');
        $BTCsellQuantity = $bitcoin_total_tnx_sell->sum('quantity');

        $BTCbuyUsdAmount = $bitcoin_total_tnx_buy->sum('amount');
        $BTCbuyNairaAmount = $bitcoin_total_tnx_buy->sum('amount_paid');

        $BTCbuyCount = $bitcoin_total_tnx_buy->count();
        $BTCsellUsdAmount = $bitcoin_total_tnx_sell->sum('amount');

        $BTCsellNairaAmount = $bitcoin_total_tnx_sell->sum('amount_paid');
        $BTCsellCount = $bitcoin_total_tnx_sell->count();


        //* USDT Transaction
        $this->roundUpAmount($USDTranx);

        $USDTbuyTranx = $USDTranx->unique('id')->where('type', 'buy');
        $USDTsellTranx = $USDTranx->unique('id')->where('type', 'sell');

        $USDTbuyQuantity = $USDTbuyTranx->sum('quantity');
        $USDTsellQuantity = $USDTsellTranx->sum('quantity');

        $USDTbuyUsdAmount = $USDTbuyTranx->sum('amount');
        $USDTbuyNairaAmount = $USDTbuyTranx->sum('amount_paid');

        $USDTbuyCount = $USDTbuyTranx->count();
        $USDTsellUsdAmount = $USDTsellTranx->sum('amount');

        $USDTsellNairaAmount = $USDTsellTranx->sum('amount_paid');
        $USDTsellCount = $USDTsellTranx->count();

        //*GiftCard Transaction BUY
        $this->roundUpAmount($giftcards_totaltnx_buy);

        $giftcards_totaltnx_buy = $giftcards_totaltnx_buy->unique('id');
        $giftcards_totaltnx_buy_amount = 0;

        foreach ($giftcards_totaltnx_buy as $st) {
            $giftcards_totaltnx_buy_amount += ($st->amount * $st->quantity);
        }

        $giftcards_totaltnx_buy_amount_naira = $giftcards_totaltnx_buy->sum('amount_paid');
        $giftcards_totaltnx_buy = $giftcards_totaltnx_buy->count();

        //*GiftCard Transaction SELL
        $this->roundUpAmount($giftcards_totaltnx_sell);

        $giftcards_totaltnx_sell = $giftcards_totaltnx_sell->unique('id');
        $giftcards_totaltnx_sell_amount = 0;

        foreach ($giftcards_totaltnx_sell as $st) {
            $giftcards_totaltnx_sell_amount += ($st->amount * $st->quantity);
        }
        $giftcards_totaltnx_sell_amount_naira = $giftcards_totaltnx_sell->sum('amount_paid');
        $giftcards_totaltnx_sell = $giftcards_totaltnx_sell->count();

        return view('admin.summary.JuniorAccountant.transaction',compact([
            'segment','accountant','all_tnx','all_tnx_count','show_data','show_category','day','month','show_summary','allNairaAmountBuy','allNairaAmountSell','allCountBuy','allCountSell',

            'giftcards_totaltnx_buy','giftcards_totaltnx_sell','giftcards_totaltnx_buy_amount','giftcards_totaltnx_sell_amount','giftcards_totaltnx_sell_amount_naira','giftcards_totaltnx_buy_amount_naira',

            'BTCbuyCount','BTCsellCount','BTCbuyUsdAmount','BTCsellUsdAmount','BTCbuyQuantity','BTCsellQuantity','BTCsellNairaAmount','BTCbuyNairaAmount',

            'USDTbuyQuantity','USDTsellQuantity','USDTbuyUsdAmount','USDTbuyNairaAmount','USDTbuyCount','USDTsellUsdAmount','USDTsellNairaAmount','USDTsellCount'

        ]));
    }

    public function UtilitiesTransactions($util_tnx,$data)
    {
        $segment = $data['segment'];
        $accountant= $data['accountant'];

        $show_data= $data['show_data'];
        $show_category= $data['show_category'];

        $day= $data['day'];
        $month= $data['month'];

        $show_summary= $data['show_summary'];
        $accountant_name = $data['accountant_name'];

        $util_tnx = $util_tnx->unique('id')->sortByDesc('updated_at');
        $util_total_tnx = $util_tnx->where('status','success')->count();

        $util_tnx_amount = $util_tnx->where('status','success')->sum('amount');
        $util_tnx_fee = $util_tnx->where('status','success')->sum('convenience_fee');

        $util_amount_paid = $util_tnx->where('status','success')->sum('total');

        return view('admin.summary.JuniorAccountant.transaction',compact([
            'segment','accountant','show_data','show_category','day','month','show_summary',
            'util_tnx','util_total_tnx', 'util_tnx_amount', 'util_tnx_fee' , 'util_amount_paid',

        ]));
    }

    public function GetAverageResponseTime($data_collection)
    {
        $tnx = $data_collection;
        $avg_response = 0;

        $total = $tnx->count();
        foreach ($tnx as $t) {
            if($t->status == 'pending'){
                $avg_response += now()->diffInSeconds($t->created_at);
            }else{
                $avg_response += $t->updated_at->diffInSeconds($t->created_at);
            }
        }
        if($total == 0){
            return 0;
        }

        $average = $avg_response/$total;
        return (CarbonInterval::seconds($average)->cascade()->forHumans());

    }
    public function PayBridgeDeposit($nw_deposit_tnx,$data)
    {
        $segment = $data['segment'];
        $accountant= $data['accountant'];

        $show_data= $data['show_data'];
        $show_category= $data['show_category'];

        $day= $data['day'];
        $month= $data['month'];

        $show_summary= $data['show_summary'];
        $accountant_name = $data['accountant_name'];

        $nw_deposit_tnx = $nw_deposit_tnx->unique('id')->sortByDesc('updated_at');
        $nw_deposit_tnx_total = $nw_deposit_tnx->where('status','success')->count();

        $nw_deposit_amount_paid = $nw_deposit_tnx->where('status','success')->sum('amount_paid');
        $nw_deposit_tnx_charges = $nw_deposit_tnx->where('status','success')->sum('charge');
        $nw_deposit_total_amount = $nw_deposit_tnx->where('status','success')->sum('amount');

        $nw_deposit_pending_total = $nw_deposit_tnx->where('status','pending')->count();
        $nw_deposit_pending_amount = $nw_deposit_tnx->where('status','pending')->sum('amount');

        $deposit_total = NairaTransaction::latest()->where('status','pending')->where('transaction_type_id',1)->get();

        $deposit_total_pending = $deposit_total->where('status','pending')->count();
        $deposit_total_pending_amount = $deposit_total->where('status','pending')->sum('amount');

        $averageResponseTime = $this->GetAverageResponseTime($nw_deposit_tnx);
        return view('admin.summary.JuniorAccountant.transaction',compact([
            'segment','accountant','show_data','show_category','day','month','show_summary',
            'nw_deposit_tnx','nw_deposit_tnx_total','nw_deposit_amount_paid','nw_deposit_tnx_charges','nw_deposit_total_amount',
            'nw_deposit_pending_total','nw_deposit_pending_amount','deposit_total_pending','deposit_total_pending_amount','averageResponseTime'
        ]));
    }

    public function PayBridgeWithdrawal($nw_withdrawal_tnx,$data)
    {
        $segment = $data['segment'];
        $accountant= $data['accountant'];

        $show_data= $data['show_data'];
        $show_category= $data['show_category'];

        $day= $data['day'];
        $month= $data['month'];

        $show_summary= $data['show_summary'];
        $accountant_name = $data['accountant_name'];

        $nw_withdrawal_tnx = $nw_withdrawal_tnx->unique('id')->sortByDesc('updated_at');
        $nw_withdrawal_tnx_total = $nw_withdrawal_tnx->where('status','success')->count();

        $nw_withdrawal_amount_paid = $nw_withdrawal_tnx->where('status','success')->sum('amount_paid');
        $nw_withdrawal_tnx_charges = $nw_withdrawal_tnx->where('status','success')->sum('charge');

        $nw_withdrawal_total_amount = $nw_withdrawal_tnx->where('status','success')->sum('amount');

        $nw_withdrawal_pending_total = $nw_withdrawal_tnx->where('status','pending')->count();
        $nw_withdrawal_pending_amount = $nw_withdrawal_tnx->where('status','pending')->sum('amount');

        $withdrawal_total = NairaTransaction::latest()->where('status','pending')->where('transaction_type_id',3)->get();
        $withdrawal_total_pending = $withdrawal_total->where('status','pending')->count();

        $withdrawal_total_pending_amount = $withdrawal_total->where('status','pending')->sum('amount');
        $averageResponseTime = $this->GetAverageResponseTime($nw_withdrawal_tnx);

        return view('admin.summary.JuniorAccountant.transaction',compact([
            'segment','accountant','show_data','show_category','day','month','show_summary',
            'nw_withdrawal_tnx','nw_withdrawal_tnx_total','nw_withdrawal_amount_paid','nw_withdrawal_tnx_charges','nw_withdrawal_total_amount',
            'nw_withdrawal_pending_total','nw_withdrawal_pending_amount','withdrawal_total_pending','withdrawal_total_pending_amount','averageResponseTime'
        ]));
    }

    public function PayBridgeOthers($nw_other_tnx,$data)
    {
        $segment = $data['segment'];
        $accountant= $data['accountant'];

        $show_data= $data['show_data'];
        $show_category= $data['show_category'];

        $day= $data['day'];
        $month= $data['month'];

        $show_summary= $data['show_summary'];
        $accountant_name = $data['accountant_name'];

        $nw_other_tnx = $nw_other_tnx->unique('id')->sortByDesc('updated_at');
        $nw_other_tnx_total = $nw_other_tnx->where('status','success')->count();

        $nw_other_amount_paid = $nw_other_tnx->where('status','success')->sum('amount_paid');
        $nw_other_tnx_charges = $nw_other_tnx->where('status','success')->sum('charge');
        $nw_other_total_amount = $nw_other_tnx->where('status','success')->sum('amount');

        $nw_other_pending_total = $nw_other_tnx->where('status','pending')->count();
        $nw_other_pending_amount = $nw_other_tnx->where('status','pending')->sum('amount');

        $other_total = NairaTransaction::latest()->where('status','pending')
        ->where('transaction_type_id','!=',1)->where('transaction_type_id','!=',3)->get();

        $other_total_pending = $other_total->where('status','pending')->count();
        $other_total_pending_amount = $other_total->where('status','pending')->sum('amount');

        $averageResponseTime = $this->GetAverageResponseTime($nw_other_tnx);
        return view('admin.summary.JuniorAccountant.transaction',compact([
            'segment','accountant','show_data','show_category','day','month','show_summary',
            'nw_other_tnx','nw_other_tnx_total','nw_other_amount_paid','nw_other_tnx_charges','nw_other_total_amount',
                'nw_other_pending_total','nw_other_pending_amount','other_total_pending','other_total_pending_amount','averageResponseTime'
            ]));
    }

    public function summary_tnx_category($month, $day, $category, Request $request)
    {
        if($request->session()->get('RequestDetails') != null){
            $sessionData = $request->session()->get('RequestDetails');
            $sessionData['category'] = $category;
            $request->session()->put('RequestDetails', $sessionData);
            return $this->sorting($request);
        }

        $show_summary = true;

        $show_data = true;
        $show_category = $category ?: null;

        $date = date('Y')."-$month-$day";

        $current_day_value = Carbon::parse($date);
        $segment = $current_day_value->format("M d");

        $user = Auth::user();
        $accountant = User::whereIn('role', [889,777,775])->get();

        $accountant_timestamp = AccountantTimeStamp::where('created_at','>=',$current_day_value->format('Y-m-d')." 00:00:00")
        ->where('created_at','<=',$current_day_value->format('Y-m-d')." 23:59:59")->where('user_id',$user->id)->orderBy('id','desc')->first();

        $data = array(
            'segment'=>$segment,
            'accountant'=>$accountant,
            'show_data' =>$show_data,
            'show_category' => $show_category,
            'day' =>$day,
            'month' => $month,
            'show_summary' =>$show_summary,
            'accountant_name' => null
        );
        if($category == "all"){

            //**All Transactions */
            $all_tnx = Transaction::whereNotNull('id')->orderBy('created_at', 'desc');
            $all_tnx = $this->category_listing($user,$accountant_timestamp,$all_tnx,$current_day_value);
            $all_tnx = $all_tnx->get();

            //**Bitcoin Transactions */
            $bitcoin_total_tnx = Transaction::whereNotNull('id');
            $bitcoin_total_tnx = $this->category_listing($user,$accountant_timestamp,$bitcoin_total_tnx,$current_day_value);
            $bitcoin_total_tnx = $bitcoin_total_tnx->where('status', 'success')->where('card','bitcoin')->get();

            //**USDT Transactions */
            $USDTranx = Transaction::whereNotNull('id');
            $USDTranx = $this->category_listing($user,$accountant_timestamp,$USDTranx,$current_day_value);
            $USDTranx = $USDTranx->where('status', 'success')->where('card_id',143)->get();

            //**GiftCards Buy Transactions */
            $giftcards_totaltnx_buy = Transaction::whereNotNull('id')->whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->where('type', 'buy');
            $giftcards_totaltnx_buy = $this->category_listing($user,$accountant_timestamp,$giftcards_totaltnx_buy,$current_day_value);
            $giftcards_totaltnx_buy = $giftcards_totaltnx_buy->where('status', 'success')->get();

            //**GiftCards Sell Transactions */
            $giftcards_totaltnx_sell = Transaction::whereNotNull('id')->whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
             })->where('type', 'sell');
             $giftcards_totaltnx_sell = $this->category_listing($user,$accountant_timestamp,$giftcards_totaltnx_sell,$current_day_value);
             $giftcards_totaltnx_sell = $giftcards_totaltnx_sell->where('status', 'success')->get();

            return $this->CryptoGiftCardTransactions($all_tnx,$bitcoin_total_tnx,$giftcards_totaltnx_buy
            ,$giftcards_totaltnx_sell,$USDTranx,$data);
        }

        if($category == "utilities"){
            //**Utilities Transaction */
            $util_tnx = UtilityTransaction::whereNotNull('id')->orderBy('updated_at', 'desc');
            $util_tnx = $this->category_listing($user,$accountant_timestamp,$util_tnx,$current_day_value);

            return $this->UtilitiesTransactions($util_tnx->get(),$data);
        }

        if($category == "paybridge"){

            //**PayBridge Deposit Transaction */
            $nw_deposit_tnx = NairaTransaction::latest()->orderBy('updated_at','desc')->where('transaction_type_id',1);
            $nw_deposit_tnx = $this->category_listing($user,$accountant_timestamp,$nw_deposit_tnx,$current_day_value);
            $nw_deposit_tnx = $nw_deposit_tnx->get();
            return $this->PayBridgeDeposit($nw_deposit_tnx,$data);
        }
        if($category == "paybridgewithdrawal"){

            //**PayBridge Withdrawal Transaction */
            $nw_withdrawal_tnx = NairaTransaction::latest()->orderBy('updated_at','desc')->where('transaction_type_id',3);
            $nw_withdrawal_tnx = $this->category_listing($user,$accountant_timestamp,$nw_withdrawal_tnx,$current_day_value);
            $nw_withdrawal_tnx = $nw_withdrawal_tnx->get();
            return $this->PayBridgeWithdrawal($nw_withdrawal_tnx,$data);

        }

        if($category == "paybridgeothers"){

            //**Other PayBridge Transaction */
            $nw_other_tnx = NairaTransaction::latest()->orderBy('updated_at','desc')->where('transaction_type_id','!=',1)->where('transaction_type_id','!=',3);
            $nw_other_tnx = $this->category_listing($user,$accountant_timestamp,$nw_other_tnx,$current_day_value);
            $nw_other_tnx = $nw_other_tnx->get();
            return $this->PayBridgeOthers($nw_other_tnx,$data);
        }

    }



    public function category_listing($user,$accountant_timestamp,$value,$current_day_value)
    {
        if (($user->role == 777 OR $user->role == 775  ) && !empty($accountant_timestamp)) {
            $value = $value
            ->where('updated_at', '>=', $accountant_timestamp->created_at)
            ->where('updated_at', '<=', $accountant_timestamp->updated_at);
        }
        else{
            $value = $value
            ->where('updated_at','>=', $current_day_value->format('Y-m-d')." 00:00:00")
            ->where('updated_at','<=', $current_day_value->format('Y-m-d')." 23:59:59");
        }
        return $value;
    }

    public function sorting(Request $request)
    {
        if(isset($request->all()['day'])|| isset($request->all()['month']))
        {
            $request->session()->put('RequestDetails', $request->all());
        }

        $requestDetails = session('RequestDetails');

        if($requestDetails['startdate'] == null AND $requestDetails['enddate'] == null AND $requestDetails['Accountant'] == "null"){
            return back()->with(['error' => 'Sorting Field is Empty ']);
        }

        $day = $requestDetails['day'];
        $month = $requestDetails['month'];

        $show_category = $requestDetails['category'];
        $show_data = true;

        $show_summary = (Auth::user()->role != 777 || Auth::user()->role != 775 || $requestDetails['Accountant'] == 'null') ? true : false;
        $startDate =  $requestDetails['startdate'];
        $endDate = $requestDetails['enddate'];

        $accountant_name = null;
        if(!isset($requestDetails['Accountant']) || $requestDetails['Accountant'] == 'null' )
        {
            $user = Auth::user();
        }
        else{
            $user = User::find($requestDetails['Accountant']);
            $accountant_name = $user->first_name ?: $user->email;
        }

        $accountant = User::whereIn('role', [777,775,889])->get();
        if($requestDetails['Accountant'] == "null")
        {
            return $this->sortByDate($requestDetails,$startDate,$endDate,$accountant,$show_data,$show_category,$day,$month,$show_summary,$accountant_name);
        }else{
            return $this->sortByAccountant($requestDetails,$day,$month,$show_category,$show_data,$show_summary,$startDate,$endDate,$user,$accountant,$accountant_name);
        }
    }

    public function juniorAccountantSort($start, $end, $segment,$accountant_timestamp, $user,$show_category,$data)
    {
        if($accountant_timestamp->count() == 0){
            $accountant_timestamp = AccountantTimeStamp::whereDate('activeTime','<=',$end[0])
            ->where('user_id',$user->id)->orderBy('id','DESC')->limit(10)->get();
        }

        $start = Carbon::parse($start[0]." ".$start[1].":00");
        $end = Carbon::parse($end[0]." ".$end[1].":59");

        $allTransactions = collect();
        $giftCardTransactions = collect();

        $utilityTransactions = collect();
        $payBridgeTransactions = collect();

        foreach ($accountant_timestamp as $at) {
            //*all Transactions
            $allTranx = Transaction::orderBy('updated_at','desc');
            $allTranx = $this->sortingByAccountantTimestamp($allTranx, $at->activeTime, $at->inactiveTime);
            $allTransactions = $allTransactions->concat($allTranx);

            //*GiftCard Transactions
            $giftTranx = Transaction::where('status', 'success')->whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->orderBy('updated_at','desc');

            $giftTranx = $this->sortingByAccountantTimestamp($giftTranx, $at->activeTime, $at->inactiveTime);
            $giftCardTransactions = $giftCardTransactions->concat($giftTranx);

            //*Utility Transactions
            $utilityTranx = UtilityTransaction::orderBy('updated_at','desc');
            $utilityTranx = $this->sortingByAccountantTimestamp($utilityTranx, $at->activeTime, $at->inactiveTime);
            $utilityTransactions = $utilityTransactions->concat($utilityTranx);

            //*PayBridge Transactions
            $payBridgeTranx = NairaTransaction::orderBy('updated_at','desc');
            $payBridgeTranx = $this->sortingByAccountantTimestamp($payBridgeTranx, $at->activeTime, $at->inactiveTime);
            $payBridgeTransactions = $payBridgeTransactions->concat($payBridgeTranx);
        }
        $allTransactions = $allTransactions->whereBetween('updated_at', [$start, $end]);
        $giftCardTransactions =$giftCardTransactions->whereBetween('updated_at', [$start, $end]);

        $utilityTransactions = $utilityTransactions->whereBetween('updated_at', [$start, $end]);
        $payBridgeTransactions = $payBridgeTransactions->whereBetween('updated_at', [$start, $end]);

        $gcBuyTranx = $giftCardTransactions->where('type','buy');
        $gcSellTranx = $giftCardTransactions->where('type','sell');

        if($show_category == "all")
        {
            $BTCtotalTranx = $allTransactions->where('status','success')->where('card','bitcoin');
            $USDTranx = $allTransactions->where('status', 'success')->where('card_id',143);

            return $this->CryptoGiftCardTransactions($allTransactions,$BTCtotalTranx,$gcBuyTranx
            ,$gcSellTranx,$USDTranx,$data);
        }

        if($show_category == "utilities")
        {
            return $this->UtilitiesTransactions($utilityTransactions, $data);
        }

        if($show_category == "paybridge")
        {
            $pbDepositTranx = $payBridgeTransactions->where('transaction_type_id',1);
            return $this->PayBridgeDeposit($pbDepositTranx, $data);
        }

        if($show_category == "paybridgewithdrawal")
        {
            $pbWithdrawalTranx = $payBridgeTransactions->where('transaction_type_id',3);
            return $this->PayBridgeWithdrawal($pbWithdrawalTranx, $data);
        }

        if($show_category == "paybridgeothers")
        {
            $pbOtherTranx = $payBridgeTransactions->where('transaction_type_id','!=',1)->where('transaction_type_id','!=',3);
            return $this->PayBridgeOthers($pbOtherTranx, $data);
        }


    }

    public function sortByAccountant($requestDetails,$day,$month,$show_category,$show_data,$show_summary,$startDate,$endDate,$user,$accountant,$accountant_name)
    {
        if($startDate)
        {
            $start = explode("T",$startDate);
            $startDate = $start[0];
            $requestDetails['startdate'] = $start[0];
        }

        if($endDate)
        {
            $end = explode("T",$endDate);
            $endDate = $end[0];
            $requestDetails['enddate'] = $end[0];
        }

        if($startDate == "")
        {
            $startDate = date('Y')."-$month-$day";
        }

        if($requestDetails['Accountant'] != 'null'){
            $segment = $accountant_name." ".Carbon::parse($startDate)->format('d F Y');
        }

        if($requestDetails['startdate'] != null AND $requestDetails['Accountant'] != 'null')
        {
            $segment = $accountant_name." ".Carbon::parse($startDate)->format('d F Y');

            if($requestDetails['enddate'] != null){
                $segment .= " to ".Carbon::parse($endDate)->format('d F Y');
            }
        }

        //*Export Data
        $data = array(
            'segment'=>$segment,
            'accountant'=>$accountant,
            'show_data' =>$show_data,
            'show_category' => $show_category,
            'day' =>$day,
            'month' => $month,
            'show_summary' =>$show_summary,
            'accountant_name' => $accountant_name,
        );

        if(!$endDate)
        {
            $endDate = $startDate;
            $requestDetails['enddate'] = $startDate;
        }

        $accountant_timestamp = AccountantTimeStamp::whereDate('activeTime','>=',$startDate)
        ->whereDate('activeTime','<=',$endDate)->where('user_id',$user->id)->get();

        if($user->role == 777)
        {
            if(!isset($start)){
                $start =[$startDate,"00:00"];
            }
            if(!isset($end))
            {
                $end = [$endDate,"23:59"];
            }

            return $this->juniorAccountantSort($start, $end, $segment,$accountant_timestamp,$user,$show_category,$data);
        }

        //*collections to store the data
        $allTransactions = collect();
        $giftCardTransactions = collect();

        $utilityTransactions = collect();
        $payBridgeTransactions = collect();

        //* adding data to the collection
        foreach ($accountant_timestamp as $at) {
            //*all Transactions
            $allTranx = Transaction::orderBy('updated_at','desc');
            $allTranx = $this->sortingByAccountantTimestamp($allTranx, $at->activeTime, $at->inactiveTime);
            $allTransactions = $allTransactions->concat($allTranx);

            //*GiftCard Transactions
            $giftTranx = Transaction::where('status', 'success')->whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->orderBy('updated_at','desc');

            $giftTranx = $this->sortingByAccountantTimestamp($giftTranx, $at->activeTime, $at->inactiveTime);
            $giftCardTransactions = $giftCardTransactions->concat($giftTranx);

            //*Utility Transactions
            $utilityTranx = UtilityTransaction::orderBy('updated_at','desc');
            $utilityTranx = $this->sortingByAccountantTimestamp($utilityTranx, $at->activeTime, $at->inactiveTime);
            $utilityTransactions = $utilityTransactions->concat($utilityTranx);

            //*PayBridge Transactions
            $payBridgeTranx = NairaTransaction::orderBy('updated_at','desc');
            $payBridgeTranx = $this->sortingByAccountantTimestamp($payBridgeTranx, $at->activeTime, $at->inactiveTime);
            $payBridgeTransactions = $payBridgeTransactions->concat($payBridgeTranx);
        }

        $gcBuyTranx = $giftCardTransactions->where('type','buy');
        $gcSellTranx = $giftCardTransactions->where('type','sell');

        if($show_category == "all")
        {
            $BTCtotalTranx = $allTransactions->where('status','success')->where('card','bitcoin');
            $USDTranx = $allTransactions->where('status', 'success')->where('card_id',143);

            return $this->CryptoGiftCardTransactions($allTransactions,$BTCtotalTranx,$gcBuyTranx
            ,$gcSellTranx,$USDTranx,$data);
        }

        if($show_category == "utilities")
        {
            return $this->UtilitiesTransactions($utilityTransactions, $data);
        }

        if($show_category == "paybridge")
        {
            $pbDepositTranx = $payBridgeTransactions->where('transaction_type_id',1);
            return $this->PayBridgeDeposit($pbDepositTranx, $data);
        }

        if($show_category == "paybridgewithdrawal")
        {
            $pbWithdrawalTranx = $payBridgeTransactions->where('transaction_type_id',3);
            return $this->PayBridgeWithdrawal($pbWithdrawalTranx, $data);
        }

        if($show_category == "paybridgeothers")
        {
            $pbOtherTranx = $payBridgeTransactions->where('transaction_type_id','!=',1)->where('transaction_type_id','!=',3);
            return $this->PayBridgeOthers($pbOtherTranx, $data);
        }
    }
    public function sortByDate($requestDetails,$startDate,$endDate,$accountant,$show_data,$show_category,$day,$month,$show_summary,$accountant_name)
    {
        if($startDate != null)
        {
            $start = str_replace("T"," ",$startDate);
            $startDate = $start.":00";
            $requestDetails['startdate'] = $start.":00";
        }

        if($endDate != null)
        {
            $end = str_replace("T"," ",$endDate);
            $endDate = $end.":59";
            $requestDetails['enddate'] = $end.":59";
        }

        if($requestDetails['startdate'] != null)
        {
            $segment = Carbon::parse($startDate)->format('d F Y-h:ia');
            if($requestDetails['enddate'] != null){
                $segment .= "  To  ".Carbon::parse($endDate)->format('d F Y-h:ia');
            }
        }

        if($endDate == null)
        {
            $end = explode(" ",$startDate);
            $endDate = $end[0]." 23:59:59";
            $requestDetails['enddate'] = $end[0]." 23:59:59";
        }

        //*Export Data

        $data = array(
            'segment'=>$segment,
            'accountant'=>$accountant,
            'show_data' =>$show_data,
            'show_category' => $show_category,
            'day' =>$day,
            'month' => $month,
            'show_summary' =>$show_summary,
            'accountant_name' => $accountant_name,
        );

        if($show_category == "all")
        {
            //* All Transactions
            $all_tnx = Transaction::query();
            $all_tnx = $this->sortingByFullDate($all_tnx, $startDate, $endDate);

            //* Bitcoin total transactions
            $bitcoin_total_tnx = $all_tnx->where('status', 'success')->where('card','bitcoin');

            //* USDT total transactions
            $USDTranx = $all_tnx->where('status', 'success')->where('card_id',143);

            //* GiftCards Total Buy
            $giftcards_totaltnx_buy = Transaction::whereNotNull('id')->where('status', 'success')->whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->where('type', 'buy');
            $giftcards_totaltnx_buy = $this->sortingByFullDate($giftcards_totaltnx_buy, $startDate, $endDate);

            //*GiftCards Total sell
            $giftcards_totaltnx_sell = Transaction::whereNotNull('id')->where('status', 'success')->whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->where('type', 'sell');
            $giftcards_totaltnx_sell = $this->sortingByFullDate($giftcards_totaltnx_sell, $startDate, $endDate);

            return $this->CryptoGiftCardTransactions($all_tnx,$bitcoin_total_tnx,$giftcards_totaltnx_buy
            ,$giftcards_totaltnx_sell,$USDTranx,$data);
        }

        if($show_category == "utilities")
        {
            //* Utility transaction
            $util_tnx = UtilityTransaction::whereNotNull('id')->orderBy('updated_at', 'desc');
            $util_tnx = $this->sortingByFullDate($util_tnx, $startDate, $endDate);
            return $this->UtilitiesTransactions($util_tnx, $data);
        }

        if($show_category == "paybridge")
        {
            //* PayBridge Deposit
            $pbDeposit = NairaTransaction::latest()->orderBy('updated_at','desc')->where('transaction_type_id',1);
            $pbDeposit = $this->sortingByFullDate($pbDeposit, $startDate, $endDate);
            return $this->PayBridgeDeposit($pbDeposit,$data);
        }

        if($show_category == "paybridgewithdrawal"){

            //*payBridge Withdrawal
            $pbWithdrawal = NairaTransaction::latest()->orderBy('updated_at','desc')->where('transaction_type_id',3);
            $pbWithdrawal = $this->sortingByFullDate($pbWithdrawal, $startDate, $endDate);
            return $this->PayBridgeWithdrawal($pbWithdrawal,$data);
        }

        if($show_category == "paybridgeothers"){

            //**Other PayBridge Transaction
            $pbOthers = NairaTransaction::latest()->orderBy('updated_at','desc')->where('transaction_type_id','!=',1)->where('transaction_type_id','!=',3);
            $pbOthers = $this->sortingByFullDate($pbOthers, $startDate, $endDate);
            return $this->PayBridgeOthers($pbOthers,$data);
        }
    }

    public function sortingStartAndEnd($value ,$start,$end)
    {
        $value = $value->where('updated_at', '>=', $start." 00:00:00");
        if($end)
        {
            $value = $value->where('updated_at', '<=', $end." 23:59:59");
        }

        return $value->get();
    }

    public function sortingByFullDate($value ,$start,$end)
    {
        $value = $value->where('updated_at', '>=', $start);
        if($end)
        {
            $value = $value->where('updated_at', '<=', $end);
        }

        return $value->get();
    }

    public function sortingByAccountantTimestamp($value, $activeTime, $inactiveTime)
    {
        if($inactiveTime == null)
        {
            $inactiveTime = now();
        }
        $value = $value->where('updated_at','>=',$activeTime)->where('updated_at', '<=', $inactiveTime)->get();

        return $value;
    }

}
