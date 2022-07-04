<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\BitcoinWallet;
use App\Card;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\NairaTransaction;
use App\Transaction;
use App\UtilityTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SummaryController extends Controller
{
    public function Transactions24hrs($date)
    {
        $data_collection = collect([]);
        $crypto_transactions = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 1);
        })->where('status','success')->whereDate('created_at',$date)->get();

        $crypto = $crypto_transactions->where("created_at",">=",$date." 01:01:00")->where("created_at","<=",$date." 02:00:00");

        $giftCard_transaction = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->where('status','success')->whereDate('created_at',$date)->get();

        $utilities_transaction = UtilityTransaction::where('status','success')->whereDate('created_at',$date)->get();

        $airtime_data = NairaTransaction::whereIn('transaction_type_id',[9,10])->where('status','success')->whereDate('created_at',$date)->get();

        //* do a loop on the collections
        for($i = 0; $i<=23; $i++){
            $previous_time = $i - 1;
            $current_time = $i;

            if($i>= 0 AND $i <=9)
            {
                $previous_time = ($i == 0) ? 23 : "0".($i - 1);
                $current_time = "0$i";
            }

            $crypto = $crypto_transactions->where("created_at",">=",$date." $previous_time:01:00")->where("created_at","<=",$date." $current_time:00:00")->count();
            $giftcard = $giftCard_transaction->where("created_at",">=",$date." $previous_time:01:00")->where("created_at","<=",$date." $current_time:00:00")->count();
            $utility = $utilities_transaction->where("created_at",">=",$date." $previous_time:01:00")->where("created_at","<=",$date." $current_time:00:00")->count();
            $airtime_data_value = $airtime_data->where("created_at",">=",$date." $previous_time:01:00")->where("created_at","<=",$date." $current_time:00:00")->count();

            $collection = array(collect(["$current_time:00" => [
                "crypto"=>$crypto,"giftCards"=>$giftcard,"utility"=>$utility,'airtime_data'=>$airtime_data_value,
            ]]));
            $data_collection = $data_collection->concat($collection);

        }
        return $data_collection;

    }
    public function timeGraph($date = null)
    {

        if($date == null)
        {
            $date = now()->format('Y-m-d');
        }
        return response()->json([
            'success' => true,
            'data' => $this->Transactions24hrs($date)
        ],200);
    }

    public function totalAsset($card_id,$date)
    {
        $data = 0;
        $transactions = Transaction::where('card_id',$card_id)
        ->whereDate("created_at",">=",$date)->whereDate("created_at","<=",$date)->where('status', 'success')->get();
        foreach ($transactions as $t) {
            $data += ($t->amount * $t->quantity);
        }
        return $data;
    }
    public function cryptoAssetData($date,$token_value,$type = null)
    {
        $tokens = Card::where('is_crypto',$token_value)->get();
        foreach ($tokens as $ct) {

            //?total transactions
            $value = Transaction::where('card_id',$ct->id)
            ->whereDate("created_at",">=",$date)->whereDate("created_at","<=",$date)->where('status', 'success');
            if($type != null)
            {
                $value = $value->where('type',$type);
            }
            $value = $value->count();
            $ct->noOfTrans = $value; 

            //?users
            $value = Transaction::where('card_id',$ct->id)
            ->whereDate("created_at",">=",$date)->whereDate("created_at","<=",$date)->where('status', 'success');

            if($type != null)
            {
                $value = $value->where('type',$type);
            }

            $value=$value->get()->unique('user_id')->count();
            $ct->total_users = $value;

            //total_traded_asset
            //?total_traded_asset
            if($token_value == 1)
            {
                $value = Transaction::where('card_id',$ct->id)
                ->whereDate("created_at",">=",$date)->whereDate("created_at","<=",$date)->where('status', 'success');
                if($type != null)
                {
                    $value = $value->where('type',$type);
                }
                $value = $value->sum('amount');
            }
            else{
                $value = $this->totalAsset($ct->id,$date);
            }

            $ct->traded_value = $value;
        }
        $tokens = $tokens->map->only(['id','name','noOfTrans','traded_value','total_users']);
        $tokens = collect($tokens);
        return $tokens;
    }

    public function cryptoTransaction()
    {
        /**
         * $token = null, $type = null, $date = null
         * ?list of token, each of that token should have the i) number of transactions ii) value traded iii)number of users
         * ?type
         * ?date
         * ?daily total of crypto transaction
         */

        $date = date('Y-m-d');

        $number_of_tranx = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 1);
        })->whereDate("created_at",">=",$date)->whereDate("created_at","<=",$date)->where('status', 'success')->get();

        foreach($number_of_tranx as $nt){
            if($nt->user){
                $nt->name = $nt->user->first_name." ".$nt->user->last_name;
                $nt->dp = $nt->user->dp;
                $nt->date = $nt->created_at->format('d M Y');
                $nt->TokenPrice = $nt->card_price;
                $nt->Amount = $nt->quantity;
                $nt->valueNGN = $nt->amount_paid;
                $nt->valueUSD = $nt->amount;
            }
        }
        $number_of_tranx = $number_of_tranx->map->only(['id','user_id','name','TokenPrice','Amount','valueNGN','valueUSD','dp']);
        $number_of_tranx = collect($number_of_tranx);
        //?getting crypto token 
        $crypto_tokens = $this->cryptoAssetData($date,1);

        return response()->json([
            'success' => true,
            'daily_total' => $number_of_tranx->count(),
            'crypto_tokens' => $crypto_tokens,
            'transactions' => $number_of_tranx->paginate(10)
        ], 200);

    }

    public function sortCryptoTransaction(Request $r)
    {
        $date = ($r->date == null) ? date('Y-m-d') : $r->date;

        if($r->token_id != null){
            $number_of_tranx = Transaction::where('card_id',$r->token_id)
            ->whereDate("created_at",">=",$date)->whereDate("created_at","<=",$date)->where('status', 'success')->get();
        }
        else{
            $number_of_tranx = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 1);
            })->whereDate("created_at",">=",$date)->whereDate("created_at","<=",$date)->where('status', 'success')->get();
        }


        $crypto_tokens = $this->cryptoAssetData($date,1,$r->type);
        if($r->type != null)
        {
            $number_of_tranx = $number_of_tranx->where('type',$r->type);
        } 

        foreach($number_of_tranx as $nt){
            if($nt->user){
                $nt->name = $nt->user->first_name." ".$nt->user->last_name;
                $nt->dp = $nt->user->dp;
                $nt->date = $nt->created_at->format('d M Y');
                $nt->TokenPrice = $nt->card_price;
                $nt->Amount = $nt->quantity;
                $nt->valueNGN = $nt->amount_paid;
                $nt->valueUSD = $nt->amount;
            }
        }

        $number_of_tranx = $number_of_tranx->map->only(['id','user_id','name','TokenPrice','Amount','valueNGN','valueUSD','dp']);
        $number_of_tranx = collect($number_of_tranx);
        return response()->json([
            'success' => true,
            'daily_total' => $number_of_tranx->count(),
            'crypto_tokens' => $crypto_tokens,
            'transactions' => $number_of_tranx->paginate(10)
        ], 200);

    }

    //TODO there is need to work on this 
    public function giftCardTransactions()
    {
        $date = now()->format('Y-m-d');

        $number_of_tranx = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 0);
        })->whereDate("created_at",">=",$date)->whereDate("created_at","<=",$date)->where('status', 'success')->get();

        //?getting crypto token
        $tokens = $this->cryptoAssetData($date,0);

        return response()->json([
            'success' => true,
            'transaction_number' => $number_of_tranx->count(),
            'crypto_tokens' => $tokens,
            'transactions' => $number_of_tranx->paginate(10)
        ], 200);
    }

    public function sortGiftCardTransactions(Request $r)
    {
        $date = ($r->date == null) ? date('Y-m-d') : $r->date;
        if($r->token_id != null){
            $number_of_tranx = Transaction::where('card_id',$r->token_id)
            ->whereDate("created_at",">=",$date)->whereDate("created_at","<=",$date)->where('status', 'success')->get();
        }
        else{
            $number_of_tranx = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->whereDate("created_at",">=",$date)->whereDate("created_at","<=",$date)->where('status', 'success')->get();
        }


        $crypto_tokens = $this->cryptoAssetData($date,0,$r->type);
        if($r->type != null)
        {
            $number_of_tranx = $number_of_tranx->where('type',$r->type);
        }
        return response()->json([
            'success' => true,
            'transaction_number' => $number_of_tranx->count(),
            'crypto_tokens' => $crypto_tokens,
            'transactions' => $number_of_tranx->paginate(10)
        ], 200);
    }

    public function loadTransactionDetails()
    {
        $date =now()->format('Y-m-d');
        $total_number = 100;
        $data_collection =  collect([]);

        for ($i=0; $i <= $total_number; $i++) {
            $highest_naira_user_name = null;
            $highest_freq_user_name = null;

            $dates = Carbon::parse($date)->subDays($i)->format("Y-m-d");

            $transactions = Transaction::whereDate('created_at',$dates)->get();
            $utility_transactions = UtilityTransaction::whereDate('created_at',$dates)->where('status', 'success')->get();

            $success_transactions = $transactions->where('status', 'success')->count();
            $declined_transactions = $transactions->whereIn('status',['failed','declined'])->count();

            $utility_success = $utility_transactions->where('status', 'success')->count();
            $utility_declined = $utility_transactions->whereNotIn('status',['success','pending'])->count();

            $successful_tranx = $success_transactions + $utility_success;
            $declined_tranx = $declined_transactions + $utility_declined;

            $highest_naira_user =  Transaction::select('*', DB::raw('sum(amount_paid) AS total_amount'))
            ->whereDate('created_at',$dates)->where('status', 'success')->groupBy('user_id')
            ->orderBy(DB::raw('total_amount'), 'desc')->first();
            if(isset($highest_naira_user->user)){
                $highest_naira_user_name = $highest_naira_user->user->first_name." ".$highest_naira_user->user->last_name;
            }

            $highest_freq_user = Transaction::select('*', DB::raw('count(user_id) AS count'))
            ->whereDate('created_at',$dates)->where('status', 'success')->groupBy('user_id')
            ->orderBy(DB::raw('count'), 'desc')->first();
            if(isset($highest_freq_user->user)){
                $highest_freq_user_name = $highest_freq_user->user->first_name." ".$highest_freq_user->user->last_name;
            }

            $successful_tranx_buy =  $transactions->where('status', 'success')->where('type','buy')->count();
            $successful_tranx_sell =  $transactions->where('status', 'success')->where('type','sell')->count();
            $naira_trades = NairaTrade::whereDate('created_at',$date)->where('status', 'success')->count();

            $data = array($i=>[
                'successful_transaction' => $successful_tranx,
                'declined_transaction' =>$declined_tranx,
                'highest_naira_user' => $highest_naira_user_name,
                'highest_freq_user' => $highest_freq_user_name,
                'successful_tranx_buy' => $successful_tranx_buy,
                'successful_tranx_sell' =>$successful_tranx_sell,
                'internal_transfer' => $naira_trades,
                'date' => $dates
            ]);
            $data_collection = $data_collection->concat($data);
        }
        return $data_collection->sortByDesc('date');
    }

    public function TransactionsTD($date, $type, $category ,$is_crypto)
    {
        if($date == null)
        {
            $date = now()->format('Y-m-d');
        }
        $total_number = 100;
        $data_collection =  collect([]);

        for ($i=0; $i <= $total_number; $i++) {
            $highest_naira_user_name = null;
            $highest_freq_user_name = null;

            $dates = Carbon::parse($date)->subDays($i)->format("Y-m-d");

            $transactions = Transaction::whereDate('created_at',$dates);
            if($category)
            {
                $transactions = $transactions->whereHas('asset', function($query) use ($is_crypto){
                    $query->where('is_crypto',$is_crypto);
                });
            }
            if($type)
            {
                $transactions = $transactions->where('type',$type);
            }
            $transactions = $transactions->get();

            $successful_tranx = $transactions->where('status', 'success')->count();
            $declined_tranx = $transactions->whereIn('status',['failed','declined'])->count();

            $highest_naira_user =  Transaction::select('*', DB::raw('sum(amount_paid) AS total_amount'));
            if($category)
            {
                $highest_naira_user = $highest_naira_user->whereHas('asset', function($query) use ($is_crypto){
                    $query->where('is_crypto',$is_crypto);
                });
            }
            if($type)
            {
                $highest_naira_user = $highest_naira_user->where('type',$type);
            }
            $highest_naira_user = $highest_naira_user->whereDate('created_at',$dates)->where('status', 'success')->groupBy('user_id')
            ->orderBy(DB::raw('total_amount'), 'desc')->first();

            if(isset($highest_naira_user->user)){
                $highest_naira_user_name = $highest_naira_user->user->first_name." ".$highest_naira_user->user->last_name;
            }

            $highest_freq_user = Transaction::select('*', DB::raw('count(user_id) AS count'));
            if($category);
            {
                $highest_freq_user = $highest_freq_user->whereHas('asset', function($query) use ($is_crypto){
                    $query->where('is_crypto',$is_crypto);
                });
            }
            if($type)
            {
                $highest_freq_user = $highest_freq_user->where('type',$type);
            }
            $highest_freq_user = $highest_freq_user->whereDate('created_at',$dates)->where('status', 'success')->groupBy('user_id')
            ->orderBy(DB::raw('count'), 'desc')->first();

            if(isset($highest_freq_user->user)){
                $highest_freq_user_name = $highest_freq_user->user->first_name." ".$highest_freq_user->user->last_name;
            }

            $successful_tranx_buy =  $transactions->where('status', 'success')->where('type','buy')->count();
            $successful_tranx_sell =  $transactions->where('status', 'success')->where('type','sell')->count();
            $naira_trades = NairaTrade::whereDate('created_at',$date)->where('status', 'success')->count();

            $data = array($i=>[
                'successful_transaction' => $successful_tranx,
                'declined_transaction' =>$declined_tranx,
                'highest_naira_user' => $highest_naira_user_name,
                'highest_freq_user' => $highest_freq_user_name,
                'successful_tranx_buy' => $successful_tranx_buy,
                'successful_tranx_sell' =>$successful_tranx_sell,
                'internal_transfer' => $naira_trades,
                'date' => $dates
            ]);
            $data_collection = $data_collection->concat($data);
        }
        return $data_collection->sortByDesc('date');
    }

    public function otherTransactionsTD($date)
    {
        if($date == null)
        {
            $date = now()->format('Y-m-d');
        }
        $total_number = 100;
        $data_collection =  collect([]);

        for ($i=0; $i <= $total_number; $i++) {
            $highest_naira_user_name = null;
            $highest_freq_user_name = null;

            $dates = Carbon::parse($date)->subDays($i)->format("Y-m-d");

            $transactions = UtilityTransaction::whereDate('created_at',$dates)->get();

            $successful_tranx = $transactions->where('status', 'success')->count();
            $declined_tranx = $transactions->whereNotIn('status',['success','pending'])->count();

            $highest_naira_user =  UtilityTransaction::select('*', DB::raw('sum(amount) AS total_amount'))
            ->whereDate('created_at',$dates)->where('status', 'success')->groupBy('user_id')
            ->orderBy(DB::raw('total_amount'), 'desc')->first();

            if(isset($highest_naira_user->user)){
                $highest_naira_user_name = $highest_naira_user->user->first_name." ".$highest_naira_user->user->last_name;
            }

            $highest_freq_user = UtilityTransaction::select('*', DB::raw('count(user_id) AS count'))
            ->whereDate('created_at',$dates)->where('status', 'success')->groupBy('user_id')
            ->orderBy(DB::raw('count'), 'desc')->first();

            if(isset($highest_freq_user->user)){
                $highest_freq_user_name = $highest_freq_user->user->first_name." ".$highest_freq_user->user->last_name;
            }
            $successful_tranx_buy =  0;
            $successful_tranx_sell =  0;
            $naira_trades = NairaTrade::whereDate('created_at',$date)->where('status', 'success')->count();

            $data = array($i=>[
                'successful_transaction' => $successful_tranx,
                'declined_transaction' =>$declined_tranx,
                'highest_naira_user' => $highest_naira_user_name,
                'highest_freq_user' => $highest_freq_user_name,
                'successful_tranx_buy' => $successful_tranx_buy,
                'successful_tranx_sell' =>$successful_tranx_sell,
                'internal_transfer' => $naira_trades,
                'date' => $dates
            ]);
            $data_collection = $data_collection->concat($data);
        }
        return $data_collection->sortByDesc('date');
    }
    public function transactionsDetails()
    {
       $data_collection = $this->loadTransactionDetails();
        return response()->json([
            'success' => true,
            'data' => $data_collection->paginate(10),
        ], 200);

    }

    public function sortTransaction(Request $r)
    {
        $validator = Validator::make($r->all(),[
            'category' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }
        $r->category = strtolower($r->category);
        if($r->category == "crypto")
        {
            $data_collection =  $this->TransactionsTD($r->date, $r->type, $r->category, 1);
        }
        if($r->category == "giftcards")
        {
            $data_collection =  $this->TransactionsTD($r->date, $r->type ,$r->category, 0);
        }
        if($r->category == "others")
        {
            $data_collection =  $this->otherTransactionsTD($r->date);
        }
        return response()->json([
            'success' => true,
            'data' => $data_collection->paginate(10)
        ], 200);
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

        return response()->json([
            'success' => true,
           'wallets' => $wallet
        ]);
    }
}
