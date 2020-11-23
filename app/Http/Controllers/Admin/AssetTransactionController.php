<?php

namespace App\Http\Controllers\Admin;

use App\BitcoinTransaction;
use App\BitcoinWallet;
use App\Card;
use App\Events\CustomNotification;
use App\Events\TransactionUpdated;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\DantownNotification;
use App\Mail\WalletAlert;
use App\NairaTransaction;
use App\NairaWallet;
use App\Notification;
use App\Transaction;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use RestApis\Blockchain\Constants;

class AssetTransactionController extends Controller
{
    public function __construct()
    {
        $this->instance = $instance = new \RestApis\Factory(env('BITCOIN_WALLET_API_KEY'));
    }

    public function editTransaction(Request $r)
    {
        $t = Transaction::find($r->id);
        $t->card = Card::find($r->card_id)->name;
        $t->card_id = $r->card_id;
        $t->type = $r->trade_type;
        $t->country = $r->country;
        $t->amount = $r->amount;
        $t->amount_paid = $r->amount_paid;
        $t->status = $r->status;
        $t->last_edited = Auth::user()->email;
        $t->save();

        $user = $t->user; //Users should see success when a transaction is approved
        if ($t->status == 'approved') {
            $t->stats = 'success';
        } else {
            $t->stats = $t->status;
        }

        $body = 'The status of your transaction to  ' . $t->type . ' ' . $t->card .
            ' worth of ₦' . number_format($t->amount_paid) . ' has been updated to ' . $t->stats;
        $title = 'Transaction update';
        $not = Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
        ]);

        broadcast(new TransactionUpdated($user))->toOthers();
        if ($t->status == 'success' && $t->user->notificationSetting->trade_email == 1) {
            $title = 'Transaction Successful';
            Mail::to($user->email)->send(new DantownNotification($title, $body, 'Go to Wallet', route('user.naira-wallet')));
        }

        /* Broadcast to all active accountants if approved for payment */
        if ($r->status == 'approved') {
            $accountants = User::where(['role' => 777, 'status' => 'active'])->orWhere(['role' => 889, 'status' => 'active'])->get();
            $message = 'A new transaction has been approved for payment ' . $t->id;
            foreach ($accountants as $acct) {
                broadcast(new CustomNotification($acct, $message))->toOthers();
            }
        }


        return redirect()->back()->with(['success' => 'Transaction updated']);
    }

    public function payTransaction(Request $r)
    {
        $r->validate([
            'id' => 'required',
            'pin' => 'required',
        ]);

        $n = NairaWallet::find(1); /* Admin general Wallet */
        $t = Transaction::find($r->id);
        $user_wallet = $t->user->nairaWallet;

        if (Hash::check($r->pin, $n->password) == false) {
            return back()->with(['error' => 'Wrong wallet pin']);
        }

        if ($t->status == 'success') {
            return back()->with(['error' => 'Transaction already completed']);
        }

        if (!$user_wallet) {
            return back()->with(['error' => 'User wallet not found']);
        }

        $amount = $t->amount_paid;
        $reference = \Str::random(2) . '-' . $t->id;

        $prev_bal = $user_wallet->amount;
        if ($t->type == 'sell') {
            $user_wallet->amount += $amount;
        } else {
            $user_wallet->amount -= $amount;
        }

        $user_wallet->save();

        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $amount;
        $nt->amount_paid = $amount;
        $nt->user_id = $t->user->id;
        $nt->type = 'naira wallet';

        $nt->previous_balance = $prev_bal;
        $nt->current_balance = $user_wallet->amount;
        $nt->charge = 0;

        if ($t->type == 'sell') {
            $nt->transaction_type_id = 4; // Sell credit User
            $nt->dr_wallet_id = $n->id;
            $nt->cr_wallet_id = $user_wallet->id;
            $nt->dr_acct_name = 'Dantown';
            $nt->cr_acct_name = $t->user->first_name;
            $nt->cr_user_id = $t->user->id;
            $nt->dr_user_id = 1;
            $type = 'Credit';
        } else {
            $nt->transaction_type_id = 5; // Buy debit user
            $nt->cr_wallet_id = $n->id;
            $nt->dr_wallet_id = $user_wallet->id;
            $nt->cr_acct_name = 'Dantown';
            $nt->dr_acct_name = $t->user->first_name;
            $nt->dr_user_id = $t->user->id;
            $nt->cr_user_id = 1;
            $type = 'Credit';
        }


        $nt->narration = 'Payment for transaction with id ' . $t->uid;
        $nt->trans_msg = 'This transaction was approved by ' . Auth::user()->email;
        $nt->status = 'success';
        $nt->save();

        /* Update Transaction satus */
        $t->status = 'success';
        $t->accountant_id = Auth::user()->id;
        $t->save();

        $title = 'Dantown wallet ' . $type;
        $msg_body = 'Your Dantown wallet has been ' . $type . 'ed with N' . $amount . ' desc: Payment for transaction with id ' . $t->uid;
        /* Send notification */
        $not = Notification::create([
            'user_id' => $t->user->id,
            'title' => $title,
            'body' => $msg_body
        ]);

        if ($t->user->notificationSetting->wallet_email == 1) {
            Mail::to($t->user->email)->send(new WalletAlert($nt, 'credit'));
        }

        $client = new Client();
        $token = env('SMS_TOKEN');
        $to = $t->user->phone;
        $sms_url = 'https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=' . $token . '&from=Dantown&to=' . $to . '&body=' . $msg_body . '&dnd=2';
        /* $snd_sms = $client->request('GET', $sms_url); */

        return back()->with(['success' => 'Transfer made successfully']);
    }

    public function payBtcTransaction(Request $request)
    {
        $data =  $request->validate([
            'primary_wallet_id' => 'required|integer',
            'primary_wallet_pin' => 'required',
            'wallet_pin' => 'required',
            'transaction_id' => 'required',
        ]);

        /* Confirm Primary Wallet Pasasword */
        $primary_wallet = BitcoinWallet::findOrFail($data['primary_wallet_id']);
        $transaction = Transaction::find($data['transaction_id']);
        $btc_txn_type = 0;
        $user_naira_wallet = $transaction->user->nairaWallet;
        $user_btc_wallet = $transaction->user->bitcoinWallet;
        $user = $transaction->user;

        if (!Hash::check($data['primary_wallet_pin'], $primary_wallet->password)) {
            return redirect()->back()->with(['error' => 'Incorrect primary wallet password']);
        }

        /* Cross Check Balance */
        if ($primary_wallet->balance < $transaction->quantity) {
            return redirect()->back()->with(['error' => 'Insufficient primary wallet fund']);
        }

        /* Confirm User has wallet */
        if (!$transaction->user->bitcoinWallet) {
            return redirect()->back()->with(['error' => 'No bitcoin wallet associated with this account']);
        }

        /* Update User Balance */

        if ($transaction->type == 'buy') {
            $btc_txn_type = 19;
            /* Deduct cost from user naira wallet and create new naira wallet transaction */
            $user_naira_wallet->amount -= $transaction->amount_paid;
            $user_naira_wallet->save();

            $reference = \Str::random(2) . '-' . $transaction->id;
            $n = NairaWallet::find(1);

            $nt = new NairaTransaction();
            $nt->reference = $reference;
            $nt->amount = $transaction->amount_paid;
            $nt->user_id = $user->id;
            $nt->type = 'naira wallet';

            $nt->previous_balance = $user_naira_wallet->getOriginal('amount');
            $nt->current_balance = $user_naira_wallet->amount;
            $nt->charge = 0;
            $nt->transaction_type_id = 5;

            $nt->cr_wallet_id = $n->id;
            $nt->dr_wallet_id = $user_naira_wallet->id;
            $nt->cr_acct_name = 'Dantown';
            $nt->dr_acct_name = $user->first_name . ' ' . $user->last_name;
            $nt->narration = 'Debit for buy transaction with id ' . $transaction->uid;
            $nt->trans_msg = 'This transaction was handled by ' . Auth::user()->first_name;
            $nt->dr_user_id = $user->id;
            $nt->cr_user_id = 1;
            $nt->status = 'success';
            $nt->save();

            $user_btc_wallet->balance += $transaction->quantity;
        } elseif ($transaction->type == 'sell') {
            $btc_txn_type = 20;
            if ($user_btc_wallet < $transaction->quantity) {
                return redirect()->back()->with(['error' => 'Insufficient user wallet fund']);
            }
            $user_btc_wallet->balance -= $transaction->quantity;
        }

        $user_btc_wallet->save();


        /* Send Transaction to Blockchain */
        $outputs = new \RestApis\Blockchain\BTC\Snippets\Output();
        $outputs->add($user_btc_wallet->address, $transaction->quantity);

        $fee = new \RestApis\Blockchain\BTC\Snippets\Fee();
        $fee->set(0.00001);

        try {
            $result = $this->instance->transactionApiBtcNewTransactionHdWallet()->create(Constants::$BTC_TESTNET, $primary_wallet->name, $primary_wallet->password, $outputs, $fee);
        } catch (\Exception $e) {
            report($e);
            $user_btc_wallet->balance = $user_btc_wallet->getOriginal('balance');
            $user_btc_wallet->save();

            return back()->with(['error' => 'An error occured while processing the transaction please confirm the details and try again']);
        }


        $btc_transaction = new BitcoinTransaction();
        $btc_transaction->user_id = $transaction->user->id;
        $btc_transaction->primary_wallet_id = $primary_wallet->id;
        $btc_transaction->wallet_id = $user_btc_wallet->address; //The wallet of the owner user
        $btc_transaction->hash = $result->payload->txid;
        if ($transaction->type == 'buy') {
            $btc_transaction->debit = $transaction->quantity;
        } elseif ($transaction->type == 'sell') {
            $btc_transaction->credit = $transaction->quantity;
        }
        $btc_transaction->fee = 0.00001; //Change to actual fee
        $btc_transaction->charge = 0.0001; //Change to feee from admin
        $btc_transaction->previous_balance = $user_btc_wallet->getOriginal('balance');
        $btc_transaction->current_balance = $user_btc_wallet->balance;
        $btc_transaction->transaction_type_id = $btc_txn_type;
        $btc_transaction->counterparty = 'Dantown Assets';
        $btc_transaction->narration = 'Approved by' . Auth::user()->id;
        $btc_transaction->confirmations = 0;
        $btc_transaction->save();

        $transaction->status = 'success';
        $transaction->save();

        return back()->with(['success' => 'Bitcoin sent successfully ']);
        /* Redirect User Back to where he came from */
    }


    
}
