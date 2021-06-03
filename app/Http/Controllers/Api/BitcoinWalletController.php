<?php

namespace App\Http\Controllers\Api;

use App\BitcoinTransaction;
use App\BitcoinWallet;
use App\Card;
use App\CardCurrency;
use App\Events\NewTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTransaction;
use App\NairaWallet;
use App\Notification;
use App\Setting;
use App\Transaction;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RestApis\Blockchain\Constants;
use Illuminate\Support\Facades\Validator;

class BitcoinWalletController extends Controller
{
    public function __construct()
    {
        $this->instance = new \RestApis\Factory(env('BITCOIN_WALLET_API_KEY'));
    }

    public function create(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'wallet_password' => 'required|min:4|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        if (Auth::user()->bitcoinWallet->count() > 0) {
            return response()->json([
                'success' => false,
                'msg' => 'A Bitcoin wallet exists for this account'
            ]);
        }

        $password = Hash::make($r->wallet_password);

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
        $total = $r->amount - $fees - $charge;

        //Check password
        if (!Hash::check($r->pin, $user_wallet->password)) {
            return response()->json([
                'success' => false,
                'msg' => 'Incorrect Bitcoin wallet password'
            ]);
        }

        //Add fees and Check balance
        if ($r->amount > $user_wallet->balance) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient Bitcoin Balance'
            ]);
        }

        //Debit User
        $old_balance = $user_wallet->balance;
        $user_wallet->balance -= $r->amount;
        $user_wallet->save();

        //Create transaction and set to pending
        $btc_transaction = new BitcoinTransaction();
        $btc_transaction->user_id = Auth::user()->id;
        $btc_transaction->primary_wallet_id = $user_wallet->primaryWallet->id;
        $btc_transaction->wallet_id = $user_wallet->address; //The wallet of the owner user
        $btc_transaction->hash = 'none';
        $btc_transaction->debit = $r->amount;
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
        $send_total = number_format((float)$total, 8);
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

            /* $charge_wallet->balance -= $charge;
            $charge_wallet->save(); */

            return response()->json([
                'success' => false,
                'msg' => 'An error occured while processing the transaction please confirm the details and try again'
            ]);
        }
    }

    public function trade(Request $r)
    {
       /*  return response()->json([
            'success' => false,
            'msg' => 'balance is '. Auth::user()->bitcoinWallet->balance .' and amount is '. $r->quantity
        ]); */

        $validator = Validator::make($r->all(), [
            'card_id' => 'required|integer',
            'type' => 'required|string',
            'amount' => 'required',
            'amount_paid' => 'required',
            'quantity' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        if ($r->amount < 3 ) {
            return response()->json([
                'success' => false,
                'msg' => 'Minimum trade amount is $3'
            ]);
        }

        if (!Auth::user()->bitcoinWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please a Bitcoin wallet to continue'
            ]);
        }

        if (!Auth::user()->nairaWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please create a Naira wallet to continue'
            ]);
        }

        if ($r->type == 'sell' && Auth::user()->bitcoinWallet->balance < $r->quantity) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient Bitcoin wallet balance to initiate trade'
            ]);
        }

        if ($r->type == 'buy' && Auth::user()->nairaWallet->amount < $r->amount_paid) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient Naira wallet balance to complete this transaction'
            ]);

        }

        if (Auth::user()->transactions()->where('status', 'waiting')->count() >= 1 || Auth::user()->transactions()->where('status', 'in progress')->count() >= 1) {
            return back()->with(['error' => 'You cant initiate a new transaction with more than 1 waiting or processing transactions']);
        }

        //Check if the trade details are correct

        //Correct figures
        $card = Card::find($r->card_id);
        $card_id = $r->card_id;
        $rates = $card->currency->first();

        $res = json_decode(file_get_contents("https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd"));
        $current_btc_rate = $res->bitcoin->usd;

        #confirm id the difference is less than $10 before assigning
        $abs = abs($current_btc_rate - $r->current_rate);
        if ($abs >= 10) {
            return response()->json([
                'success' => false,
                'msg' => 'Network busy, please try again'
            ]);
        }

        $trade_rate = 0;

        if ($r->type == 'buy') {
            $buy =  CardCurrency::where(['card_id' => $card_id, 'currency_id' => $rates->id, 'buy_sell' => 1])->first()->paymentMediums()->first();
            $trade_rate = json_decode($buy->pivot->payment_range_settings);
            $trade_rate = $trade_rate[0]->rate;
        } else {
            $sell =  CardCurrency::where(['card_id' => $card_id, 'currency_id' => $rates->id, 'buy_sell' => 2])->first()->paymentMediums()->first();
            $trade_rate = json_decode($sell->pivot->payment_range_settings);
            $trade_rate = $trade_rate[0]->rate;
        }

        //Check if the details are correct using the dollar value to get other values
        $trade_btc = $r->amount / $r->current_rate;
        $trade_ngn = $r->amount * $trade_rate;

        //Convert the charge to naira and subtract it from the amount paid
        $charge = Setting::where('name', 'bitcoin_sell_charge')->first()->value ?? 0;
        $charge_ngn = $charge * $r->current_rate * $trade_rate;

        if ($r->amount_paid != $trade_ngn || $r->quantity != $trade_btc) {
            return response()->json([
                'success' => false,
                'msg' => 'Incorrect trade parameters, trade has been declined'
            ]);
        }

        if($r->type == 'sell') {
            //Deduct the charge from the tranaction amount_paid
            $r->amount_paid -=  $charge_ngn;
        }


        $online_agent = User::where('role', 888)->where('status', 'active')->inRandomOrder()->first();
        $data['status'] = 'waiting';
        $data['uid'] = uniqid();
        $data['user_email'] = Auth::user()->email;
        $data['user_id'] = Auth::user()->id;
        $data['card'] = Card::find($r->card_id)->name;
        $data['agent_id'] = $online_agent->id;

        $data['card_id'] = $r->card_id;
        $data['type'] = $r->type;
        $data['amount'] = $r->amount;
        $data['amount_paid'] = $r->amount_paid;
        $data['quantity'] = $r->quantity;

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

        //Call autonated pay function
        if ($t->amount < 49000 && $t->amount > 0) {
           // $this->automatedPayment($t, $data['card_id'], $r->current_rate);
        }

        return response()->json([
            'success' => true,
            'msg' => 'Trade initiated successfully'
        ]);
    }


    public function automatedPayment(Transaction $t, $card_id, $current_rate)
    {
        $card = Card::find($card_id);
        $rates = $card->currency->first();


        $transaction = $t;
        $btc_txn_type = 0;
        $user_naira_wallet = $transaction->user->nairaWallet;
        $user_btc_wallet = $transaction->user->bitcoinWallet;
        $old_user_btc_balance = $user_btc_wallet->balance;
        $old_user_naira_balance = $user_naira_wallet->amount;
        $primary_wallet = $user_btc_wallet->primaryWallet;
        $user = $transaction->user;
        $charge = 0;
        $charge_wallet = BitcoinWallet::where('name', 'bitcoin charges')->first();


        if ($transaction->type == 'buy') {
            $charge = Setting::where('name', 'bitcoin_buy_charge')->first()->value ?? 0;
            /* Cross Check Balance */
            if ($user_naira_wallet->amount < $transaction->amount_paid) {
                return response()->json([
                    'success' => true,
                    'msg' => 'Insufficient Naira wallet balance to complete trade'
                ]);
            }
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
            $nt->previous_balance = $old_user_naira_balance;
            $nt->current_balance = $user_naira_wallet->amount;
            $nt->charge = 0;
            $nt->transaction_type_id = 5;
            $nt->cr_wallet_id = $n->id;
            $nt->dr_wallet_id = $user_naira_wallet->id;
            $nt->cr_acct_name = 'Dantown';
            $nt->dr_acct_name = $user->first_name . ' ' . $user->last_name;
            $nt->narration = 'Debit for buy transaction with id ' . $transaction->uid;
            $nt->trans_msg = 'This transaction was handled automatically';
            $nt->dr_user_id = $user->id;
            $nt->cr_user_id = 1;
            $nt->status = 'success';
            $nt->save();

            $user_btc_wallet->balance += ($transaction->quantity - $charge);
            $user_btc_wallet->save();

            $primary_wallet->balance -= $transaction->quantity;
            $primary_wallet->save();
        } elseif ($transaction->type == 'sell') {
            $charge = Setting::where('name', 'bitcoin_sell_charge')->first()->value ?? 0;
            $btc_txn_type = 20;
            if ($user_btc_wallet->balance < ($transaction->quantity )) {
                return response()->json([
                    'success' => true,
                    'msg' => 'Insufficient Bitcoin wallet balance to complete trade'
                ]);
            }
            $user_btc_wallet->balance -= ($transaction->quantity );
            $user_btc_wallet->save();

            $primary_wallet->balance += $transaction->quantity - $charge;
            $primary_wallet->save();

            $user_naira_wallet->amount += $transaction->amount_paid;
            $user_naira_wallet->save();

            //Create naira txn
            $reference = \Str::random(2) . '-' . $transaction->id;
            $n = NairaWallet::find(1);
            $nt = new NairaTransaction();
            $nt->reference = $reference;
            $nt->amount = $transaction->amount_paid;
            $nt->user_id = $user->id;
            $nt->type = 'naira wallet';
            $nt->previous_balance = $old_user_naira_balance;
            $nt->current_balance = $user_naira_wallet->amount;
            $nt->charge = 0;
            $nt->transaction_type_id = 4;
            $nt->dr_wallet_id = $n->id;
            $nt->cr_wallet_id = $user_naira_wallet->id;
            $nt->dr_acct_name = 'Dantown';
            $nt->cr_acct_name = $user->first_name . ' ' . $user->last_name;
            $nt->narration = 'Credit for sell transaction with id ' . $transaction->uid;
            $nt->trans_msg = 'This transaction was handled automatically ';
            $nt->cr_user_id = $user->id;
            $nt->dr_user_id = 1;
            $nt->status = 'success';
            $nt->save();
        } else {
            return back()->with(['error' => 'Invalid transaction']);
        }

        #send charge to charge wallet
        $charge_wallet->balance += $charge;
        $charge_wallet->save();

        $btc_transaction = new BitcoinTransaction();
        $btc_transaction->user_id = $transaction->user->id;
        $btc_transaction->primary_wallet_id = $primary_wallet->id;
        $btc_transaction->wallet_id = $user_btc_wallet->address; //The wallet of the owner user
        $btc_transaction->hash = 'none';
        if ($transaction->type == 'buy') {
            $btc_transaction->credit = ($transaction->quantity - $charge);
        } elseif ($transaction->type == 'sell') {
            $btc_transaction->debit = ($transaction->quantity + $charge);
        }
        $btc_transaction->fee = 0;
        $btc_transaction->charge = $charge;
        $btc_transaction->previous_balance = $old_user_btc_balance;
        $btc_transaction->current_balance = $user_btc_wallet->balance;
        $btc_transaction->transaction_type_id = $btc_txn_type;
        $btc_transaction->counterparty = 'Dantown Assets';
        $btc_transaction->narration = 'Approved automatically';
        $btc_transaction->confirmations = 3;
        $btc_transaction->status = 'success';
        $btc_transaction->save();

        $transaction->status = 'success';
        $transaction->save();
    }
}
