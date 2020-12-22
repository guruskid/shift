<?php

namespace App\Http\Controllers;

use App\BitcoinTransaction;
use App\BitcoinWallet;
use App\Card;
use App\Events\NewTransaction;
use App\Mail\DantownNotification;
use App\Notification;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use RestApis\Blockchain\Constants;

class BitcoinWalletController extends Controller
{
    public function __construct()
    {
        $this->instance = $instance = new \RestApis\Factory(env('BITCOIN_WALLET_API_KEY'));
    }

    public function wallet()
    {
        if (!Auth::user()->bitcoinWallet) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please a bitcoin wallet to continue']);
        }
        return view('newpages.bitcoin-wallet');
    }


    public function create(Request $r)
    {
        $data = $r->validate([
            'wallet_password' => 'required|min:4|confirmed',
        ]);


        $password = Hash::make($data['wallet_password']);

        try {
            $primary_wallet = BitcoinWallet::where(['user_id' => 1, 'primary_wallet_id' => 0])->first();
            $result = $this->instance->walletApiBtcGenerateAddressInWallet()->createHd(Constants::$BTC_TESTNET, $primary_wallet->name, $primary_wallet->password, 1);
            $wallet = new BitcoinWallet();
            $address = $result->payload->addresses[0];
            $wallet->user_id = Auth::user()->id;
            $wallet->path = $address->path;
            $wallet->address = $address->address;
            $wallet->type = 'secondary';
            $wallet->name = Auth::user()->first_name;
            $wallet->password = $password;
            $wallet->balance = 0;
            $wallet->primary_wallet_id = $primary_wallet->id;
            $wallet->save();

            $callback = route('user.wallet-webhook');
            $result = $this->instance->webhookBtcCreateAddressTransaction()->create(Constants::$BTC_TESTNET, $callback, $wallet->address, 6);
        } catch (\Throwable  $e) {
            report($e);
            return back()->with(['error' => 'An error occured, please try again']);
        }
        return back()->with(['success' => 'Wallet created successfully']);
    }

    //sell Bitcoin
    public function trade(Request $r)
    {
        $data = $r->validate([
            'card_id' => 'required|integer',
            'type' => 'required|string',
            'amount' => 'required',
            'amount_paid' => 'required',
            'quantity' => 'required',
        ]);

        if (!Auth::user()->bitcoinWallet) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please a bitcoin wallet to continue']);
        }

        /* if (Auth::user()->transactions()->where('status', 'waiting')->count() >= 3 || Auth::user()->transactions()->where('status', 'in progress')->count() >= 3) {
            return back()->with(['error' => 'You cant initiate a new transaction with more than 3 waiting or processing transactions']);
        } */

        if (Auth::user()->bitcoinWallet->balance < $data['quantity']) {
            return back()->with(['error' => 'Insufficient bitcoin wallet balance to initiate trade']);
        }

        if (!Auth::user()->nairaWallet) {
            return back()->with(['error' => 'Please create a Naira wallet to continue']);
        }

        $online_agent = User::where('role', 888)->where('status', 'active')->inRandomOrder()->first();
        $data['status'] = 'waiting';
        $data['uid'] = uniqid();
        $data['user_email'] = Auth::user()->email;
        $data['user_id'] = Auth::user()->id;
        $data['card'] = Card::find($r->card_id)->name;
        $data['agent_id'] = $online_agent->id;

        $t = Transaction::create($data);

        try {
            broadcast(new NewTransaction($t))->toOthers();
        } catch (\Exception $e) {
            report($e);
        }
        $title = ucwords($t->type) . ' ' . $t->card;
        $body = 'Your order to ' . $t->type . ' ' . $t->card . ' worth of ₦' . number_format($t->amount_paid) . ' has been initiated successfully';
        Notification::create([
            'user_id' => Auth::user()->id,
            'title' => $title,
            'body' => $body,
        ]);
        if (Auth::user()->notificationSetting->trade_email == 1) {
            Mail::to(Auth::user()->email)->send(new DantownNotification($title, $body, 'Transaction History', route('user.transactions')));
        }

        return redirect()->route('user.transactions');
    }

    public function send(Request $r)
    {
        $data = $r->validate([
            'amount' => 'required|numeric',
            'address' => 'required|string',
            'pin' => 'required',
        ]);
        if (!Auth::user()->bitcoinWallet) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please a bitcoin wallet to continue']);
        }

        $user_wallet = Auth::user()->bitcoinWallet;
        $primary_wallet = $user_wallet->primaryWallet;
        $fees = 0.00001; //get from API
        $charge = 0.0000001; // Get from Admin
        $total = $data['amount'] + $fees + $charge;

        //Check password
        if (!Hash::check($data['pin'], $user_wallet->password)) {
            return back()->with(['error' => 'Incorrect bitcoin wallet pin']);
        }

        //Add fees and Check balance
        if ($total > $user_wallet->balance) {
            return back()->with(['error' => 'Insufficient balance']);
        }

        //Debit User
        $user_wallet->balance -= $total;
        $user_wallet->save();

        //Create transaction and set to pending
        $btc_transaction = new BitcoinTransaction();
        $btc_transaction->user_id = Auth::user()->id;
        $btc_transaction->primary_wallet_id = $user_wallet->primaryWallet->id;
        $btc_transaction->wallet_id = $user_wallet->address; //The wallet of the owner user
        $btc_transaction->hash = 'none';
        $btc_transaction->debit = $total;
        $btc_transaction->fee = $fees;
        $btc_transaction->charge = $charge;
        $btc_transaction->previous_balance = $user_wallet->getOriginal('balance');
        $btc_transaction->current_balance = $user_wallet->balance;
        $btc_transaction->transaction_type_id = 21;
        $btc_transaction->counterparty = $data['address'];
        $btc_transaction->narration = 'Sending bitcoin to '.$data['address'];
        $btc_transaction->confirmations = 0;
        $btc_transaction->status = 'pending';
        $btc_transaction->save();

        //Push transaction using try



        //else revert users balance
        $outputs = new \RestApis\Blockchain\BTC\Snippets\Output();
        $input = new \RestApis\Blockchain\BTC\Snippets\Input();
        $outputs->add($data['address'], $total - $charge);
        $input->add($primary_wallet->address, $total - $charge);

        $fee = new \RestApis\Blockchain\BTC\Snippets\Fee();
        $fee->set($fees);

        try {
             //update status and hash if it goes through
            $result = $this->instance->transactionApiBtcNewTransactionHdWallet()->create(Constants::$BTC_TESTNET, $primary_wallet->name, $primary_wallet->password, $input,  $outputs,  $fee);
            $btc_transaction->hash = $result->payload->txid;
            $btc_transaction->status = 'success';
            $btc_transaction->save();

            //send mail
            return back()->with(['success' => 'Bitcoin sent successfully']);
        } catch (\Exception $e) {
            report($e);
            $user_wallet->balance = $user_wallet->getOriginal('balance');
            $user_wallet->save();

            $btc_transaction->status = 'failed';
            $btc_transaction->save();
            //set the transaction status to failed

            return back()->with(['error' => 'An error occured while processing the transaction please confirm the details and try again']);
        }

    }

    public function webhook(Request $request)
    {
        try {
            \Log::info('app.requests', ['request' => $request->all()]);
            /* $btc_transaction = new BitcoinTransaction();
            $btc_transaction->user_id = 19;
            $btc_transaction->primary_wallet_id = 1;
            $btc_transaction->wallet_id = 'so '; //The wallet of the owner user
            $btc_transaction->hash = 'here ';
            $btc_transaction->credit = 2;
            $btc_transaction->fee = 0.00001; //Change to actual fee
            $btc_transaction->charge = 0.0001; //Change to feee from admin
            $btc_transaction->previous_balance = 0;
            $btc_transaction->current_balance = 0;
            $btc_transaction->transaction_type_id = 19;
            $btc_transaction->counterparty = 'Dantown Assets';
            $btc_transaction->narration = 'Approved by '.$request['confirmations'];
            $btc_transaction->confirmations = 99;
            $btc_transaction->save(); */
        } catch (\Exception $e) {
            report($e);
        }

        $btc_txn = BitcoinTransaction::where('hash', $request->txid)->first();
        if ($btc_txn == null) { //New Transaction e.g recieve
            # code...
        } else { //Old transaction like Trade payment and recieve transaction waiting for confirmation
            $btc_txn->confirmations = $request->confirmations;
            /* if confirmations are up to 6 and status is pending, Update users balance and set to success */
        }
    }
}
