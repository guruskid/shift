<?php

namespace App\Http\Controllers\Admin;

use App\NairaTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
                'success' =>true,
                'transactions' => $transactions,
                'user' => $wallet->user,
                'wallet' => $wallet
            ]);
        } else {
            return response()->json([
                'success' =>false,
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
        $transaction_types = TransactionType::whereIn('id', [7, 8])->get();

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
        $r->validate([
            'account_number' => 'required|integer|exists:naira_wallets,account_number',
            'reference' => 'required|string|unique:naira_transactions,reference',
            'wallet_id' => 'required',
            'amount' => 'required',
            'narration' => 'required|string',
            'pin' => 'required',
        ]);

        if (Hash::check($r->pin, Auth::user()->nairaWallet->password) == false) {
            return redirect()->back()->with(['error' => 'Incorrect Pin']);
        }

        $wallet = NairaWallet::findOrFail($r->wallet_id);
        $t = new NairaTransaction();
        $t->reference = $r->reference;
        $t->amount = $r->amount;
        $t->amount_paid = $r->amount;
        $t->previous_balance = $wallet->amount;
        $t->user_id = $wallet->user->id;
        $t->type = 'Naira Wallet';
        $t->transaction_type_id = $r->transaction_type;
        $t->narration = $r->narration;
        $t->charge = 0;
        $t->trans_msg = 'This transaction was authenticated by '.Auth::user()->id.' '.Auth::user()->first_name;
        $t->status = 'pending';

        if ($r->transaction_type == 7) {
            //Top Up
            $wallet->amount += $r->amount;
            $wallet->save();
            $t->current_balance = $wallet->amount;
            $t->cr_user_id = $wallet->user->id;
            $t->dr_user_id = 1;
            $t->cr_wallet_id = $wallet->id;
            $t->dr_wallet_id = 1;
            $t->cr_acct_name = $wallet->account_name;
            $t->dr_acct_name = 'Dantown Assets';
            $t->status = 'success';
            $t->save();

            $title = 'Dantown wallet Credit';
            $type = 'credit';
        }elseif ($r->transaction_type == 8) {
            //Deduction
            $wallet->amount -= $r->amount;
            $wallet->save();
            $t->current_balance = $wallet->amount;
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
        

        
        $msg_body = 'Your Dantown wallet has been '.$type.'ed with N' . $r->amount . ' from ' . $r->originatorname . ' desc: ' . $r->narration;

        $not = Notification::create([
            'user_id' => $wallet->user->id,
            'title' => $title,
            'body' => $msg_body,
        ]);

        if ($wallet->user->notificationSetting->wallet_email == 1) {
            Mail::to($wallet->user->email)->send(new WalletAlert($t, $type));
        }

        $client = new Client();
        $token = env('SMS_TOKEN');
        $to = $wallet->user->phone;
        $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
        $snd_sms = $client->request('GET', $sms_url);
        dd($snd_sms->getBody()->getContents());

        return back()->with(['success' => 'Transaction completed']);
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
