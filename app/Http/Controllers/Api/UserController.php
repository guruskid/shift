<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTransaction;
use App\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

    public function updatePassword(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'old_password' => 'required',
            'new_password' => 'required|string|confirmed|min:6|different:old_password'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        if (Hash::check($r->old_password, Auth::user()->password) == false) {
            return response()->json([
                'success' => false,
                'message' => 'Your current password does not match with the password you provided. Please try again.',
            ]);
        }

        $user = Auth::user();
        $user->password = Hash::make($r->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    public function updateEmail(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'password' => 'required',
            'new_email' => 'required|email|unique:users,email'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        if (Hash::check($r->password, Auth::user()->password) == false) {
            return response()->json([
                'success' => false,
                'message' => 'Your current password does not match with the password you provided. Please try again.',
            ]);
        }

        Auth::user()->email = $r->new_email;
        Auth::user()->email_verified_at = null;
        Auth::user()->save();
        return response()->json([
            'success' => true,
            'data' => Auth::user(),
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
