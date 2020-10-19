<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class NairaWalletController extends Controller
{
    public function index(){
        $wallet = Auth::user()->nairaWallet;
        return response()->json([
            'success' => true,
            'data' => $wallet
        ]);
    }
    public function allTransactions()
    {
        $naira_transactions = NairaTransaction::where('cr_user_id', Auth::user()->id)->orWhere('dr_user_id', Auth::user()->id)->latest()->with('transactionType')->get();
        return response()->json([
            'success' => true,
            'data' => $naira_transactions
        ]);
    }

    public function updateWalletPin(Request $r)
    {
        $n = Auth::user()->nairaWallet;

        $validator = Validator::make($r->all(), [
            'account_password' => 'required',
            'new_pin' => 'required|string|confirmed|min:4|max:4|different:account_password'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        if (Hash::check($r->account_password, Auth::user()->password) == false) {
            return response()->json([
                'success' => false,
                'message' => 'Your current account does not match with the password you provided. Please try again.',
            ]);
        }

        $n->password = Hash::make($r->new_pin);
        $n->save();

        return response()->json([
            'success' => true,
            'data' => $n,
        ]);
    }
}
