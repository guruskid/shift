<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTransaction;
use App\Notification;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function dashboard()
    {
        $transactions = Auth::user()->transactions;
        $naira_wallet = Auth::user()->nairaWallet;
        $naira_wallet_transactions = NairaTransaction::where('cr_user_id', Auth::user()->id)->orWhere('dr_user_id', Auth::user()->id)->latest()->with('transactionType')->get();

        return response()->json([
            'success' => true,
            'assets_transactions' => $transactions,
            'naira_wallet' => $naira_wallet,
            'naira_wallet_transactions' => $naira_wallet_transactions,
        ]);
    }
    public function notifications()
    {
        $user_id = 1;
        $nots = Notification::where('user_id', 0)->orWhere('user_id', $user_id)->orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $nots
        ]);
    }
}
