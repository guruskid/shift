<?php

namespace App\Http\Controllers\Admin;

use App\Bank;
use App\FlaggedTransactions;
use App\NairaTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\FlaggedTransactionsController;
use App\Mail\WalletAlert;
use App\NairaWallet;
use App\Notification;
use App\TransactionType;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class NairaTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getWalletDetails($account_number)
    {
        $wallet = NairaWallet::where('account_number', $account_number)->first();
        if ($wallet) {
            $transactions = NairaTransaction::where('cr_user_id', $wallet->user->id)->orWhere('dr_user_id', $wallet->user->id)->latest()->with('transactionType')->get();
            return response()->json([
                'success' => true,
                'transactions' => $transactions,
                'user' => $wallet->user,
                'wallet' => $wallet
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Wallet details not found, please try again'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $transaction_types = TransactionType::whereIn('id', [1, 7, 8])->get();

        return view('admin.new_naira_transaction', compact(['transaction_types']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $r)
    {
        // return back()->with(['error' => 'Incorrect details']);
        $r->validate([
            'email' => 'required|email|exists:users',
            'amount' => 'required',
            'narration' => 'required|string',
            'pin' => 'required',
        ]);

        if (Hash::check($r->pin, Auth::user()->pin) == false) {
            return redirect()->back()->with(['error' => 'Incorrect Pin']);
        }
        $user = User::where('email', $r->email)->first();

        $narration = NULL;
        $isFlagged = 0;
        $dailyMonthlyFlaggedData = FlaggedTransactionsController::userDailyMonthlyManualDeposit($user, $r->amount);

        $dailyFlaggedData = $dailyMonthlyFlaggedData['daily'];
        $monthlyFlaggedData = $dailyMonthlyFlaggedData['monthly'];

        if($monthlyFlaggedData >= 1000000){
            $isFlagged = 1;
            $narration = "Manual Deposit for the month is greater than 1 million";
        }

        if($dailyFlaggedData >= 1000000){
            $isFlagged = 1;
            $narration = "Manual Deposit for the day is greater than 1 million";
        }

        if($isFlagged == 1){
            $lastTranxAmount = FlaggedTransactionsController::getLastTransaction($user->id);
        }
        

        $systemBalance = NairaWallet::sum('amount');
        $wallet = $user->nairaWallet;

        $t = new NairaTransaction();
        $t->reference = uniqid();
        $t->amount = $r->amount;
        $t->amount_paid = $r->amount;
        $t->previous_balance = $wallet->amount;
        $t->user_id = $wallet->user->id;
        $t->type = 'Naira Wallet';
        $t->transaction_type_id = $r->transaction_type;
        $t->narration = $r->narration;
        $t->charge = 0;
        $t->trans_msg = 'This transaction was authenticated by ' . Auth::user()->id . ' ' . Auth::user()->first_name;
        $t->is_manual = 1;
        $t->is_flagged = $isFlagged;

        if ($r->transaction_type == 1 ) {
            //Deposit
            $wallet->amount += $r->amount;
            $wallet->save();

            $currentSystemBalance = NairaWallet::sum('amount');

            $t->current_balance = $wallet->amount;
            $t->system_previous_balance = $systemBalance;
            $t->system_current_balance =  $currentSystemBalance;
            $t->cr_user_id = $wallet->user->id;
            $t->dr_user_id = 1;
            $t->cr_wallet_id = $wallet->id;
            $t->dr_wallet_id = 1;
            $t->cr_acct_name = $wallet->account_name;
            $t->dr_acct_name = 'Dantown Assets';
            $t->status = 'success';
            $t->save();

            if($t->is_flagged == 1){
                $agent_id = Auth::user()->id;
                $type = 'Manual Deposit';
                $flaggedTranx =  new FlaggedTransactions();
                $flaggedTranx->type = $type;
                $flaggedTranx->user_id = $user->id;
                $flaggedTranx->transaction_id = $t->id;
                $flaggedTranx->reference_id = $t->reference;
                $flaggedTranx->previousTransactionAmount = $lastTranxAmount;
                $flaggedTranx->accountant_id = $agent_id;
                $flaggedTranx->narration = $narration;
                $flaggedTranx->save();
            }

            $title = 'Dantown wallet Credit';
            $type = 'credit';
        } elseif ($r->transaction_type == 8) {
            //Deduction
            $wallet->amount -= $r->amount;
            $wallet->save();
            $currentSystemBalance = NairaWallet::sum('amount');
            $t->current_balance = $wallet->amount;
            $t->system_previous_balance = $systemBalance;
            $t->system_current_balance =  $currentSystemBalance;
            $t->dr_user_id = $wallet->user->id;
            $t->cr_user_id = 1;
            $t->dr_wallet_id = $wallet->id;
            $t->cr_wallet_id = 1;
            $t->dr_acct_name = $wallet->account_name;
            $t->cr_acct_name = 'Dantown Assets';
            $t->status = 'success';
            $t->save();

            $title = 'Dantown wallet Debit';
            $type = 'debit';
        }



        $msg_body = 'Your Dantown wallet has been ' . $type . 'ed with N' . $r->amount;

        $not = Notification::create([
            'user_id' => $wallet->user->id,
            'title' => $title,
            'body' => $msg_body,
        ]);


        return back()->with(['success' => 'Transaction completed']);
    }

    public function profits()
    {
        $transfer_charge = NairaWallet::where('account_number', 0000000001)->first()->amount;
        $sms_charge = NairaWallet::where('account_number', 0000000002)->first()->amount;
        $charges = $transfer_charge + $sms_charge;
        $banks = Bank::orderBy('name', 'asc')->get();
        $ref = \Str::random(2) . time();
        $admin_accounts = NairaWallet::where('user_id', 1)->latest()->get();

        return view('admin.profits', compact(['transfer_charge', 'sms_charge', 'charges', 'banks', 'ref', 'admin_accounts']));
    }

    public function sendCharges(Request $r)
    {
        $r->validate([
            'bank_code' => 'required',
            'acct_num' => 'required',
            'acct_name' => 'required',
            'pin' => 'required',
            'admin_account' => 'required|integer',
            'ref' => 'required|unique:naira_transactions,reference',
        ]);
        $systemBalance = NairaWallet::sum('amount');
        $admin_account = NairaWallet::where(['user_id' => 1, 'id' => $r->admin_account])->firstOrFail();
        $n = Auth::user()->nairaWallet;

        if (Hash::check($r->pin, $n->password) == false) {
            return redirect()->back()->with(['error' => 'Wrong wallet pin entered']);
        }

        $reference = $r->ref;
        $bank_name = Bank::where('code', $r->bank_code)->first()->name;
        $acct_name = $r->acct_name;
        $acct_num = $r->acct_num;
        $bank_code = $r->bank_code;
        $tid = 2;

        $amount = $admin_account->amount;
        $amount_paid = $admin_account->amount;

        $prev_bal = $admin_account->amount;
        $admin_account->amount -= $amount;
        $admin_account->save();

        $currentSystemBalance = NairaWallet::sum('amount');
        $msg = 'Transaction initiated';
        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $amount;

        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $admin_account->amount;
        $nt->system_previous_balance = $systemBalance;
        $nt->system_current_balance =  $currentSystemBalance;
        $nt->charge = 0;
        $nt->transfer_charge = 0;
        $nt->sms_charge = 0;
        $nt->amount_paid = $amount_paid;
        $nt->transaction_type_id = $tid;

        $nt->user_id = 1;
        $nt->type = 'naira wallet';
        $nt->dr_user_id = 1;
        $nt->dr_wallet_id = $admin_account->id;
        $nt->dr_acct_name =  $admin_account->name;
        $nt->cr_acct_name = $acct_name . ' ' . $acct_num . " " . $bank_name;
        $nt->narration = 'Charges withdrawal Authorised by '. Auth::user()->name;
        $nt->trans_msg = $msg;
        $nt->status = 'pending';
        $nt->save();


        $client = new Client();
        $url = env('RUBBIES_API') . "/fundtransfer";

        $response = $client->request('POST', $url, [
            'json' => [
                "reference" => $reference,
                "amount" => $amount,
                "narration" => $nt->narration,
                "craccountname" => $acct_name,
                "bankname" => $bank_name,
                "draccountname" => Auth::user()->first_name,
                "craccount" => $acct_num,
                "bankcode" => $bank_code
            ],
            'headers' => [
                'authorization' => env('RUBBIES_SECRET_KEY'),
            ],
        ]);
        $body = json_decode($response->getBody()->getContents());
        if ($body->responsecode == 00) {
            $nt->status = 'success';
            $nt->save();

            $title = 'Dantown wallet Debit';
            $msg_body = 'Your Dantown wallet has been debited with N' . $amount . ' for transfer to ' . $body->craccountname . ' desc: ' . $nt->narration;
            $not = Notification::create([
                'user_id' => 1,
                'title' => $title,
                'body' => $msg_body,
            ]);
            return back()->with(['success' => 'Transfer made successfully']);
        } else {
            return back()->with(['error' => 'Oops! ' . $body->responsemessage]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\NairaTransaction  $nairaTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(NairaTransaction $nairaTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\NairaTransaction  $nairaTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(NairaTransaction $nairaTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NairaTransaction  $nairaTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NairaTransaction $nairaTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NairaTransaction  $nairaTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(NairaTransaction $nairaTransaction)
    {
        //
    }
}
