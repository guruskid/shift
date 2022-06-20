<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\Card;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\Transaction;
use App\UtilityTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SummaryController extends Controller
{
    public function Transactions24hrs($date)
    {
        $crypto_collection = collect([]);
        $giftCard_collection = collect([]);
        $utilities_collection = collect([]);

        for($i = 0; $i < 24 ; $i++)
        {
            $crypto_transaction = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 1);
            })->where("created_at",">=",$date." $i:00:00")->where("created_at","<=",$date." $i:59:59")->where('status', 'success')->count();
            $data = array($i=>["start"=>"$i:00:00",
                                "end"=>"$i:59:59",
                                "transactions"=>$crypto_transaction]); 
            $crypto_collection = $crypto_collection->concat($data);

            $giftCard_transaction = Transaction::whereHas('asset', function ($query) {
                $query->where('is_crypto', 0);
            })->where("created_at",">=",$date." $i:00:00")->where("created_at","<=",$date." $i:59:59")->where('status', 'success')->count();
            $data = array($i=>["start"=>"$i:00:00",
                                "end"=>"$i:59:59",
                                "transactions"=>$giftCard_transaction]); 
            $giftCard_collection = $giftCard_collection->concat($data);

            $utilities_transaction = UtilityTransaction::where("created_at",">=",$date." $i:00:00")
            ->where("created_at","<=",$date." $i:59:59")->where('status', 'success')->count();
            $data = array($i=>["start"=>"$i:00:00",
                                "end"=>"$i:59:59",
                                "transactions"=>$utilities_transaction]); 
            $utilities_collection = $utilities_collection->concat($data);

        }
        $data = [$crypto_collection,$giftCard_collection,$utilities_collection];
        return $data;
    }
    public function timeGraph($date = null)
    {
        if($date == null)
        {
            $date = date('Y-m-d');
        }

        $crypto_transaction = $this->Transactions24hrs($date)[0];
        $giftCard_transaction = $this->Transactions24hrs($date)[1];
        $utilities_transaction = $this->Transactions24hrs($date)[2];

        return response()->json([
            'success' => true,
            'crypto' => $crypto_transaction,
            'gift_card' => $giftCard_transaction,
            'utility' => $utilities_transaction,
        ], 200);
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
            $ct->total_transactions = $value; 

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
            if($token_value ==1)
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

        //?getting crypto token 
        $crypto_tokens = $this->cryptoAssetData($date,1);

        return response()->json([
            'success' => true,
            'transaction_number' => $number_of_tranx->count(),
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
        return response()->json([
            'success' => true,
            'transaction_number' => $number_of_tranx->count(),
            'crypto_tokens' => $crypto_tokens,
            'transactions' => $number_of_tranx->paginate(10)
        ], 200);

    }

    public function giftCardTransactions()
    {
        $date = date('Y-m-d');

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

    public function transactions($category = null,$date = null)
    {
        $date = ($date != null) ? $date: Carbon::now();
        $total_number = ($date != null) ? 40 : 100;
        $is_crypto = ($category == "Crypto") ? 1:0;

        $data_collection = collect([]);
        for ($i=0; $i <= $total_number; $i++) { 
            $highest_naira_user_name = null;
            $highest_freq_user_name = null;
            $dates = Carbon::parse($date)->subDays($i)->format("Y-m-d");
            $transactions = Transaction::whereDate('created_at',$dates)->where('status', 'success');
            if($category != null){
                $transactions = $transactions->whereHas('asset', function($query) use ($is_crypto){
                    $query->where('is_crypto', $is_crypto);
                });
            }
            $transactions = $transactions->get();

            $successful_tranx =  $transactions->where('status', 'success')->count();
            $declined_tranx = $transactions->where('status','!=', 'success')->where('status','!=', 'waiting')->count();

            $highest_naira_user = Transaction::select('*', DB::raw('sum(amount_paid) AS total_amount'))
            ->whereDate('created_at',$dates)->where('status', 'success');
            if($category){
                $highest_naira_user = $highest_naira_user->whereHas('asset', function($query) use ($is_crypto){
                    $query->where('is_crypto', $is_crypto);
                });
            }
            $highest_naira_user = $highest_naira_user->groupBy('user_id')->orderBy(DB::raw('total_amount'), 'desc')->first();
            if(isset($highest_naira_user->user)){
                $highest_naira_user_name = $highest_naira_user->user->first_name." ".$highest_naira_user->user->last_name;
            }

            $highest_freq_user = Transaction::select('*', DB::raw('count(user_id) AS count'))
            ->whereDate('created_at',$dates)->where('status', 'success');
            if($category){
                $highest_freq_user = $highest_freq_user->whereHas('asset', function($query) use ($is_crypto){
                    $query->where('is_crypto', $is_crypto);
                });
            }
            $highest_freq_user = $highest_freq_user->groupBy('user_id')->orderBy(DB::raw('count'), 'desc')->first();
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

    public function transactionsDetails()
    {
       $data_collection = $this->transactions();
        return response()->json([
            'success' => true,
            'data' => $data_collection->paginate(10),
        ], 200);

    }

    public function sortTransaction(Request $r)
    {
        $data_collection = $this->transactions($r->category,$r->date);
        return response()->json([
            'success' => true,
            'data' => $data_collection->paginate(10),
        ], 200);
    }
}
