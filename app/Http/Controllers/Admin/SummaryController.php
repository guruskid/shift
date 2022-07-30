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


    public function summaryhomepage($month=null, $day = null)
    {
        if($day)
        {
            $date = date('Y').'-'.$month.'-'.$day;
            $dates=date_create($date);
            $segment = date_format($dates, "M d");
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

    public function cryptoAndGiftCardTransactions($all_tnx,$bitcoin_total_tnx,$giftcards_totaltnx_buy
    ,$giftcards_totaltnx_sell,$crypto_totaltnx_buy,$crypto_totaltnx_sell,$data)
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
        $all_tnx = $all_tnx->unique('id');
        $all_tnx_count = $all_tnx->where('status', 'success')->count();
        $all_tnx = $all_tnx->paginate(100);

        //*Bitcoin Transaction
        $bitcoin_total_tnx_buy = $bitcoin_total_tnx->where('type', 'buy')->sum('quantity');
        $bitcoin_total_tnx_sell = $bitcoin_total_tnx->where('type', 'sell')->sum('quantity');

        //*GiftCard Transaction BUY
        $giftcards_totaltnx_buy = $giftcards_totaltnx_buy->unique('id');
        $giftcards_totaltnx_buy_amount = $giftcards_totaltnx_buy->sum('amount');
        $giftcards_totaltnx_buy_amount_naira = $giftcards_totaltnx_buy->sum('amount_paid');
        $giftcards_totaltnx_buy = $giftcards_totaltnx_buy->count();

        //*GiftCard Transaction SELL
        $giftcards_totaltnx_sell = $giftcards_totaltnx_sell->unique('id');
        $giftcards_totaltnx_sell_amount = $giftcards_totaltnx_sell->sum('amount');
        $giftcards_totaltnx_sell_amount_naira = $giftcards_totaltnx_sell->sum('amount_paid');
        $giftcards_totaltnx_sell = $giftcards_totaltnx_sell->count();

        //*Crypto Transaction BUY
        $crypto_totaltnx_buy = $crypto_totaltnx_buy->unique('id');
        $crypto_totaltnx_buy_amount = $crypto_totaltnx_buy->sum('amount');
        $crypto_totaltnx_buy_amount_naira = $crypto_totaltnx_buy->sum('amount_paid');
        $crypto_totaltnx_buy = $crypto_totaltnx_buy->count();

        //*Crypto Transaction SELL
        $crypto_totaltnx_sell = $crypto_totaltnx_sell->unique('id');
        $crypto_totaltnx_sell_amount = $crypto_totaltnx_sell->sum('amount');
        $crypto_totaltnx_sell_amount_naira = $crypto_totaltnx_sell->sum('amount_paid');
        $crypto_totaltnx_sell = $crypto_totaltnx_sell->count();

        return view('admin.summary.JuniorAccountant.transaction',compact([
            'segment','accountant','all_tnx','all_tnx_count','show_data','show_category','day','month','show_summary',
            'giftcards_totaltnx_buy','giftcards_totaltnx_sell','crypto_totaltnx_buy','crypto_totaltnx_sell',
            'giftcards_totaltnx_buy_amount','giftcards_totaltnx_sell_amount','giftcards_totaltnx_sell_amount_naira','giftcards_totaltnx_buy_amount_naira','crypto_totaltnx_buy_amount','crypto_totaltnx_sell_amount',
            'bitcoin_total_tnx_buy','bitcoin_total_tnx_sell','crypto_totaltnx_sell_amount_naira','crypto_totaltnx_buy_amount_naira'

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

        $util_tnx = $util_tnx->unique('id');
        $util_total_tnx = $util_tnx->where('status','success')->count();
        $util_tnx_amount = $util_tnx->where('status','success')->sum('amount');
        $util_tnx_fee = $util_tnx->where('status','success')->sum('convenience_fee');
        $util_amount_paid = $util_tnx->where('status','success')->sum('total');
        $util_tnx = $util_tnx->paginate(100);
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
        if($total != 0){
            $average = $avg_response/$total;
            return (CarbonInterval::seconds($average)->cascade()->forHumans());
        }
        else{
            return 0;
        }

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

        $nw_deposit_tnx = $nw_deposit_tnx->unique('id');
        $nw_deposit_tnx_total = $nw_deposit_tnx->where('status','success')->count();
        $nw_deposit_amount_paid = $nw_deposit_tnx->where('status','success')->sum('amount_paid');
        $nw_deposit_tnx_charges = $nw_deposit_tnx->where('status','success')->sum('charge');
        $nw_deposit_total_amount = $nw_deposit_tnx->where('status','success')->sum('amount');

        $nw_deposit_pending_total = $nw_deposit_tnx->where('status','pending')->count();
        $nw_deposit_pending_amount = $nw_deposit_tnx->where('status','pending')->sum('amount');
        $nw_deposit_tnx = $nw_deposit_tnx->paginate(100);
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

        $nw_withdrawal_tnx = $nw_withdrawal_tnx->unique('id');
        $nw_withdrawal_tnx_total = $nw_withdrawal_tnx->where('status','success')->count();
        $nw_withdrawal_amount_paid = $nw_withdrawal_tnx->where('status','success')->sum('amount_paid');
        $nw_withdrawal_tnx_charges = $nw_withdrawal_tnx->where('status','success')->sum('charge');
        $nw_withdrawal_total_amount = $nw_withdrawal_tnx->where('status','success')->sum('amount');

        $nw_withdrawal_pending_total = $nw_withdrawal_tnx->where('status','pending')->count();
        $nw_withdrawal_pending_amount = $nw_withdrawal_tnx->where('status','pending')->sum('amount');
        $nw_withdrawal_tnx = $nw_withdrawal_tnx->paginate(100);

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

        $nw_other_tnx = $nw_other_tnx->unique('id');
        $nw_other_tnx_total = $nw_other_tnx->where('status','success')->count();
        $nw_other_amount_paid = $nw_other_tnx->where('status','success')->sum('amount_paid');
        $nw_other_tnx_charges = $nw_other_tnx->where('status','success')->sum('charge');
        $nw_other_total_amount = $nw_other_tnx->where('status','success')->sum('amount');

        $nw_other_pending_total = $nw_other_tnx->where('status','pending')->count();
        $nw_other_pending_amount = $nw_other_tnx->where('status','pending')->sum('amount');
        $nw_other_tnx = $nw_other_tnx->paginate(100);

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
        $request->session()->forget('RequestDetails');
        $show_summary = true;
        $show_data = true;
        $show_category = $category ?: null;
        $date = date('Y').'-'.$month.'-'.$day;
        $current_day_value = Carbon::parse($date);
        $segment = $current_day_value->format("M d");

        $user = Auth::user();
        $accountant = User::whereIn('role', [777,775])->get();

        $accountant_timestamp = AccountantTimeStamp::whereDate('created_at','>=',$current_day_value)
        ->whereDate('created_at','<=',$current_day_value)
        ->where('user_id',$user->id)
        ->latest('id')->first();

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
            $all_tnx = $all_tnx->latest()->get();

            //**Bitcoin Transactions */
            $bitcoin_total_tnx = Transaction::whereNotNull('id');
            $bitcoin_total_tnx = $this->category_listing($user,$accountant_timestamp,$bitcoin_total_tnx,$current_day_value);
            $bitcoin_total_tnx = $bitcoin_total_tnx->where('status', 'success')->where('card_id',102)->get();

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

             //**Crypto Buy Transactions */
             $crypto_totaltnx_buy = Transaction::whereNotNull('id')->whereHas('asset', function ($query) {
                $query->where('is_crypto', 1);
            })->where('type', 'buy');
            $crypto_totaltnx_buy = $this->category_listing($user,$accountant_timestamp,$crypto_totaltnx_buy,$current_day_value);
            $crypto_totaltnx_buy = $crypto_totaltnx_buy->where('status', 'success')->get();

            //**Crypto Sell Transactions */
            $crypto_totaltnx_sell = Transaction::whereNotNull('id')->whereHas('asset', function ($query) {
                $query->where('is_crypto', 1);
            })->where('type', 'sell');
            $crypto_totaltnx_sell = $this->category_listing($user,$accountant_timestamp,$crypto_totaltnx_sell,$current_day_value);
            $crypto_totaltnx_sell = $crypto_totaltnx_sell->where('status', 'success')->get();

            return $this->cryptoAndGiftCardTransactions($all_tnx,$bitcoin_total_tnx,$giftcards_totaltnx_buy
            ,$giftcards_totaltnx_sell,$crypto_totaltnx_buy,$crypto_totaltnx_sell,$data);
        }

        if($category == "utilities"){
            //**Utilities Transaction */
            $util_tnx = UtilityTransaction::whereNotNull('id')->orderBy('created_at', 'desc');
            $util_tnx = $this->category_listing($user,$accountant_timestamp,$util_tnx,$current_day_value);

            return $this->UtilitiesTransactions($util_tnx->get(),$data);
        }

        if($category == "paybridge"){

            //**PayBridge Deposit Transaction */
            $nw_deposit_tnx = NairaTransaction::latest()->orderBy('created_at','desc')->where('transaction_type_id',1);
            $nw_deposit_tnx = $this->category_listing($user,$accountant_timestamp,$nw_deposit_tnx,$current_day_value);
            $nw_deposit_tnx = $nw_deposit_tnx->get();
            return $this->PayBridgeDeposit($nw_deposit_tnx,$data);
        }
        if($category == "paybridgewithdrawal"){

            //**PayBridge Withdrawal Transaction */
            $nw_withdrawal_tnx = NairaTransaction::latest()->orderBy('created_at','desc')->where('transaction_type_id',3);
            $nw_withdrawal_tnx = $this->category_listing($user,$accountant_timestamp,$nw_withdrawal_tnx,$current_day_value);
            $nw_withdrawal_tnx = $nw_withdrawal_tnx->get();
            return $this->PayBridgeWithdrawal($nw_withdrawal_tnx,$data);

        }

        if($category == "paybridgeothers"){

            //**Other PayBridge Transaction */
            $nw_other_tnx = NairaTransaction::latest()->orderBy('created_at','desc')->where('transaction_type_id','!=',1)->where('transaction_type_id','!=',3);
            $nw_other_tnx = $this->category_listing($user,$accountant_timestamp,$nw_other_tnx,$current_day_value);
            $nw_other_tnx = $nw_other_tnx->get();
            return $this->PayBridgeOthers($nw_other_tnx,$data);
        }

    }



    public function category_listing($user,$accountant_timestamp,$value,$current_day_value)
    {
        if (($user->role == 777 OR $user->role == 775 )&& !empty($accountant_timestamp)) {
            $value = $value
            ->where('updated_at', '>=', $accountant_timestamp->created_at)
            ->where('updated_at', '<=', $accountant_timestamp->updated_at);
        }
        else{
            $value = $value
            ->whereDate('updated_at', '>=', $current_day_value)
            ->whereDate('updated_at', '<=', $current_day_value);
        }
        return $value;
    }

    public function sorting(Request $request)
    {
        $request->session()->put('RequestDetails', $request->all());
        $requestDetails = session('RequestDetails');
        if($requestDetails['startdate'] == null AND $requestDetails['enddate'] == null AND $requestDetails['Accountant'] == "null"){
            return back()->with(['error' => 'Sorting Field is Empty ']);
        }
        $day = $requestDetails['day'];
        $month = $requestDetails['month'];
        $show_category = $requestDetails['category'];
        $show_data = true;
        $show_summary = (Auth::user()->role != 777 || Auth::user()->role != 775 || $requestDetails['Accountant'] == 'null') ? true : false;
        $start_date =  explode('T',$requestDetails['startdate']);
        $end_date = explode('T',$requestDetails['enddate']);

        $start = str_replace('T',' ',$requestDetails['startdate']);
        $end = str_replace('T',' ',$requestDetails['enddate']);

        $accountant_name = null;
        if(!isset($requestDetails['Accountant']) || $requestDetails['Accountant'] == 'null' )
        {
            $user = Auth::user();
        }
        else{
            $user = User::find($requestDetails['Accountant']);
            $accountant_name = $user->first_name ?: $user->email;
        }
        $accountant = User::whereIn('role', [777,775])->get();

        if($requestDetails['Accountant'] == 'null')
        {
            return $this->sortbydate($requestDetails,$day,$month,$show_category,$show_data,
            $show_summary,$start_date,$start,$end_date,$end,$user,$accountant,$accountant_name);
        }
        else{
            return $this->sortbyaccountant($requestDetails,$day,$month,$show_category,$show_data,
            $show_summary,$start_date,$start,$end_date,$end,$user,$accountant,$accountant_name);
        }
    }

    public function sortByAccountant($requestDetails,$day,$month,$show_category,$show_data,$show_summary,$start_date,$start,$end_date,$end,$user,$accountant,$accountant_name)
    {
        if($start_date[0] == "")
        {
            $start_date[0] = date('Y').'-'.$month.'-'.$day;
        }
        if($requestDetails['Accountant'] != 'null'){
            $segment = Carbon::parse(date('Y')."-$month-$day")->format('d M');
        }
        if($requestDetails['Accountant'] != 'null'){
            $segment = $accountant_name." ".Carbon::parse($start_date[0])->format('d M');

        }
        if($requestDetails['startdate'] != null AND $requestDetails['Accountant'] != 'null')
        {
            $segment = $accountant_name." ".Carbon::parse($start)->format('d-M-y h:ia');
            if($requestDetails['enddate'] != null){
                $segment .= " to ".Carbon::parse($end)->format('d-M-y h:ia');
            }
        }
        // dd($start);
        if(empty($start))
        {
            $start = $start_date[0]." 00:00";
        }
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

        $accountant_timestamp = AccountantTimeStamp::whereDate('created_at','>=',$start_date[0])->where('user_id',$user->id)->get();

        $all_transaction_Collection = collect([]);
        $giftcard_collection = collect([]);
        $crypto_collection = collect([]);
        $util_collection = collect([]);
        $nw_collection = collect([]);

        if($accountant_timestamp)
        {
            foreach ($accountant_timestamp  as $at) {
                $all_tranx = Transaction::whereNotNull('id')
                ->orderBy('created_at', 'desc')->where('updated_at', '>=', $at->activeTime);
                if($at->inactiveTime != null){
                    $all_tranx = $all_tranx->where('updated_at', '<=', $at->inactiveTime);
                }
                $all_tranx = $all_tranx->get();

                $all_transaction_Collection = $all_transaction_Collection->concat($all_tranx);

                $gift_tranx = Transaction::whereNotNull('id')
                ->orderBy('created_at', 'desc')->where('updated_at', '>=', $at->activeTime)->where('status', 'success')
                ->whereHas('asset', function ($query) {
                    $query->where('is_crypto', 0);
                });
                if($at->inactiveTime != null){
                    $gift_tranx = $gift_tranx->where('updated_at', '<=', $at->inactiveTime);
                }
                $gift_tranx = $gift_tranx->get();
                $giftcard_collection = $giftcard_collection->concat($gift_tranx);

                $crypto_tranx = Transaction::whereNotNull('id')->orderBy('created_at', 'desc')->where('updated_at', '>=', $at->activeTime)
                ->where('status', 'success')
                ->whereHas('asset', function ($query) {
                    $query->where('is_crypto', 1);
                });
                if($at->inactiveTime != null){
                    $crypto_tranx = $crypto_tranx->where('updated_at', '<=', $at->inactiveTime);
                }
                $crypto_tranx = $crypto_tranx->get();
                $crypto_collection = $crypto_collection->concat($crypto_tranx);

                $util_tranx = UtilityTransaction::whereNotNull('id')->orderBy('created_at', 'desc')->where('updated_at', '>=', $at->activeTime);
                if($at->inactiveTime != null){
                    $util_tranx = $util_tranx->where('updated_at', '<=', $at->inactiveTime);
                }
                $util_tranx = $util_tranx->get();
                $util_collection = $util_collection->concat($util_tranx);

                $nw_tranx = NairaTransaction::latest()->orderBy('created_at','desc')->where('updated_at', '>=', $at->activeTime);
                if($at->inactiveTime != null){
                    $nw_tranx = $nw_tranx->where('updated_at', '<=', $at->inactiveTime);
                }
                $nw_tranx = $nw_tranx->get();
                $nw_collection = $nw_collection->concat($nw_tranx);

            }
            $all_tnx = $all_transaction_Collection;
            $all_tnx = $all_tnx->where('updated_at', '>=', Carbon::parse($start));
            $all_tnx = $this->SortingByAccountantAndDate($requestDetails['enddate'],$all_tnx,$start_date[0],$end);

            $gift_tranx = $giftcard_collection;
            $gift_tranx = $gift_tranx->where('updated_at', '>=', Carbon::parse($start));
            $gift_tranx = $this->SortingByAccountantAndDate($requestDetails['enddate'],$gift_tranx,$start_date[0],$end);
            $giftcards_totaltnx_buy = $gift_tranx->where('type', 'buy');
            $giftcards_totaltnx_sell = $gift_tranx->where('type', 'sell');


            $crypto_tranx = $crypto_collection;
            $crypto_tranx = $crypto_tranx->where('updated_at', '>=', Carbon::parse($start));
            $crypto_tranx = $this->SortingByAccountantAndDate($requestDetails['enddate'],$crypto_tranx,$start_date[0],$end);
            $crypto_totaltnx_buy = $crypto_tranx->where('type', 'buy');
            $crypto_totaltnx_sell = $crypto_tranx->where('type', 'sell');

            $util_tranx = $util_collection;
            $util_tranx = $util_tranx->where('updated_at', '>=', Carbon::parse($start));
            $util_tranx = $this->SortingByAccountantAndDate($requestDetails['enddate'],$util_tranx,$start_date[0],$end);

            $nw_tranx = $nw_collection;
            $nw_tranx = $nw_tranx->where('updated_at', '>=', Carbon::parse($start));
            $nw_tranx = $this->SortingByAccountantAndDate($requestDetails['enddate'],$nw_tranx,$start_date[0],$end);

        if($show_category == "all")
        {

            $bitcoin_total_tnx = $all_tnx ->where('status', 'success')->where('card_id',102);
            return $this->cryptoAndGiftCardTransactions($all_tnx,$bitcoin_total_tnx,$giftcards_totaltnx_buy
            ,$giftcards_totaltnx_sell,$crypto_totaltnx_buy,$crypto_totaltnx_sell,$data);

        }

        if($show_category == "utilities")
        {
            $util_tnx = $util_tranx;
            return $this->UtilitiesTransactions($util_tnx,$data);
        }
        if($show_category == "paybridge"){
            $nw_deposit_tnx = $nw_tranx->where('transaction_type_id',1);
            return $this->PayBridgeDeposit($nw_deposit_tnx,$data);
        }

        if($show_category == "paybridgewithdrawal")
        {
            $nw_withdrawal_tnx = $nw_tranx->where('transaction_type_id',3);
            return $this->PayBridgeWithdrawal($nw_withdrawal_tnx,$data);
        }
        if($show_category == "paybridgeothers")
        {
            $nw_other_tnx = $nw_tranx->where('transaction_type_id','!=',1)->where('transaction_type_id','!=',3);
            return $this->PayBridgeOthers($nw_other_tnx,$data);
        }
        }


    }
    public function SortingByAccountantAndDate($enddate,$value,$start_date,$end){
        if($enddate != null){
            $value = $value->where('updated_at', '<=', Carbon::parse($end));
        }
        else{
            $value = $value->where('updated_at', '<=', Carbon::parse($start_date." 23:59:59"));
        }
        return $value;
    }
    public function sortByDate($requestDetails,$day,$month,$show_category,$show_data,$show_summary,$start_date,$start,$end_date,$end,$user,$accountant,$accountant_name)
    {
        if($requestDetails['startdate'] != null)
        {
            $segment = Carbon::parse($start)->format('d-M-y h:ia');
            if($requestDetails['enddate'] != null){
                $segment .= " to ".Carbon::parse($end)->format('d-M-y h:ia');
            }
        }
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
            //**All Transactions */
            $all_tnx = Transaction::whereNotNull('id')->where('updated_at', '>=', $start);
            $all_tnx = $this->SortingStartDateAndEndDate($requestDetails['enddate'],$all_tnx,$start_date[0],$end);

            //**Bitcoin Buy and Sell Transactions */
            $bitcoin_total_tnx = $all_tnx ->where('status', 'success')->where('card_id',102);

            //**GiftCard Buy Transactions */
            $giftcards_totaltnx_buy = Transaction::whereNotNull('id')->where('status', 'success')->whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->where('type', 'buy')->where('updated_at', '>=', $start);
            $giftcards_totaltnx_buy = $this->SortingStartDateAndEndDate($requestDetails['enddate'],$giftcards_totaltnx_buy,$start_date[0],$end);

            //**GiftCard Sell Transactions */
            $giftcards_totaltnx_sell = Transaction::whereNotNull('id')->where('status', 'success')->whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->where('type', 'sell')->where('updated_at', '>=', $start);
            $giftcards_totaltnx_sell = $this->SortingStartDateAndEndDate($requestDetails['enddate'],$giftcards_totaltnx_sell,$start_date[0],$end);

            //**All Crypto Buy Transactions */
            $crypto_totaltnx_buy = Transaction::whereNotNull('id') ->where('status', 'success')->whereHas('asset', function ($query) {
                $query->where('is_crypto', 1);
            })->where('type', 'buy')->where('updated_at', '>=', $start);
            $crypto_totaltnx_buy = $this->SortingStartDateAndEndDate($requestDetails['enddate'],$crypto_totaltnx_buy,$start_date[0],$end);

            //**All Crypto Sell Transactions */
            $crypto_totaltnx_sell = Transaction::whereNotNull('id')->where('status', 'success')->whereHas('asset', function ($query) {
                $query->where('is_crypto', 1);
            })->where('type', 'sell')->where('updated_at', '>=', $start);
            $crypto_totaltnx_sell = $this->SortingStartDateAndEndDate($requestDetails['enddate'],$crypto_totaltnx_sell,$start_date[0],$end);

            return $this->cryptoAndGiftCardTransactions($all_tnx,$bitcoin_total_tnx,$giftcards_totaltnx_buy
            ,$giftcards_totaltnx_sell,$crypto_totaltnx_buy,$crypto_totaltnx_sell,$data);
        }

        if($show_category == "utilities")
        {
            //**Utility Transaction */
            $util_tranx = UtilityTransaction::whereNotNull('id')->where('updated_at', '>=', $start);
            $util_tranx = $this->SortingStartDateAndEndDate($requestDetails['enddate'],$util_tranx,$start_date[0],$end);

            $util_tnx = $util_tranx;
            return $this->UtilitiesTransactions($util_tnx,$data);
        }

        if($show_category == "paybridge" OR
        $show_category == "paybridgewithdrawal" OR $show_category == "paybridgeothers")
        {
            //**Paybridge Transaction */
            $nw_tranx = NairaTransaction::whereNotNull('id')->where('updated_at', '>=', $start);
            $nw_tranx = $this->SortingStartDateAndEndDate($requestDetails['enddate'],$nw_tranx,$start_date[0],$end);

            if($show_category == "paybridge"){
                $nw_deposit_tnx = $nw_tranx->where('transaction_type_id',1);
                return $this->PayBridgeDeposit($nw_deposit_tnx,$data);

            }

            if($show_category == "paybridgewithdrawal")
            {
                $nw_withdrawal_tnx = $nw_tranx->where('transaction_type_id',3);
                return $this->PayBridgeWithdrawal($nw_withdrawal_tnx,$data);
            }
            if($show_category == "paybridgeothers")
            {
                $nw_other_tnx = $nw_tranx->where('transaction_type_id','!=',1)->where('transaction_type_id','!=',3);
                return $this->PayBridgeOthers($nw_other_tnx,$data);
            }
        }

    }

    public function SortingStartDateAndEndDate($enddate, $value, $start_date, $end){
        if($enddate != null){
            $value = $value->where('updated_at', '<=', $end)->get();
        }
        else{
            $value = $value->whereDate('updated_at', '<=', $start_date)->get();
        }
        return $value;
    }

    public function sellTnx_summary($created_at,$updated_at,$card_id)
    {
        //? sell tnx crypto
        $crypto_tnx = Transaction::latest('id')
            ->where('created_at', '>=', $created_at)
            ->where('updated_at', '<=', $updated_at)
            ->where('card_id', $card_id)->where('type', 'sell')->get();
        return $crypto_tnx;
    }

    public function buyTnx_summary($created_at,$updated_at,$card_id)
    {
        //? buy tnx crypto
        $crypto_tnx = Transaction::latest('id')
            ->where('created_at', '>=', $created_at)
            ->where('updated_at', '<=', $updated_at)
            ->where('card_id', $card_id)->where('type', 'buy')->get();
        return $crypto_tnx;
    }

}
