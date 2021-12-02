<?php

namespace App\Http\Controllers\Admin;

use App\Account;
use App\Bank;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTrade;
use App\NairaTransaction;
use App\NairaWallet;
use App\User;
use App\PayBridgeAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\GeneralTemplateOne;
use Illuminate\Support\Facades\Mail;

class TradeNairaController extends Controller
{
    public function index()
    {
        $users = User::where('role', 777)->get();
        $users->each(function ($u) {
            $u->success = $u->agentNairaTrades()->where('status', 'success')->count();
            $u->cancelled = $u->agentNairaTrades()->where('status', 'cancelled')->count();
            $u->pending = $u->agentNairaTrades()->whereIn('status', ['pending', 'waiting'])->count();
        });

        return view('admin.trade_naira.index', compact('users'));
    }


    public function transactions()
    {
        if (!Auth::user()->agentLimits) {
            Auth::user()->agentLimits()->create();
        }

        $show_limit = true;
        $transactions = Auth::user()->agentNairaTrades()->paginate(20);
        $banks = Bank::all();
        $account = Auth::user()->accounts->first();

        foreach ($transactions as $t) {
            if ($t->type == 'withdrawal') {
                $a = Account::find($t->account_id);
                $acct = $a['account_name'] . ', ' . $a['bank_name'] . ', ' . $a['account_number'];
                $t->acct_details = $acct;
            }
        }

        return view('admin.trade_naira.transactions', compact('transactions', 'show_limit', 'banks', 'account'));
    }

    public function accounts() {
        $accounts = PayBridgeAccount::all();
        return view('admin.trade_naira.accounts', compact('accounts'));
    }

    public function addAccount(Request $request) {
        $data = $request->except('_token');
        PayBridgeAccount::create($data);
        return redirect()->back()->with(["success" => 'Account added']);
    }

    public function updateAccount(Request $request) {
        $data = $request->except('_token');
        $account = PayBridgeAccount::find($request['id']);
        $account->account_name = $request['account_name'];
        $account->bank_name = $request['bank_name'];
        $account->account_number = $request['account_number'];
        $account->account_type = $request['account_type'];
        $account->status = $request['status'];
        $account->save();
        return redirect()->back()->with(["success" => 'Account Updated']);
    }

    public function updateBankDetails(Request $request)
    {
        $a = Account::find($request->id);
        if ($a->user_id != Auth::user()->id) {
            return redirect()->back()->with(["error" => 'Invalid Operation']);
        }
        $bank = Bank::find($request->bank_id);
        $a->account_name = $request->account_name;
        $a->bank_name = $bank->name;
        $a->account_number = $request->account_number;
        $a->bank_id = $bank->id;
        $a->save();

        return redirect()->back()->with(["success" => 'Details updated']);
    }

    public function agentTransactions(User $user)
    {

        $transactions = $user->agentNairaTrades()->orderBy('created_at', 'desc')->paginate(20);

        foreach ($transactions as $t) {
            if ($t->type == 'withdrawal') {
                $a = Account::find($t->account_id);
                $account = $a['account_name'] . ', ' . $a['bank_name'] . ', ' . $a['account_number'];
                $t->acct_details = $account;
            }

        }

        $show_limit = true;

        return view('admin.trade_naira.transactions', compact('transactions', 'show_limit'));
    }

    public function setLimits(Request $request)
    {
        $data = $request->validate([
            'min' => 'required|min:0',
            'max' => 'required|min:0',
        ]);

        Auth::user()->agentLimits()->update($data);

        return back()->with(['success' => 'Limits uppdated']);
    }

    public function declineTrade(Request $request, NairaTrade $transaction)
    {
        if (!Hash::check($request->pin, Auth::user()->pin)) {
            return back()->with(['error' => 'Incorrect pin']);
        }

        if ($transaction->status != 'waiting') {
            return back()->with(['error' => 'Invalid transaction']);
        }



        $nt = NairaTransaction::where('reference', $transaction->reference)->first();

        // dd($transaction);
        if ($transaction->type == 'withdrawal') {
            # credit the user
            $user_wallet = $nt->user->nairaWallet;
            $user_wallet->amount += $nt->amount;
            $user_wallet->save();

            //Send back the charges
            $transfer_charges_wallet = NairaWallet::where('account_number', 0000000001)->first();
            $transfer_charges_wallet->amount -= $nt->charge;
            $transfer_charges_wallet->save();
        }

        if ($nt) {
            $nt->status = 'failed';
            $nt->save();
        }

        $transaction->status = 'cancelled';
        $transaction->save();

        return back()->with(['success' => 'Transaction cancelled']);
    }

    public function confirm(Request $request, NairaTrade $transaction)
    {   
        if (!Hash::check($request->pin, Auth::user()->pin)) {
            return back()->with(['error' => 'Incorrect pin']);
        }
        
        $user = $transaction->user;
        $user_wallet = $transaction->user->nairaWallet;

        if ($transaction->status != 'waiting') {
            return back()->with(['error' => 'Invalid transaction']);
        }

        $nt = NairaTransaction::where('reference', $transaction->reference)->first();

        if ($nt) {
            $nt->previous_balance = $user_wallet->amount;
            $nt->current_balance = $user_wallet->amount + $transaction->amount;
            $nt->trans_msg = 'This transaction was handled by ' . Auth::user()->first_name;
            $nt->status = 'success';
            $nt->save();
        }

        $user_wallet->amount += $transaction->amount;
        $user_wallet->save();

        $transaction->status = 'success';
        $transaction->save();
        //?mail for deposit successfull
        $title = 'PAY-BRIDGE DEPOSIT SUCCESSFUL
        ';
        $body ="Your naria wallet has been credited with <b>NGN".number_format($transaction->amount)."</b><br><br>
        <b style='color:00070'>
        Reference Number: $transaction->reference<br><br>
        Date: ".date("Y-m-d; h:ia")."<br><br>
        Account Balance: NGN ".number_format($user_wallet->amount)." 
        </b>";


        $btn_text = '';
        $btn_url = '';

        $name = ($user->first_name == " ") ? $user->username : $user->first_name;
        $name = explode(' ', $name);       
        $firstname = ucfirst($name[0]);
        Mail::to($user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));

        return back()->with(['success' => 'Transaction confirmed']);
    }

    public function confirmSell(Request $request, NairaTrade $transaction)
    {
        if (!Hash::check($request->pin, Auth::user()->pin)) {
            return back()->with(['error' => 'Incorrect pin']);
        }
        
        $user = $transaction->user;
        $user_wallet = $transaction->user->nairaWallet;
        $Agent = User::find($transaction->agent_id);

        if ($transaction->status != 'waiting') {
            return back()->with(['error' => 'Invalid transaction']);
        }

        $nt = NairaTransaction::where('reference', $transaction->reference)->first();

        if ($nt) {
            $nt->trans_msg = 'This transaction was handled by ' . Auth::user()->first_name;
            $nt->status = 'success';
            $nt->save();
        }

        $transaction->status = 'success';
        $transaction->save();
        //? mail for withdrawal
        $title = 'PAY-BRIDGE WITHDRAWAL(SUCCESSFUL)
        ';
        $body ="You have successfully withdrawn the sum of ".number_format($transaction->amount)." to ".$user->accounts->first()->account_name."<br>
        (".$user->accounts->first()->bank_name.", ".$user->accounts->first()->account_number." ). <br><br>
        <b style='color:00070'>
            Pay-bridge Agent: ".$Agent->first_name."<br><br>
            Bank Name: ".$Agent->accounts->first()->bank_name."<br><br>
            Status: <span style='color:green'>success</span><br><br>
            Reference Number: $transaction->reference <br><br>
            Date: ".date("Y-m-d; h:ia")."<br><br>
            Account Balance: ".number_format($user_wallet->amount)."
        </b>
        ";


        $btn_text = '';
        $btn_url = '';

        $name = ($user->first_name == " ") ? $user->username : $user->first_name;
        $name = explode(' ', $name);       
        $firstname = ucfirst($name[0]);
        Mail::to($user->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));

        return back()->with(['success' => 'Transaction confirmed']);
    }

    public function topup(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required',
            'pin' => 'required',
            'user_id' => 'required'
        ]);

        $user = User::find($data['user_id']);
        $user_wallet = $user->nairaWallet;

        if (!in_array($user->role, [777])) {
            return back()->with(['error' => 'Invalid user account']);
        }

        if (!Hash::check($data['pin'], Auth::user()->nairaWallet->password)) {
            return back()->with(['error' => 'Incorrect pin']);
        }


        //create txn
        $prev_bal = $user_wallet->amount;
        $user_wallet->amount += $data['amount'];
        $user_wallet->save();

        $nt = new NairaTransaction();
        $nt->reference = time();
        $nt->amount = $data['amount'];
        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $user_wallet->amount;
        $nt->charge = 0;
        $nt->transfer_charge = 0;
        $nt->sms_charge = 0;
        $nt->amount_paid = $data['amount'];
        $nt->transaction_type_id = 7;
        $nt->user_id = $user->id;
        $nt->type = 'naira wallet';
        $nt->dr_user_id = Auth::user()->id;
        $nt->cr_user_id = $user->id;
        $nt->dr_acct_name = Auth::user()->first_name;
        $nt->cr_acct_name = $user->first_name;
        $nt->narration = 'Dantown topup';
        $nt->trans_msg = 'Inhouse shit';
        $nt->status = 'success';
        $nt->save();


        return back()->with(['success' => 'Account credited successfully']);
    }

    public function deduct(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required',
            'pin' => 'required',
            'user_id' => 'required'
        ]);

        $user = User::find($data['user_id']);
        $user_wallet = $user->nairaWallet;

        if (!in_array($user->role, [777])) {
            return back()->with(['error' => 'Invalid user account']);
        }

        if (!Hash::check($data['pin'], Auth::user()->nairaWallet->password)) {
            return back()->with(['error' => 'Incorrect pin']);
        }


        //create txn
        $prev_bal = $user_wallet->amount;
        $user_wallet->amount -= $data['amount'];
        $user_wallet->save();

        $nt = new NairaTransaction();
        $nt->reference = time();
        $nt->amount = $data['amount'];
        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $user_wallet->amount;
        $nt->charge = 0;
        $nt->transfer_charge = 0;
        $nt->sms_charge = 0;
        $nt->amount_paid = $data['amount'];
        $nt->transaction_type_id = 8;
        $nt->user_id = $user->id;
        $nt->type = 'naira wallet';
        $nt->dr_user_id = $user->id;
        $nt->cr_user_id = Auth::user()->id;
        $nt->dr_wallet_id = $user_wallet->id;
        $nt->dr_acct_name = $user->first_name;
        $nt->narration = 'Dantown duduction';
        $nt->trans_msg = 'Inhouse shit';
        $nt->status = 'success';
        $nt->save();

        return back()->with(['success' => 'Account debited successfully']);
    }
}
