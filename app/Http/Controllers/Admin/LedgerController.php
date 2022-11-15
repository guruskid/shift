<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\NairaTransaction;
use App\User;

class LedgerController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(100);
        foreach ($users as $user) {
            $user->ledger = UserController::ledgerBalance($user->id)->getData();
        }

        $extra_data = [];

        return view('admin.ledger.index', compact(['users', 'extra_data']));
    }

    public function negative()
    {

        $users = collect();
        User::latest()->chunk(100, function ($us) use ($users) {
            foreach ($us as $user) {
                $user->ledger = UserController::ledgerBalance($user->id)->getData();
                if ($user->ledger->balance < 0) {
                    $users->push($user);
                }
            }
        });

        $users->paginate(200);

        // $us =  User::paginate(200);
        // foreach ($us as $user) {
        //     $user->ledger = UserController::ledgerBalance($user->id)->getData();
        //     if ($user->ledger->balance < 0) {
        //         $users->push($user);
        //     }
        // }

        $extra_data = [
            [
                'name' => "Total count",
                'value' => $users->count(),
            ]
        ];

        return view('admin.ledger.index', compact(['users', 'extra_data']));
    }

    public function resolveTransactions()
    {
        $transactions = NairaTransaction::where('transaction_type_id', 27)->orWhere('transaction_type_id', 26)->orderBy('id', 'desc')->paginate(1000);

        return view('admin.ledger.resolves', compact('transactions'));
    }

    public static function resolve()
    {
        $users = User::where('ledger_resolved', false)->take(100)->get();
        foreach ($users as $user) {
            $ledger_bal = UserController::ledgerBalance($user->id)->getData()->balance;
            if (!$user->nairaWallet) {
                $user->ledger_resolved = true;
                $user->save();
                continue;
            }
            $r_amt = "";
            if ($ledger_bal < $user->nairaWallet->amount) {
                //credit txn
                $user->ledger_resolved = true;
                $user->save();

                $amt = $user->nairaWallet->amount - $ledger_bal;
                $r_amt = "+" . $amt;

                $nt = new NairaTransaction();
                $nt->reference =  \Str::random(5) . $user->id;
                $nt->amount = $amt;
                $nt->user_id = $user->id;
                $nt->type = 'naira wallet';
                $nt->current_balance = $user->nairaWallet->amount;
                $nt->previous_balance = $user->nairaWallet->amount;
                $nt->charge = 0;
                $nt->transaction_type_id = 27;
                $nt->dr_wallet_id = 1;
                $nt->cr_wallet_id = $user->nairaWallet->id;
                $nt->dr_acct_name = 'Dantown';
                $nt->cr_acct_name = $user->first_name . ' ' . $user->last_name;
                $nt->narration = 'Credit for ledger balancing';
                $nt->trans_msg = 'This transaction was handled automatically ';
                $nt->cr_user_id = $user->id;
                $nt->dr_user_id = 1;
                $nt->status = 'success';
                $nt->created_at = "2020-08-17 15:21:38";
                $nt->save();
            } else if ($ledger_bal > $user->nairaWallet->amount) {
                // debit txn
                $user->ledger_resolved = true;
                $user->save();

                $amt = $ledger_bal - $user->nairaWallet->amount;
                $r_amt = "-" . $amt;


                $nt = new NairaTransaction();
                $nt->reference =  \Str::random(5) . $user->id;
                $nt->amount = $amt;
                $nt->user_id = $user->id;
                $nt->type = 'naira wallet';
                $nt->current_balance = $user->nairaWallet->amount;
                $nt->previous_balance = $user->nairaWallet->amount;
                $nt->charge = 0;
                $nt->transaction_type_id = 26;
                $nt->cr_wallet_id = 1;
                $nt->dr_wallet_id = $user->nairaWallet->id;
                $nt->cr_acct_name = 'Dantown';
                $nt->dr_acct_name = $user->first_name . ' ' . $user->last_name;
                $nt->narration = 'Debit for ledger balancing';
                $nt->trans_msg = 'This transaction was handled automatically ';
                $nt->dr_user_id = $user->id;
                $nt->cr_user_id = 1;
                $nt->status = 'success';
                $nt->created_at = "2020-08-17 15:21:38";
                $nt->save();
            }

            $user->ledger_resolved = true;
            $user->save();
        }
    }
}
