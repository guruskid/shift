<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTransaction;
use Illuminate\Support\Facades\Auth;

class NairaWalletController extends Controller
{
    public function allTransactions()
    {
        $naira_transactions = NairaTransaction::where('cr_user_id', Auth::user()->id)->orWhere('dr_user_id', Auth::user()->id)->latest()->with('transactionType')->get();
        return response()->json([
            'success' => true,
            'data' => $naira_transactions
        ]);
    }
}
