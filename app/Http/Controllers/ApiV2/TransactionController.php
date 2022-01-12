<?php

namespace App\Http\Controllers\ApiV2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTransaction;
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
}
