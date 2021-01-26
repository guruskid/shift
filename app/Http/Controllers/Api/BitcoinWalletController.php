<?php

namespace App\Http\Controllers\Api;

use App\BitcoinTransaction;
use App\BitcoinWallet;
use App\Card;
use App\CardCurrency;
use App\Events\NewTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notification;
use App\Setting;
use App\Transaction;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RestApis\Blockchain\Constants;

class BitcoinWalletController extends Controller
{
    public function __construct()
    {
        $this->instance = new \RestApis\Factory(env('BITCOIN_WALLET_API_KEY'));
    }

    public function create(Request $r)
    {
        $data = $r->validate([
            'wallet_password' => 'required|min:4|confirmed',
        ]);

        if (Auth::user()->bitcoinWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'A bitcoin wallet exists for this account'
            ]);
        }

        $password = Hash::make($data['wallet_password']);

        try {
            $primary_wallet = BitcoinWallet::where(['user_id' => 1, 'primary_wallet_id' => 0])->first();
            $result = $this->instance->walletApiBtcGenerateAddressInWallet()->createHd(Constants::$BTC_MAINNET, $primary_wallet->name, $primary_wallet->password, 1);
            $wallet = new BitcoinWallet();
            $address = $result->payload->addresses[0];
            $wallet->user_id = Auth::user()->id;
            $wallet->path = $address->path;
            $wallet->address = $address->address;
            $wallet->type = 'secondary';
            $wallet->name = Auth::user()->first_name;
            $wallet->password = $password;
            $wallet->balance = 0.00000000;
            $wallet->primary_wallet_id = $primary_wallet->id;
            $wallet->save();

            $callback = route('user.wallet-webhook');
            $result = $this->instance->webhookBtcCreateAddressTransaction()->create(Constants::$BTC_MAINNET, $callback, $wallet->address, 6);
        } catch (\Throwable  $e) {
            report($e);
            return response()->json([
                'success' => false,
                'msg' => 'An error occured while creating a Bitcoin wallet.'
            ]);
        }
        return response()->json([
            'success' => true,
        ]);
    }

    public function balance()
    {
        if (!Auth::user()->bitcoinWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'No bitcoin wallet exists for this account'
            ]);
        }
        $card = Card::find(102);
        $rates = $card->currency->first();
        $res = json_decode(file_get_contents("https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd"));
        $btc_rate = $res->bitcoin->usd;
        $btc_wallet_bal  = Auth::user()->bitcoinWallet->balance ?? 0;
        $btc_usd = $btc_wallet_bal  * $btc_rate;

        $sell =  CardCurrency::where(['card_id' => 102, 'currency_id' => $rates->id, 'buy_sell' => 2])->first()->paymentMediums()->first();
        $rates->sell = json_decode($sell->pivot->payment_range_settings);

        $btc_ngn = $btc_usd * $rates->sell[0]->rate;

        return response()->json([
            'success' => true,
            'btc_wallet' => Auth::user()->bitcoinWallet->address,
            'btc_value' => number_format((float)$btc_wallet_bal, 8),
            'ngn_value' => (int)$btc_ngn,
            'usd_value' => $btc_usd
        ]);
    }

    public function transactions()
    {
        $transactions = Auth::user()->bitcoinWallet->transactions;

        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);
    }

    public function sendBtcCharges()
    {
        $fees_req = $this->instance->transactionApiBtcNewTransactionFee()->get(Constants::$BTC_MAINNET);
        $fees = $fees_req->payload->recommended;
        $charge = Setting::where('name', 'bitcoin_charge')->first();
        if (!$charge) {
            $charge = 0;
        } else {
            $charge = Setting::where('name', 'bitcoin_charge')->first()->value;
        }
        $total_fees = $fees + $charge;
        $res = json_decode(file_get_contents("https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd"));
        $btc_rate = $res->bitcoin->usd;
        //$btc_usd = Auth::user()->bitcoinWallet->balance * $btc_rate;

        return response()->json([
            'success' => true,
            'send_fee' => $total_fees,
            'btc_to_usd' => $btc_rate,
        ]);
    }

    public function send(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'amount' => 'required|numeric',
            'address' => 'required|string',
            'pin' => 'required',
            'fees' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }
        if (!Auth::user()->bitcoinWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please create a Bitcoin wallet to continue'
            ]);
        }

        $user_wallet = Auth::user()->bitcoinWallet;
        $primary_wallet = $user_wallet->primaryWallet;
        $charge_wallet = BitcoinWallet::where('name', 'bitcoin charges')->first();
        $fees = $r->fees;
        $charge = Setting::where('name', 'bitcoin_charge')->first()->value;; // Get from Admin
        $total = $r->amount + $fees + $charge;

        //Check password
        if (!Hash::check($r->pin, $user_wallet->password)) {
            return response()->json([
                'success' => false,
                'msg' => 'Incorrect Bitcoin wallet password'
            ]);
        }

        //Add fees and Check balance
        if ($total > $user_wallet->balance) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient Bitcoin Balance'
            ]);
        }

        //Debit User
        $old_balance = $user_wallet->balance;
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
        $btc_transaction->previous_balance = $old_balance;
        $btc_transaction->current_balance = $user_wallet->balance;
        $btc_transaction->transaction_type_id = 21;
        $btc_transaction->counterparty = $r->address;
        $btc_transaction->narration = 'Sending bitcoin to ' . $r->address;
        $btc_transaction->confirmations = 0;
        $btc_transaction->status = 'pending';
        $btc_transaction->save();

        //Push transaction using try



        //else revert users balance
        $send_total = number_format((float)$r->amount, 8);
        $outputs = new \RestApis\Blockchain\BTC\Snippets\Output();
        $input = new \RestApis\Blockchain\BTC\Snippets\Input();
        $outputs->add($r->address, $send_total);
        $input->add($primary_wallet->address, $send_total);

        $fee = new \RestApis\Blockchain\BTC\Snippets\Fee();
        $fee->set($fees);

        try {
            //update status and hash if it goes through
            $result = $this->instance->transactionApiBtcNewTransactionHdWallet()->create(Constants::$BTC_MAINNET, $primary_wallet->name, $primary_wallet->password,  $outputs,  $fee);
            $btc_transaction->hash = $result->payload->txid;
            $btc_transaction->status = 'success';
            $btc_transaction->save();

            $charge_wallet->balance += $charge;
            $charge_wallet->save();

            //send mail
            return response()->json([
                'success' => true,
                'msg' => 'Bitcoin sent successfully'
            ]);
        } catch (\Exception $e) {
            report($e);
            $user_wallet->balance = $old_balance;
            $user_wallet->save();

            $btc_transaction->status = 'failed';
            $btc_transaction->save();
            //set the transaction status to failed

            return response()->json([
                'success' => false,
                'msg' => 'An error occured while processing the transaction please confirm the details and try again'
            ]);
        }
    }

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

        $charge = Setting::where('name', 'bitcoin_sell_charge')->first()->value ?? 0;

        if ($data['type'] == 'sell' && Auth::user()->bitcoinWallet->balance < ($data['quantity'] + $charge)) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient bitcoin wallet balance to initiate trade'
            ]);

        }

        if (!Auth::user()->nairaWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please create a Naira wallet to continue'
            ]);
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
           // Mail::to(Auth::user()->email)->send(new DantownNotification($title, $body, 'Transaction History', route('user.transactions')));
        }

        return response()->json([
            'success' => true,
            'msg' => 'Transaction initiated sucessfully'
        ]);
    }
}
