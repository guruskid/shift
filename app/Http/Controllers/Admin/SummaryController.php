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
            'cur', 'card_id'
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
            'sell_average', 'cur', 'card_id'
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


    public function summaryhomepage($month=null)
    {
        if($month)
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

    public function summary_tnx($month,$day)
    {
        $paginate = true;
        $show_summary = true;
        $date = date('Y').'-'.$month.'-'.$day;
        $dates=date_create($date);
        $current_day_value = date_format($dates,"Y-m-d") ;

        $user = Auth::user();
        $accountant = User::whereIn('role', [777])->where('id','!=',$user->id)->get();

        if($user->role == 777)
        {
            
            $accountant_timestamp = AccountantTimeStamp::whereDate('created_at','>=',$current_day_value)
            ->whereDate('updated_at','<=',$current_day_value)
            ->where('user_id',$user->id)
            ->latest('id')->first();
            if(!empty($accountant_timestamp) )
            {
                //?All Tnx
                $all_tnx = Transaction::whereNotNull('id')
                ->orderBy('created_at', 'desc')
                ->where('created_at', '>=', $accountant_timestamp->created_at)
                ->where('updated_at', '<=', $accountant_timestamp->updated_at)
                ->latest()->get();

                $all_tnx_count = $all_tnx->where('status', 'success')->count();
                $all_tnx = $all_tnx->paginate(50);

                $bitcoin_total_tnx = Transaction::whereNotNull('id')
                ->where('created_at', '>=', $accountant_timestamp->created_at)
                ->where('updated_at', '<=', $accountant_timestamp->updated_at)
                ->where('status', 'success')
                ->where('card_id',102)->get();

               $bitcoin_total_tnx_buy = $bitcoin_total_tnx->where('type', 'buy')->sum('quantity');
               $bitcoin_total_tnx_sell = $bitcoin_total_tnx->where('type', 'sell')->sum('quantity');

                $giftcards_totaltnx_buy = Transaction::whereNotNull('id')
                ->where('created_at', '>=', $accountant_timestamp->created_at)
                ->where('updated_at', '<=', $accountant_timestamp->updated_at)
                ->where('status', 'success')->where('type', 'buy')
                ->whereHas('asset', function ($query) {
                    $query->where('is_crypto', 0);
                })->get();
                $giftcards_totaltnx_buy_amount = $giftcards_totaltnx_buy->sum('amount');
                $giftcards_totaltnx_buy = $giftcards_totaltnx_buy->count();

                $giftcards_totaltnx_sell = Transaction::whereNotNull('id')
                ->where('created_at', '>=', $accountant_timestamp->created_at)
                ->where('updated_at', '<=', $accountant_timestamp->updated_at)
                ->where('status', 'success')->where('type', 'sell')
                ->whereHas('asset', function ($query) {
                    $query->where('is_crypto', 0);
                })->get();

                $giftcards_totaltnx_sell_amount = $giftcards_totaltnx_sell->sum('amount');
                $giftcards_totaltnx_sell = $giftcards_totaltnx_sell->count();

                $crypto_totaltnx_buy = Transaction::whereNotNull('id')
                ->where('created_at', '>=', $accountant_timestamp->created_at)
                ->where('updated_at', '<=', $accountant_timestamp->updated_at)
                ->where('status', 'success')->where('type', 'buy')
                ->whereHas('asset', function ($query) {
                    $query->where('is_crypto', 1);
                })->get();
                $crypto_totaltnx_buy_amount = $crypto_totaltnx_buy->sum('amount');
                $crypto_totaltnx_buy = $crypto_totaltnx_buy->count();

                $crypto_totaltnx_sell = Transaction::whereNotNull('id')
                ->where('created_at', '>=', $accountant_timestamp->created_at)
                ->where('updated_at', '<=', $accountant_timestamp->updated_at)
                ->where('type', 'sell')->where('status', 'success')
                ->whereHas('asset', function ($query) {
                    $query->where('is_crypto', 1);
                })->get();

                $crypto_totaltnx_sell_amount = $crypto_totaltnx_sell->sum('amount');
                $crypto_totaltnx_sell = $crypto_totaltnx_sell->count();

                //? utility transactions
                $util_tnx = UtilityTransaction::whereNotNull('id')
                ->orderBy('created_at', 'desc')
                ->where('created_at', '>=', $accountant_timestamp->created_at)
                ->where('updated_at', '<=', $accountant_timestamp->updated_at)->get();
                
                //? utility transactions calculations
                $util_total_tnx = $util_tnx->where('status','success')->count();
                $util_tnx_amount = $util_tnx->where('status','success')->sum('amount');
                $util_tnx_fee = $util_tnx->where('status','success')->sum('convenience_fee');
                $util_amount_paid = $util_tnx->where('status','success')->sum('total');

                $util_tnx = $util_tnx->paginate(50);

                //?naria Wallet
                
                //?deposit
                $nw_deposit_tnx = NairaTransaction::latest()
                ->orderBy('created_at','desc')
                ->where('created_at', '>=', $accountant_timestamp->created_at)
                ->where('updated_at', '<=', $accountant_timestamp->updated_at)
                ->where('transaction_type_id',1)->get();

                $nw_deposit_tnx_total = $nw_deposit_tnx->where('status','success')->count();
                $nw_deposit_amount_paid = $nw_deposit_tnx->where('status','success')->sum('amount_paid');
                $nw_deposit_tnx_charges = $nw_deposit_tnx->where('status','success')->sum('charge');
                $nw_deposit_total_amount = $nw_deposit_tnx->where('status','success')->sum('amount');
                $nw_deposit_tnx = $nw_deposit_tnx->paginate(50);

                //?withdrawal
                $nw_withdrawal_tnx = NairaTransaction::latest()
                ->orderBy('created_at','desc')
                ->where('created_at', '>=', $accountant_timestamp->created_at)
                ->where('updated_at', '<=', $accountant_timestamp->updated_at)
                ->where('transaction_type_id',3)->get();

                $nw_withdrawal_tnx_total = $nw_withdrawal_tnx->where('status','success')->count();
                $nw_withdrawal_amount_paid = $nw_withdrawal_tnx->where('status','success')->sum('amount_paid');
                $nw_withdrawal_tnx_charges = $nw_withdrawal_tnx->where('status','success')->sum('charge');
                $nw_withdrawal_total_amount = $nw_withdrawal_tnx->where('status','success')->sum('amount');
                $nw_withdrawal_tnx = $nw_withdrawal_tnx->paginate(50);

                //?others
                $nw_other_tnx = NairaTransaction::latest()
                ->orderBy('created_at','desc')
                ->where('created_at', '>=', $accountant_timestamp->created_at)
                ->where('updated_at', '<=', $accountant_timestamp->updated_at)
                ->where('transaction_type_id','!=',1)->where('transaction_type_id','!=',3)->get();

                $nw_other_tnx_total = $nw_other_tnx->where('status','success')->count();
                $nw_other_amount_paid = $nw_other_tnx->where('status','success')->sum('amount_paid');
                $nw_other_tnx_charges = $nw_other_tnx->where('status','success')->sum('charge');
                $nw_other_total_amount = $nw_other_tnx->where('status','success')->sum('amount');
                $nw_other_tnx = $nw_other_tnx->paginate(50);

                $segment = date_format($dates, "M d");
                return view('admin.summary.JuniorAccountant.transaction',compact([
                    'segment','accountant','all_tnx','all_tnx_count','show_summary','paginate',
                    'giftcards_totaltnx_buy','giftcards_totaltnx_sell','crypto_totaltnx_buy','crypto_totaltnx_sell',
                    'util_tnx','util_total_tnx', 'util_tnx_amount', 'util_tnx_fee' , 'util_amount_paid',
                    'nw_deposit_tnx','nw_deposit_tnx_total','nw_deposit_amount_paid','nw_deposit_tnx_charges','nw_deposit_total_amount',
                    'nw_withdrawal_tnx','nw_withdrawal_tnx_total','nw_withdrawal_amount_paid','nw_withdrawal_tnx_charges','nw_withdrawal_total_amount',
                    'nw_other_tnx','nw_other_tnx_total','nw_other_amount_paid','nw_other_tnx_charges','nw_other_total_amount',
                    'giftcards_totaltnx_buy_amount','giftcards_totaltnx_sell_amount','crypto_totaltnx_buy_amount','crypto_totaltnx_sell_amount',
                    'bitcoin_total_tnx_buy','bitcoin_total_tnx_sell'

                ]));



            }
            else{
                $segment = date_format($dates, "M d");
                return view('admin.summary.JuniorAccountant.transaction',compact([
                    'segment','accountant','show_summary','paginate'
                ]));
            }
        }
        else{
            //?All Tnx
            $all_tnx = Transaction::whereNotNull('id')
            ->orderBy('created_at', 'desc')
            ->whereDate('created_at', '>=', $current_day_value)
            ->whereDate('updated_at', '<=', $current_day_value)
            ->latest()->get();

            $all_tnx_count = $all_tnx->where('status', 'success')->count();
            $all_tnx = $all_tnx->paginate(50);

            $bitcoin_total_tnx = Transaction::whereNotNull('id')
            ->whereDate('created_at', '>=', $current_day_value)
            ->whereDate('updated_at', '<=', $current_day_value)
            ->where('status', 'success')
            ->where('card_id',102)->get();

            $bitcoin_total_tnx_buy = $bitcoin_total_tnx->where('type', 'buy')->sum('quantity');
            $bitcoin_total_tnx_sell = $bitcoin_total_tnx->where('type', 'sell')->sum('quantity');


            $giftcards_totaltnx_buy = Transaction::whereNotNull('id')
            ->whereDate('created_at', '>=', $current_day_value)
            ->whereDate('updated_at', '<=', $current_day_value)
            ->where('status', 'success')->where('type', 'buy')
            ->whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->get();
            $giftcards_totaltnx_buy_amount = $giftcards_totaltnx_buy->sum('amount');
            $giftcards_totaltnx_buy = $giftcards_totaltnx_buy->count();

            $giftcards_totaltnx_sell = Transaction::whereNotNull('id')
            ->whereDate('created_at', '>=', $current_day_value)
            ->whereDate('updated_at', '<=', $current_day_value)
            ->where('status', 'success')->where('type', 'sell')
            ->whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->get();

            $giftcards_totaltnx_sell_amount = $giftcards_totaltnx_sell->sum('amount');
            $giftcards_totaltnx_sell = $giftcards_totaltnx_sell->count();

            $crypto_totaltnx_buy = Transaction::whereNotNull('id')
            ->whereDate('created_at', '>=', $current_day_value)
            ->whereDate('updated_at', '<=', $current_day_value)
            ->where('status', 'success')->where('type', 'buy')
            ->whereHas('asset', function ($query) {
                $query->where('is_crypto', 1);
            })->get();
            $crypto_totaltnx_buy_amount = $crypto_totaltnx_buy->sum('amount');
            $crypto_totaltnx_buy = $crypto_totaltnx_buy->count();

            $crypto_totaltnx_sell = Transaction::whereNotNull('id')
            ->whereDate('created_at', '>=', $current_day_value)
            ->whereDate('updated_at', '<=', $current_day_value)
            ->whereDate('type', 'sell')->where('status', 'success')
            ->whereHas('asset', function ($query) {
                $query->where('is_crypto', 1);
            })->get();

            $crypto_totaltnx_sell_amount = $crypto_totaltnx_sell->sum('amount');
            $crypto_totaltnx_sell = $crypto_totaltnx_sell->count();

            //? utility transactions
            $util_tnx = UtilityTransaction::whereNotNull('id')
            ->orderBy('created_at', 'desc')
            ->whereDate('created_at', '>=', $current_day_value)
            ->whereDate('updated_at', '<=', $current_day_value)
            ->paginate(50);
            
            //? utility transactions calculations
            $util_total_tnx = $util_tnx->where('status','success')->count();
            $util_tnx_amount = $util_tnx->where('status','success')->sum('amount');
            $util_tnx_fee = $util_tnx->where('status','success')->sum('convenience_fee');
            $util_amount_paid = $util_tnx->where('status','success')->sum('total');

            //?naria Wallet
                
                //?deposit
                $nw_deposit_tnx = NairaTransaction::latest()
                ->orderBy('created_at','desc')
                ->whereDate('created_at', '>=', $current_day_value)
                ->whereDate('updated_at', '<=', $current_day_value)
                ->where('transaction_type_id',1)->get();

                $nw_deposit_tnx_total = $nw_deposit_tnx->where('status','success')->count();
                $nw_deposit_amount_paid = $nw_deposit_tnx->where('status','success')->sum('amount_paid');
                $nw_deposit_tnx_charges = $nw_deposit_tnx->where('status','success')->sum('charge');
                $nw_deposit_total_amount = $nw_deposit_tnx->where('status','success')->sum('amount');
                $nw_deposit_tnx = $nw_deposit_tnx->paginate(50);

                //?withdrawal
                $nw_withdrawal_tnx = NairaTransaction::latest()
                ->orderBy('created_at','desc')
                ->whereDate('created_at', '>=', $current_day_value)
                ->whereDate('updated_at', '<=', $current_day_value)
                ->where('transaction_type_id',3)->get();

                $nw_withdrawal_tnx_total = $nw_withdrawal_tnx->where('status','success')->count();
                $nw_withdrawal_amount_paid = $nw_withdrawal_tnx->where('status','success')->sum('amount_paid');
                $nw_withdrawal_tnx_charges = $nw_withdrawal_tnx->where('status','success')->sum('charge');
                $nw_withdrawal_total_amount = $nw_withdrawal_tnx->where('status','success')->sum('amount');
                $nw_withdrawal_tnx = $nw_withdrawal_tnx->paginate(50);

                //?others
                $nw_other_tnx = NairaTransaction::latest()
                ->orderBy('created_at','desc')
                ->whereDate('created_at', '>=', $current_day_value)
                ->whereDate('updated_at', '<=', $current_day_value)
                ->where('transaction_type_id','!=',1)->where('transaction_type_id','!=',3)->get();

                $nw_other_tnx_total = $nw_other_tnx->where('status','success')->count();
                $nw_other_amount_paid = $nw_other_tnx->where('status','success')->sum('amount_paid');
                $nw_other_tnx_charges = $nw_other_tnx->where('status','success')->sum('charge');
                $nw_other_total_amount = $nw_other_tnx->where('status','success')->sum('amount');
                $nw_other_tnx = $nw_other_tnx->paginate(50);

                $segment = date_format($dates, "M d");
                return view('admin.summary.JuniorAccountant.transaction',compact([
                    'segment','accountant','all_tnx','all_tnx_count','show_summary','paginate',
                    'giftcards_totaltnx_buy','giftcards_totaltnx_sell','crypto_totaltnx_buy','crypto_totaltnx_sell',
                    'util_tnx','util_total_tnx', 'util_tnx_amount', 'util_tnx_fee' , 'util_amount_paid',
                    'nw_deposit_tnx','nw_deposit_tnx_total','nw_deposit_amount_paid','nw_deposit_tnx_charges','nw_deposit_total_amount',
                    'nw_withdrawal_tnx','nw_withdrawal_tnx_total','nw_withdrawal_amount_paid','nw_withdrawal_tnx_charges','nw_withdrawal_total_amount',
                    'nw_other_tnx','nw_other_tnx_total','nw_other_amount_paid','nw_other_tnx_charges','nw_other_total_amount',
                    'giftcards_totaltnx_buy_amount','giftcards_totaltnx_sell_amount','crypto_totaltnx_buy_amount','crypto_totaltnx_sell_amount',
                    'bitcoin_total_tnx_buy','bitcoin_total_tnx_sell'

                ]));


        }
        
    }

    public function sort_summary_tnx(Request $request)
    {
        $paginate = false;
        $show_summary = (Auth::user()->role != 777 || $request->Accountant == 'null') ? true : false;
        $start_date =  explode('T',$request->startdate);
        $end_date = explode('T',$request->enddate);
        $accountant_name = null;
        if(!isset($request->Accountant) || $request->Accountant == 'null' )
        {
            $user = Auth::user();
        }
        else{
            $user = User::find($request->Accountant);
            $accountant_name = $user->first_name ?: $user->email;
        }

        

        $accountant = User::whereIn('role', [777])->where('id','!=',$user->id)->get();
        $accountant_timestamp = AccountantTimeStamp::whereDate('created_at','>=',$start_date[0])
        ->whereDate('updated_at','<=',$end_date[0])->where('user_id',$user->id)->get();

        $start = str_replace('T',' ',$request->startdate);
        $end = str_replace('T',' ',$request->enddate);
        $segment = 
        date_format(date_create($start),"d-M-y h:ia")
        ." to ". 
        date_format(date_create($end),"d-M-y h:ia");    

            if(!empty($accountant_timestamp))
            {
                $all_transaction_Collection = collect([]);
                $giftcard_collection = collect([]);
                $crypto_collection = collect([]);
                $util_collection = collect([]);
                $nw_collection = collect([]);
                $bitcoin_collection = collect([]);
                
                foreach ($accountant_timestamp  as $at) {
                    $all_tranx = Transaction::whereNotNull('id')
                    ->orderBy('created_at', 'desc')
                    ->where('updated_at', '>=', $at->created_at)
                    ->where('updated_at', '<=', $at->updated_at)->get();
                    $all_transaction_Collection = $all_transaction_Collection->concat($all_tranx);
                    
                    $gift_tranx = Transaction::whereNotNull('id')
                    ->orderBy('created_at', 'desc')
                    ->where('updated_at', '>=', $at->created_at)
                    ->where('updated_at', '<=', $at->updated_at)
                    ->where('status', 'success')
                    ->whereHas('asset', function ($query) {
                        $query->where('is_crypto', 0);
                    })->get();
                    $giftcard_collection = $giftcard_collection->concat($gift_tranx);

                    $crypto_tranx = Transaction::whereNotNull('id')
                    ->orderBy('created_at', 'desc')
                    ->where('updated_at', '>=', $at->created_at)
                    ->where('updated_at', '<=', $at->updated_at)
                    ->where('status', 'success')
                    ->whereHas('asset', function ($query) {
                        $query->where('is_crypto', 1);
                    })->get();
                    $crypto_collection = $crypto_collection->concat($crypto_tranx);

                    $util_tranx = UtilityTransaction::whereNotNull('id')
                    ->orderBy('created_at', 'desc')
                    ->where('created_at', '>=', $at->created_at)
                    ->where('updated_at', '<=', $at->updated_at)->get();
                    $util_collection = $util_collection->concat($util_tranx);

                    $nw_tranx = NairaTransaction::latest()
                        ->orderBy('created_at','desc')
                        ->where('created_at', '>=', $at->created_at)
                        ->where('updated_at', '<=', $at->updated_at)->get();
                    $nw_collection = $nw_collection->concat($nw_tranx);

                    $bitcoin_tranx = Transaction::whereNotNull('id')
                    ->where('created_at', '>=', $at->created_at)
                    ->where('updated_at', '<=', $at->updated_at)
                    ->where('status', 'success')
                    ->where('card_id',102)->get();

                    $bitcoin_collection = $bitcoin_collection->concat($bitcoin_tranx); 
                }
                //? All Transactions
                $all_tnx = $all_transaction_Collection->where('updated_at', '>=', str_replace('T',' ',$request->startdate))
                ->where('updated_at', '<=', str_replace('T',' ',$request->enddate));

                $all_tnx_count = $all_tnx->where('status', 'success')->count();

                $all_tnx = $all_tnx;
                
                $bitcoin_total_tnx_buy = $bitcoin_collection->where('type', 'buy')->sum('quantity');
                $bitcoin_total_tnx_sell = $bitcoin_collection->where('type', 'sell')->sum('quantity');

                $giftcards_totaltnx_buy = $giftcard_collection->where('type', 'buy')->count();
                $giftcards_totaltnx_buy_amount = $giftcard_collection->where('type', 'buy')->sum('amount');

                $giftcards_totaltnx_sell = $giftcard_collection->where('type', 'sell')->count();
                $giftcards_totaltnx_sell_amount = $giftcard_collection->where('type', 'sell')->sum('amount');

                $crypto_totaltnx_buy = $crypto_collection->where('type', 'buy')->count();
                $crypto_totaltnx_buy_amount =  $crypto_collection->where('type', 'buy')->sum('amount');

                $crypto_totaltnx_sell = $crypto_collection->where('type', 'sell')->count();
                $crypto_totaltnx_sell_amount =  $crypto_collection->where('type', 'sell')->sum('amount');

                //?utility
                $util_total_tnx = $util_collection->where('status','success')->count();
                
                $util_tnx_amount = $util_collection->where('status','success')->sum('amount');

                $util_tnx_fee = $util_collection->where('status','success')->sum('convenience_fee');

                $util_amount_paid = $util_collection->where('status','success')->sum('total');
                
                $util_tnx = $util_collection;

                //?pay bridge
                //?deposit
                $nw_deposit_tnx = $nw_collection->where('transaction_type_id',1);

                $nw_deposit_tnx_total = $nw_deposit_tnx->where('status','success')->count();
                $nw_deposit_amount_paid = $nw_deposit_tnx->where('status','success')->sum('amount_paid');
                $nw_deposit_tnx_charges = $nw_deposit_tnx->where('status','success')->sum('charge');
                $nw_deposit_total_amount = $nw_deposit_tnx->where('status','success')->sum('amount');

                $nw_withdrawal_tnx = $nw_collection->where('transaction_type_id',3);
                $nw_withdrawal_tnx_total = $nw_withdrawal_tnx->where('status','success')->count();
                $nw_withdrawal_amount_paid = $nw_withdrawal_tnx->where('status','success')->sum('amount_paid');
                $nw_withdrawal_tnx_charges = $nw_withdrawal_tnx->where('status','success')->sum('charge');
                $nw_withdrawal_total_amount = $nw_withdrawal_tnx->where('status','success')->sum('amount');

                $nw_other_tnx = $nw_collection->where('transaction_type_id','!=',1)->where('transaction_type_id','!=',3);
                $nw_other_tnx_total = $nw_other_tnx->where('status','success')->count();
                $nw_other_amount_paid = $nw_other_tnx->where('status','success')->sum('amount_paid');
                $nw_other_tnx_charges = $nw_other_tnx->where('status','success')->sum('charge');
                $nw_other_total_amount = $nw_other_tnx->where('status','success')->sum('amount');

                return view('admin.summary.JuniorAccountant.transaction',compact([
                    'segment','accountant','all_tnx','all_tnx_count','show_summary','paginate',
                    'giftcards_totaltnx_buy','giftcards_totaltnx_sell','crypto_totaltnx_buy','crypto_totaltnx_sell',
                    'util_tnx','util_total_tnx', 'util_tnx_amount', 'util_tnx_fee' , 'util_amount_paid',
                    'nw_deposit_tnx','nw_deposit_tnx_total','nw_deposit_amount_paid','nw_deposit_tnx_charges','nw_deposit_total_amount',
                    'nw_withdrawal_tnx','nw_withdrawal_tnx_total','nw_withdrawal_amount_paid','nw_withdrawal_tnx_charges','nw_withdrawal_total_amount',
                    'nw_other_tnx','nw_other_tnx_total','nw_other_amount_paid','nw_other_tnx_charges','nw_other_total_amount',
                    'giftcards_totaltnx_buy_amount','giftcards_totaltnx_sell_amount','crypto_totaltnx_buy_amount','crypto_totaltnx_sell_amount',
                    'bitcoin_total_tnx_buy','bitcoin_total_tnx_sell'
                ]));
            }
            else{
                return view('admin.summary.JuniorAccountant.transaction',compact([
                    'segment','accountant_name, accountant','show_summary'
                ]));
            }
        

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
