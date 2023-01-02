<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTransaction;
use App\NairaWallet;
use App\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transaction = null;
        $naira_txn = null;
        $request->validate([
            'reference' => 'nullable|exists:transactions,uid'
        ]);

        if ($request->reference) {
            $transaction = Transaction::where('uid', $request->reference)->first();
            $naira_txn = NairaTransaction::where('reference', $request->reference)->first();
            if ($naira_txn) {
                return back()->with(['error' => 'User already credited']);
            }
        }
        return view('admin.resolve_transaction', compact('naira_txn', 'transaction'));
    }

    public function credit(Request $request, Transaction $transaction)
    {
        $request->validate([
            'pin' => 'required',
        ]);

        if (!Hash::check($request->pin, Auth::user()->pin)) {
            return back()->with(['error' => 'Incorrect authorization pin']);
        }

        if (NairaTransaction::where('reference', $transaction->uid)->exists()) {
            return back()->with(['error' => 'User already credited']);
        }

        $systemBalance = NairaWallet::sum('amount');
        $wallet = $transaction->user->nairaWallet;
        $user = $transaction->user;
        $wallet->amount += $transaction->amount_paid;
        $wallet->save();
        $currentSystemBalance = NairaWallet::sum('amount');

        $nt = new NairaTransaction();
        $nt->reference = $transaction->uid;
        $nt->amount = $transaction->amount_paid;
        $nt->user_id = $transaction->user->id;
        $nt->type = 'naira wallet';
        $nt->previous_balance = Auth::user()->nairaWallet->amount - $transaction->amount_paid;
        $nt->current_balance = Auth::user()->nairaWallet->amount;
        $nt->system_previous_balance = $systemBalance;
        $nt->system_current_balance =  $currentSystemBalance;
        $nt->charge = 0;
        $nt->transaction_type_id = 20;
        $nt->dr_wallet_id = 1;
        $nt->cr_wallet_id = $wallet->id;
        $nt->dr_acct_name = 'Dantown';
        $nt->cr_acct_name = $user->first_name . ' ' . $user->last_name;
        $nt->narration = 'Credit for sell transaction with id ' . $transaction->uid;
        $nt->trans_msg = 'This transaction was handled automatically ';
        $nt->cr_user_id = $user->id;
        $nt->dr_user_id = 1;
        $nt->status = 'success';
        $nt->is_flagged = 0;
        $nt->save();

        return back()->with(['success' => 'User credited']);
    }
}
