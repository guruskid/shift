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
        foreach($tranx as $tr): 
            $tr->transactionType = "CryptoGiftCard";
        endforeach;

        $utilTranx = Auth::user()->utilityTransaction;
        foreach($utilTranx as $ut):
            $ut->transactionType = "utilities";
        endforeach;

        $p2pTranx = Auth::user()->nairaTrades;
        foreach($p2pTranx as $tr):
            $tr->transactionType = "payBridge";
        endforeach;

        $allTranx = collect($tranx->toArray(),$utilTranx->toArray(),$p2pTranx->toArray())
        ->sortByDesc('updated_at')
        ->groupBy(function($date) {
            return Carbon::parse($date['updated_at'])->format("d F Y");
        });

        // $buyTranx = collect($tranx->where('type','buy')->toArray(),$utilTranx->toArray(),$p2pTranx->toArray())
        // ->sortByDesc('updated_at')
        // ->groupBy(function($date) {
        //     return Carbon::parse($date['updated_at'])->format("d F Y");
        // });
        
        // $sellTranx = collect($tranx->where('type','sell')->toArray())
        // ->sortByDesc('updated_at')
        // ->groupBy(function($date) {
        //     return Carbon::parse($date['updated_at'])->format("d F Y");
        // });

        return response()->json([
            'success' => true,
            'allTransactions' => $allTranx,
            // 'buyTransactions' => $buyTranx,
            // 'sellTransactions' => $sellTranx,
        ]);
    }

    public function showUserTransaction(Request $r)
    {
        if(!in_array($r->transactionType,['CryptoGiftCard','utilities','payBridge']))
        {
            return response()->json([
                'success' => false,
                'message' => "Error Wrong Transaction Type"
            ],401);
        }

        $userTranx = null;
        if($r->transactionType == 'CryptoGiftCard'):
            $userTranx = Auth::user()->transactions;
        endif;

        if($r->transactionType == 'utilities'):
            $userTranx = Auth::user()->utilityTransaction;
        endif;

        if($r->transactionType == 'payBridge'):
            $userTranx = Auth::user()->nairaTrades;
        endif;

        $transaction = $userTranx->where('id',$r->id)->first();
        
        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }
}
