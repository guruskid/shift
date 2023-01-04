<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Mail\GeneralTemplateOne;
use App\NairaTransaction;
use App\NairaWallet;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LedgerController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(100);
        foreach ($users as $user) {
            $user->ledger = UserController::ledgerBalance($user->id)->getData();
        }

        $debt = User::sum('debt');
        $debtors = User::where('debt', ">", 0)->count();

        $extra_data = [
            [
                "name" => "Debt",
                "value" => "₦". number_format($debt),
                "url" => route('admin.debtors'),
            ]
        ];

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

    public function debtors()
    {
        $debtors = User::where('debt', '>', 0)->get();
        $transactions = NairaTransaction::where('transaction_type_id', 28)->latest()->paginate(100);

        return view('admin.ledger.debts', compact('debtors', 'transactions'));
    }

    public function addDebt(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required',
            'account_id' => 'required',
            'reason' => 'required',
            'frequency' => 'required',
            'when' => 'nullable',
            'pin' => 'required',
        ]);

        if (!Hash::check($request->pin, Auth::user()->pin)) {
           return back()->with(['error' => 'Operation could not be authorized, wrong pin']);
        }
        $date = now();
        if ($request->when) {
            $date = $request->when;
        }

        $user->debtDetails()->create([
            'admin_id' => Auth::user()->id,
            'account_id' => $request->account_id,
            'amount' => $request->amount,
            'frequency' => $request->frequency,
            'reason' => $request->reason,
            'date' => $date,
        ]);

        $user->debt += $request->amount;
        $user->save();

        return back()->with(['success' => 'Debt added successfully']);
    }

    public static function recoverDebt($user_id = 0)
    {
        $user = null;
        if ($user_id == 0) {
            $user = Auth::user();
        }else{
            $user = User::find($user_id);
        }

        $percentage = 0.8;
        $wallet = $user->nairaWallet;
        $recoverable_amount = $percentage * $user->debt;


        if ($wallet->amount < $recoverable_amount) {
            return true;
        }

        $systemBalance = NairaWallet::sum('amount');

        $amount = 0;
        if ($wallet->amount >= $user->debt) {
            //nice users (^_^)
            $amount = $user->debt;
            $wallet->amount -= $user->debt;
            $wallet->save();

            $user->debt = 0;
            $user->save();
        }else{ //Debt higher than balamce
            //We will meet (-_-)
            $amount = $wallet->amount;
            $user->debt -= $wallet->amount;
            $user->save();

            $wallet->amount = 0;
            $wallet->save();
        }

        $reference = \Str::random(5);
        // $narration =
        //create debit txn
        $currentSystemBalance = NairaWallet::sum('amount');
        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $amount;
        $nt->user_id = $user->id;
        $nt->type = 'naira wallet';
        $nt->current_balance = $wallet->amount;
        $nt->previous_balance = $wallet->amount + $amount;
        $nt->system_previous_balance = $systemBalance;
        $nt->system_current_balance =  $currentSystemBalance;
        $nt->charge = 0;
        $nt->transaction_type_id = 28;
        $nt->cr_wallet_id = 1;
        $nt->dr_wallet_id = $wallet->id;
        $nt->cr_acct_name = 'Dantown';
        $nt->dr_acct_name = $user->first_name . ' ' . $user->last_name;
        $nt->narration = 'Debit for debt recovery';
        $nt->trans_msg = 'This transaction was handled automatically, cheers!!';
        $nt->dr_user_id = $user->id;
        $nt->cr_user_id = 1;
        $nt->status = 'success';
        $nt->save();

        //send email and pushN
        $title = 'Debt Recovery';
        $body = 'Your account has been debited due to discrepancy previously noted on your account. Please contact the customer happiness for further details. <br> <br><br> Thank you for Trading with Dantown.';
        $btn_text = '';
        $btn_url = '';

        $name = (Auth::user()->first_name == " ") ? Auth::user()->username : Auth::user()->first_name;
        $name = explode(' ', $name);
        $firstname = ucfirst($name[0]);
        Mail::to(Auth::user()->email)->queue(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));

        return true;
    }

    public static function resolve()
    {
        $systemBalance = NairaWallet::sum('amount');
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

                $currentSystemBalance = NairaWallet::sum('amount');
                $nt = new NairaTransaction();
                $nt->reference =  \Str::random(5) . $user->id;
                $nt->amount = $amt;
                $nt->user_id = $user->id;
                $nt->type = 'naira wallet';
                $nt->current_balance = $user->nairaWallet->amount;
                $nt->previous_balance = $user->nairaWallet->amount;
                $nt->system_previous_balance = $systemBalance;
                $nt->system_current_balance =  $currentSystemBalance;
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

                $currentSystemBalance = NairaWallet::sum('amount');
                $nt = new NairaTransaction();
                $nt->reference =  \Str::random(5) . $user->id;
                $nt->amount = $amt;
                $nt->user_id = $user->id;
                $nt->type = 'naira wallet';
                $nt->current_balance = $user->nairaWallet->amount;
                $nt->previous_balance = $user->nairaWallet->amount;
                $nt->system_previous_balance = $systemBalance;
                $nt->system_current_balance =  $currentSystemBalance;
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
