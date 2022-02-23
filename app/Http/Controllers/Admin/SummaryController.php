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
        try {
            $sell_average = $sell_usd / $sell_btc;
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
            'card_id'
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
            'card_id'
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

    public function summary_tnx_category($month, $day, $category, Request $request)
    {
        $show_summary = true;
        $show_data = true;
        $show_category = $category ?: null;
        $date = date('Y').'-'.$month.'-'.$day;
        $dates=date_create($date);
        $segment = date_format($dates, "M d");
        $current_day_value = date_format($dates,"Y-m-d") ;
        

        $user = Auth::user();
        $accountant = User::whereIn('role', [777])->where('id','!=',$user->id)->get();
        //?Accounatnt Timestamps
            $accountant_timestamp = AccountantTimeStamp::whereDate('created_at','>=',$current_day_value)
            ->whereDate('created_at','<=',$current_day_value)
            ->where('user_id',$user->id)
            ->latest('id')->first(); 

            if($category == "all"){
                    //?All tansactions 
                $all_tnx = Transaction::whereNotNull('id')
                ->orderBy('created_at', 'desc');
                if ($user->role == 777 && !empty($accountant_timestamp)) {
                    $all_tnx = $all_tnx
                    ->where('updated_at', '>=', $accountant_timestamp->created_at)
                    ->where('updated_at', '<=', $accountant_timestamp->updated_at);
                }
                else{
                    $all_tnx = $all_tnx
                    ->whereDate('updated_at', '>=', $current_day_value)
                    ->whereDate('updated_at', '<=', $current_day_value);
                }
                
                $all_tnx = $all_tnx->latest()->get();
                
                $all_tnx_count = $all_tnx->where('status', 'success')->count();
                $all_tnx = $all_tnx->paginate(100);

                //? bitcoin transactions
                $bitcoin_total_tnx = Transaction::whereNotNull('id');
                if ($user->role == 777 && !empty($accountant_timestamp)) {
                    $bitcoin_total_tnx = $bitcoin_total_tnx
                    ->where('updated_at', '>=', $accountant_timestamp->created_at)
                    ->where('updated_at', '<=', $accountant_timestamp->updated_at);
                }
                else{
                    $bitcoin_total_tnx = $bitcoin_total_tnx
                    ->whereDate('updated_at', '>=', $current_day_value)
                    ->whereDate('updated_at', '<=', $current_day_value);
                }
                $bitcoin_total_tnx = $bitcoin_total_tnx
                ->where('status', 'success')
                ->where('card_id',102)->get();

                $bitcoin_total_tnx_buy = $bitcoin_total_tnx->where('type', 'buy')->sum('quantity');
                $bitcoin_total_tnx_sell = $bitcoin_total_tnx->where('type', 'sell')->sum('quantity');

                //*Gitfcard transactions 
                $giftcards_totaltnx_buy = Transaction::whereNotNull('id')
                ->whereHas('asset', function ($query) {
                    $query->where('is_crypto', 0);
                })->where('type', 'buy');
                if ($user->role == 777 && !empty($accountant_timestamp)) {
                    $giftcards_totaltnx_buy = $giftcards_totaltnx_buy
                    ->where('updated_at', '>=', $accountant_timestamp->created_at)
                    ->where('updated_at', '<=', $accountant_timestamp->updated_at);
                }
                else{
                    $giftcards_totaltnx_buy = $giftcards_totaltnx_buy
                    ->whereDate('updated_at', '>=', $current_day_value)
                    ->whereDate('updated_at', '<=', $current_day_value);
                }
                $giftcards_totaltnx_buy = $giftcards_totaltnx_buy
                ->where('status', 'success')
                ->get();
                
                $giftcards_totaltnx_buy_amount = $giftcards_totaltnx_buy->sum('amount');
                $giftcards_totaltnx_buy = $giftcards_totaltnx_buy->count();

                $giftcards_totaltnx_sell = Transaction::whereNotNull('id')
                ->whereHas('asset', function ($query) {
                    $query->where('is_crypto', 0);
                })->where('type', 'sell');
                if ($user->role == 777 && !empty($accountant_timestamp)) {
                    $giftcards_totaltnx_sell = $giftcards_totaltnx_sell
                    ->where('updated_at', '>=', $accountant_timestamp->created_at)
                    ->where('updated_at', '<=', $accountant_timestamp->updated_at);
                }
                else{
                    $giftcards_totaltnx_sell = $giftcards_totaltnx_sell
                    ->whereDate('updated_at', '>=', $current_day_value)
                    ->whereDate('updated_at', '<=', $current_day_value);
                }
                $giftcards_totaltnx_sell = $giftcards_totaltnx_sell
                ->where('status', 'success')
                ->get();

                $giftcards_totaltnx_sell_amount = $giftcards_totaltnx_sell->sum('amount');
                $giftcards_totaltnx_sell = $giftcards_totaltnx_sell->count();

                $crypto_totaltnx_buy = Transaction::whereNotNull('id')
                ->whereHas('asset', function ($query) {
                    $query->where('is_crypto', 1);
                })->where('type', 'buy');
                if ($user->role == 777 && !empty($accountant_timestamp)) {
                    $crypto_totaltnx_buy = $crypto_totaltnx_buy
                    ->where('updated_at', '>=', $accountant_timestamp->created_at)
                    ->where('updated_at', '<=', $accountant_timestamp->updated_at);
                }
                else{
                    $crypto_totaltnx_buy = $crypto_totaltnx_buy
                    ->whereDate('updated_at', '>=', $current_day_value)
                    ->whereDate('updated_at', '<=', $current_day_value);
                }
                $crypto_totaltnx_buy = $crypto_totaltnx_buy
                ->where('status', 'success')
                ->get();

                $crypto_totaltnx_buy_amount = $crypto_totaltnx_buy->sum('amount');
                $crypto_totaltnx_buy = $crypto_totaltnx_buy->count();

                $crypto_totaltnx_sell = Transaction::whereNotNull('id')
                ->whereHas('asset', function ($query) {
                    $query->where('is_crypto', 1);
                })->where('type', 'sell');
                if ($user->role == 777 && !empty($accountant_timestamp)) {
                    $crypto_totaltnx_sell = $crypto_totaltnx_sell
                    ->where('updated_at', '>=', $accountant_timestamp->created_at)
                    ->where('updated_at', '<=', $accountant_timestamp->updated_at);
                }
                else{
                    $crypto_totaltnx_sell = $crypto_totaltnx_sell
                    ->whereDate('updated_at', '>=', $current_day_value)
                    ->whereDate('updated_at', '<=', $current_day_value);
                }
                $crypto_totaltnx_sell = $crypto_totaltnx_sell
                ->where('status', 'success')
                ->get();

                $crypto_totaltnx_sell_amount = $crypto_totaltnx_sell->sum('amount');
                $crypto_totaltnx_sell = $crypto_totaltnx_sell->count();
                return view('admin.summary.JuniorAccountant.transaction',compact([
                    'segment','accountant','all_tnx','all_tnx_count','show_data','show_category','day','month','show_summary',
                    'giftcards_totaltnx_buy','giftcards_totaltnx_sell','crypto_totaltnx_buy','crypto_totaltnx_sell',
                    'giftcards_totaltnx_buy_amount','giftcards_totaltnx_sell_amount','crypto_totaltnx_buy_amount','crypto_totaltnx_sell_amount',
                    'bitcoin_total_tnx_buy','bitcoin_total_tnx_sell',

                ]));
            }

            if($category == "utilities"){
                $util_tnx = UtilityTransaction::whereNotNull('id')
                ->orderBy('created_at', 'desc');
                if ($user->role == 777) {
                    $util_tnx = $util_tnx
                    ->where('updated_at', '>=', $accountant_timestamp->created_at)
                    ->where('updated_at', '<=', $accountant_timestamp->updated_at);
                }
                else{
                    $util_tnx = $util_tnx
                    ->whereDate('updated_at', '>=', $current_day_value)
                    ->whereDate('updated_at', '<=', $current_day_value);
                }
                $util_tnx = $util_tnx->get();
                
                //? utility transactions calculations
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

            if($category == "paybridge"){
                    //?deposit
                    $nw_deposit_tnx = NairaTransaction::latest()
                    ->orderBy('created_at','desc')->where('transaction_type_id',1);
                    if ($user->role == 777) {
                        $nw_deposit_tnx = $nw_deposit_tnx
                        ->where('updated_at', '>=', $accountant_timestamp->created_at)
                        ->where('updated_at', '<=', $accountant_timestamp->updated_at);
                    }
                    else{
                        $nw_deposit_tnx = $nw_deposit_tnx
                        ->whereDate('updated_at', '>=', $current_day_value)
                        ->whereDate('updated_at', '<=', $current_day_value);
                    }
                    $nw_deposit_tnx = $nw_deposit_tnx->get();

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

                    return view('admin.summary.JuniorAccountant.transaction',compact([
                    'segment','accountant','show_data','show_category','day','month','show_summary',
                    'nw_deposit_tnx','nw_deposit_tnx_total','nw_deposit_amount_paid','nw_deposit_tnx_charges','nw_deposit_total_amount',
                    'nw_deposit_pending_total','nw_deposit_pending_amount','deposit_total_pending','deposit_total_pending_amount'
                ]));
            }
            if($category == "paybridgewithdrawal"){
                //?withdrawal
                $nw_withdrawal_tnx = NairaTransaction::latest()
                ->orderBy('created_at','desc')->where('transaction_type_id',3);
                if ($user->role == 777) {
                    $nw_withdrawal_tnx = $nw_withdrawal_tnx
                    ->where('updated_at', '>=', $accountant_timestamp->created_at)
                    ->where('updated_at', '<=', $accountant_timestamp->updated_at);
                }
                else{
                    $nw_withdrawal_tnx = $nw_withdrawal_tnx
                    ->whereDate('updated_at', '>=', $current_day_value)
                    ->whereDate('updated_at', '<=', $current_day_value);
                }
                $nw_withdrawal_tnx = $nw_withdrawal_tnx->get();
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
                return view('admin.summary.JuniorAccountant.transaction',compact([
                    'segment','accountant','show_data','show_category','day','month','show_summary',
                    'nw_withdrawal_tnx','nw_withdrawal_tnx_total','nw_withdrawal_amount_paid','nw_withdrawal_tnx_charges','nw_withdrawal_total_amount',
                    'nw_withdrawal_pending_total','nw_withdrawal_pending_amount','withdrawal_total_pending','withdrawal_total_pending_amount'
                ]));
            }
            if($category == "paybridgeothers"){
                //?others
                $nw_other_tnx = NairaTransaction::latest()
                ->orderBy('created_at','desc')
                ->where('transaction_type_id','!=',1)->where('transaction_type_id','!=',3);
                if ($user->role == 777) {
                    $nw_other_tnx = $nw_other_tnx
                    ->where('updated_at', '>=', $accountant_timestamp->created_at)
                    ->where('updated_at', '<=', $accountant_timestamp->updated_at);
                }
                else{
                    $nw_other_tnx = $nw_other_tnx
                    ->whereDate('updated_at', '>=', $current_day_value)
                    ->whereDate('updated_at', '<=', $current_day_value);
                }
                $nw_other_tnx = $nw_other_tnx->get();

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
                return view('admin.summary.JuniorAccountant.transaction',compact([
                    'segment','accountant','show_data','show_category','day','month','show_summary',
                    'nw_other_tnx','nw_other_tnx_total','nw_other_amount_paid','nw_other_tnx_charges','nw_other_total_amount',
                    'nw_other_pending_total','nw_other_pending_amount','other_total_pending','other_total_pending_amount'
                ]));
            }

            
        
    }

    public function sorting(Request $request)
    {
        /*
        ? startdate  enddate day month category Accountant entries
        */
        $show_category = $request['category'];
        $show_data = true;
        $show_summary = (Auth::user()->role != 777 || $request['Accountant'] == 'null') ? true : false;
        $start_date =  explode('T',$request['startdate']);
        $end_date = explode('T',$request['enddate']);
        
        $start = str_replace('T',' ',$request['startdate']);
        $end = str_replace('T',' ',$request['enddate']);

        $pagination = (int)$request['entries'] ?: 50; 
        $accountant_name = null;
        if(!isset($request['Accountant']) || $request['Accountant'] == 'null' )
        {
            $user = Auth::user();
        }
        else{
            $user = User::find($request['Accountant']);
            $accountant_name = $user->first_name ?: $user->email;
        }
        $accountant = User::whereIn('role', [777])->where('id','!=',$user->id)->get();
        $accountant_timestamp = AccountantTimeStamp::whereDate('created_at','>=',$start_date[0])
        ->whereDate('created_at','<=',$end_date[0])->where('user_id',$user->id)->get();
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
