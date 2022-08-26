<?php

namespace App\Http\Controllers\ApiV2;

use App\Http\Controllers\ApiV2\Admin\UtilityController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function allCardTransactions()
    {
        $transactions = Auth::user()->transactions;
        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function utilityTransactions()
    {
        $transactions = Auth::user()->utilityTransaction;
        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function bitcoinWalletTransactions()
    {
        if (Auth::user()->bitcoinWallet) {
            $transactions = Auth::user()->bitcoinWallet->transactions;
        } else {
            $transactions = [];
        }


        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);
    }

    public function nairaTransactions()
    {
        $naira_transactions = NairaTransaction::where('cr_user_id', Auth::user()->id)->orWhere('dr_user_id', Auth::user()->id)->latest()->with('transactionType')->get();
        return response()->json([
            'success' => Auth::user()->id,
            'data' => $naira_transactions
        ]);
    }

    public function AllUserTransactions()
    {
        $tranx = Auth::user()->transactions;
        foreach($tranx as $tr) 
            $tr->isUtility = 0;
        

        $utilTranx = Auth::user()->utilityTransaction;
        foreach($utilTranx as $ut) 
            $ut->isUtility = 1;
        

        $allTranx = collect($tranx->toArray(),$utilTranx->toArray())
        ->sortByDesc('updated_at')
        ->groupBy(function($date) {
            return Carbon::parse($date['updated_at'])->format("d F Y");
        });

        $buyTranx = collect($tranx->where('type','buy')->toArray(),$utilTranx->toArray())
        ->sortByDesc('updated_at')
        ->groupBy(function($date) {
            return Carbon::parse($date['updated_at'])->format("d F Y");
        });
        
        $sellTranx = collect($tranx->where('type','sell')->toArray())
        ->sortByDesc('updated_at')
        ->groupBy(function($date) {
            return Carbon::parse($date['updated_at'])->format("d F Y");
        });

        return response()->json([
            'success' => true,
            'all' => $allTranx,
            'buy' => $buyTranx,
            'sell' => $sellTranx,
        ]);
    }

    public function showUserTransaction(Request $r)
    {
        if($r->isUtility >= 2)
        {
            return response()->json([
                'success' => false,
                'message' => "Error Wrong Query Check"
            ],401);
        }

        $UserTranx = ($r->isUtility == 1) ? Auth::user()->utilityTransaction : Auth::user()->transactions;
        $transaction = $UserTranx->where('id',$r->id)->first();
        
        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }
}
