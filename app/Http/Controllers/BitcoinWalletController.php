<?php

namespace App\Http\Controllers;

use App\BitcoinTransaction;
use App\BitcoinWallet;
use App\Card;
use App\CardCurrency;
use App\Events\NewTransaction;
use App\Mail\DantownNotification;
use App\NairaTransaction;
use App\NairaWallet;
use App\Notification;
use App\Setting;
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

    public function getBitcoinNgn()
    {
        $card = Card::find(102);
        $rates = $card->currency->first();

        $res = json_decode(file_get_contents("https://api.coinbase.com/v2/prices/spot?currency=USD"));
        $btc_rate = $res->data->amount;
        $trading_per = Setting::where('name', 'trading_btc_per')->first()->value;
        $tp = ($trading_per / 100) * $btc_rate;
        $btc_rate -= $tp;

        $btc_wallet_bal  = Auth::user()->bitcoinWallet->balance ?? 0;
        $btc_usd = $btc_wallet_bal  * $btc_rate;

        $sell =  CardCurrency::where(['card_id' => 102, 'currency_id' => $rates->id, 'buy_sell' => 2])->first()->paymentMediums()->first();
        $rates->sell = json_decode($sell->pivot->payment_range_settings);

        $btc_ngn = $btc_usd * $rates->sell[0]->rate;

        return response()->json([
            'data' => (int)$btc_ngn
        ]);
    }

    public function wallet(Request $r)
    {

        if (!Auth::user()->bitcoinWallet) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please a bitcoin wallet to continue']);
        }

        if ($r->has('start')) {
            $transactions = Auth::user()->bitcoinWallet->transactions()->where('created_at', '>=', $r->start)->where('created_at', '<=', $r->end)->paginate(20);
        } else {
            $transactions = Auth::user()->bitcoinWallet->transactions()->paginate(5);
        }
        try {
            $fees_req = $this->instance->transactionApiBtcNewTransactionFee()->get(Constants::$BTC_MAINNET);
        } catch (\Throwable $th) {
            return back()->with(['error' => 'Network busy']);
        }

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
        $btc_usd = Auth::user()->bitcoinWallet->balance * $btc_rate;

        return view('newpages.bitcoin-wallet', compact('transactions', 'fees', 'btc_usd', 'btc_rate', 'charge', 'total_fees'));
    }

    public function create(Request $r)
    {
        $data = $r->validate([
            'wallet_password' => 'required|min:4',
        ]);

        if (Auth::user()->bitcoinWallet) {
            return back()->with(['error' => 'Bitcoin wallet already exists']);
        }

        if (Auth::user()->nairaWallet) {
            if (!Hash::check($data['wallet_password'], Auth::user()->nairaWallet->password)) {
                return back()->with(['error' => 'Incorrect Naira wallet password']);
            }
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
            return back()->with(['error' => 'An error occured, please try again']);
        }

        $title = 'Bitcoin Wallet created';
        $msg_body = 'Congratulations your Dantown Bitcoin Wallet has been created successfully, you can now send, receive, buy and sell Bitcoins in the wallet. ';
        $not = Notification::create([
            'user_id' => Auth::user()->id,
            'title' => $title,
            'body' => $msg_body,
        ]);

        Mail::to(Auth::user()->email)->send(new DantownNotification($title, $msg_body, 'Go to Wallet', route('user.bitcoin-wallet')));

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

        if ($r->type == 'buy') {
            return back()->with(['error' => 'Currently not available']);
        }

        if ($data['amount'] < 3) {
            return back()->with(['error' => 'Minimum trade amount is $3']);
        }

        if (!Auth::user()->bitcoinWallet) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please a bitcoin wallet to continue']);
        }

        if (!Auth::user()->nairaWallet) {
            return back()->with(['error' => 'Please create a Naira wallet to continue']);
        }

        if ($data['type'] == 'sell' && Auth::user()->bitcoinWallet->balance < $data['quantity'] /* + $charge */) {
            return back()->with(['error' => 'Insufficient bitcoin wallet balance to initiate trade']);
        }

        if ($r->type == 'buy' && Auth::user()->nairaWallet->amount < $r->amount_paid) {
            return back()->with(['error' => 'Insufficient wallet balance to complete this transaction ']);
        }

       /*  if (Auth::user()->transactions()->where('status', 'waiting')->count() >= 1 || Auth::user()->transactions()->where('status', 'in progress')->count() >= 1) {
            return back()->with(['error' => 'You cant initiate a new transaction with more than 1 waiting or processing transactions']);
        } */

        //Check if the trade details are correct

        //Correct figures
        $card = Card::find($data['card_id']);
        $card_id = $data['card_id'];
        $rates = $card->currency->first();

        $res = json_decode(file_get_contents("https://api.coinbase.com/v2/prices/spot?currency=USD"));
        $current_btc_rate = $res->data->amount;
        $main_rate = $current_btc_rate;

        #confirm id the difference is less than $10 before assigning


        $trade_rate = 0;
        $trading_per = Setting::where('name', 'trading_btc_per')->first()->value;
        $tp = ($trading_per / 100) * $current_btc_rate;

        if ($data['type'] == 'buy') {
            $current_btc_rate = $current_btc_rate + $tp;
            $abs = abs($current_btc_rate - $r->current_rate);
            if ($abs >= 10) {
                return back()->with(['error' => 'Network busy, please try again']);
            }

            if (Auth::user()->v_progress < 50) {
                return back()->with(['error' => 'Please upgrade your account to start buying Bitcoin']);
            }

            $buy =  CardCurrency::where(['card_id' => $card_id, 'currency_id' => $rates->id, 'buy_sell' => 1])->first()->paymentMediums()->first();
            $trade_rate = json_decode($buy->pivot->payment_range_settings);
            $trade_rate = $trade_rate[0]->rate;
        } else {
            $current_btc_rate = $current_btc_rate - $tp;
            $abs = abs($current_btc_rate - $r->current_rate);
            if ($abs >= 10) {
                return back()->with(['error' => 'Network busy, please try again']);
            }

            $sell =  CardCurrency::where(['card_id' => $card_id, 'currency_id' => $rates->id, 'buy_sell' => 2])->first()->paymentMediums()->first();
            $trade_rate = json_decode($sell->pivot->payment_range_settings);
            $trade_rate = $trade_rate[0]->rate;
        }

        $trade_usd = $data['amount'];
        $trade_btc = $data['amount'] / $r->current_rate;
        $trade_ngn = $data['amount'] * $trade_rate;

        //Convert the charge t naira and subtract it from the amount paid
        $charge = Setting::where('name', 'bitcoin_sell_charge')->first()->value ?? 0;
        $charge = ($charge / 100) * $data['quantity'];
        $charge_ngn = $charge * $r->current_rate * $trade_rate;

        if ($data['amount_paid'] != $trade_ngn || $data['quantity'] != $trade_btc) {
            return back()->with(['error' => 'Incorrect trade parameters, trade has been declined']);
        }

        if ($data['type'] == 'sell') {
            //Deduct the charge from the tranaction amount_paid
            $data['amount_paid'] -=  $charge_ngn;
        }


        $online_agent = User::where('role', 888)->where('status', 'active')->inRandomOrder()->first();
        $data['status'] = 'waiting';
        $data['uid'] = uniqid();
        $data['user_email'] = Auth::user()->email;
        $data['user_id'] = Auth::user()->id;
        $data['card'] = Card::find($r->card_id)->name;
        $data['agent_id'] = $online_agent->id;
        $data['card_price'] = $r->current_rate;

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
        if ($t->amount <= 49000 && $t->amount > 0) {
            $this->automatedPayment($t, $data['card_id'], $r->current_rate, $main_rate);
        }

        return redirect()->route('user.transactions');
    }



    public function automatedPayment(Transaction $t, $card_id, $current_rate, $main_rate)
    {
        //Current_rate was modified with % while $main_rate was not
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
        $fee_wallet = BitcoinWallet::where('name', 'bitcoin trade fee')->first();


        if ($transaction->type == 'buy') {
            $main_btc_quantity = $t->amount / $main_rate;  //the actual worth in btc
            $service_fee = abs($main_btc_quantity - $t->quantity);


            $charge = Setting::where('name', 'bitcoin_buy_charge')->first()->value ?? 0;
            $charge = ($charge / 100) * $transaction->quantity;
            /* Cross Check Balance */
            if ($user_naira_wallet->amount < $transaction->amount_paid) {
                return redirect()->back()->with(['error' => 'Insufficient Naira wallet balance to complete trade']);
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
            $main_btc_quantity = $t->amount / $main_rate;  //the actual worth in btc
            $service_fee = abs($main_btc_quantity - $t->quantity);

            $charge = Setting::where('name', 'bitcoin_sell_charge')->first()->value ?? 0;
            $charge = ($charge / 100) * $transaction->quantity;
            $btc_txn_type = 20;
            if ($user_btc_wallet->balance < ($transaction->quantity /* + $charge */)) {
                return redirect()->back()->with(['error' => 'Insufficient user bitcoin wallet balance']);
            }
            $user_btc_wallet->balance -= $transaction->quantity;
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

        $fee_wallet->balance += $service_fee;
        $fee_wallet->save();

        $btc_transaction = new BitcoinTransaction();
        $btc_transaction->user_id = $transaction->user->id;
        $btc_transaction->primary_wallet_id = $primary_wallet->id;
        $btc_transaction->wallet_id = $user_btc_wallet->address; //The wallet of the owner user
        $btc_transaction->hash = 'none';
        if ($transaction->type == 'buy') {
            $btc_transaction->credit = ($transaction->quantity - $charge);
        } elseif ($transaction->type == 'sell') {
            $btc_transaction->debit = ($transaction->quantity);
        }
        $btc_transaction->fee = $service_fee;
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

    public function send(Request $r)
    {

        return back()->with(['error' => 'Currently not available']);

        $data = $r->validate([
            'amount' => 'required|numeric',
            'address' => 'required|string',
            'pin' => 'required',
            'fees' => 'required',
        ]);
        if (!Auth::user()->bitcoinWallet) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please a bitcoin wallet to continue']);
        }

        $user_wallet = Auth::user()->bitcoinWallet;
        $primary_wallet = $user_wallet->primaryWallet;
        $charge_wallet = BitcoinWallet::where('name', 'bitcoin charges')->first();
        $fees = $data['fees'];
        $charge = Setting::where('name', 'bitcoin_charge')->first()->value;; // Get from Admin
        $total = $data['amount'] - $fees - $charge;

        //Check password
        if (!Hash::check($data['pin'], $user_wallet->password)) {
            return back()->with(['error' => 'Incorrect bitcoin wallet pin']);
        }

        //Add fees and Check balance
        if ($data['amount'] > $user_wallet->balance) {
            return back()->with(['error' => 'Insufficient balance']);
        }

        //Debit User
        $old_balance = $user_wallet->balance;
        $user_wallet->balance -= $data['amount'];
        $user_wallet->save();

        //Create transaction and set to pending
        $btc_transaction = new BitcoinTransaction();
        $btc_transaction->user_id = Auth::user()->id;
        $btc_transaction->primary_wallet_id = $user_wallet->primaryWallet->id;
        $btc_transaction->wallet_id = $user_wallet->address; //The wallet of the owner user
        $btc_transaction->hash = 'none';
        $btc_transaction->debit = $data['amount'];
        $btc_transaction->fee = $fees;
        $btc_transaction->charge = $charge;
        $btc_transaction->previous_balance = $old_balance;
        $btc_transaction->current_balance = $user_wallet->balance;
        $btc_transaction->transaction_type_id = 21;
        $btc_transaction->counterparty = $data['address'];
        $btc_transaction->narration = 'Sending bitcoin to ' . $data['address'];
        $btc_transaction->confirmations = 0;
        $btc_transaction->status = 'pending';
        $btc_transaction->save();

        //Push transaction using try



        //else revert users balance
        $send_total = number_format((float)$total, 8);
        $outputs = new \RestApis\Blockchain\BTC\Snippets\Output();
        $input = new \RestApis\Blockchain\BTC\Snippets\Input();
        $outputs->add($data['address'], $send_total);
        $input->add($primary_wallet->address, $send_total);
        $fee = new \RestApis\Blockchain\BTC\Snippets\Fee();
        $fee->set($fees);

        try {
            //update status and hash if it goes through
            $result = $this->instance->transactionApiBtcNewTransactionHdWallet()->create(Constants::$BTC_MAINNET, $primary_wallet->name, $primary_wallet->password, $outputs,  $fee);
            $btc_transaction->hash = $result->payload->txid;
            $btc_transaction->save();

            $charge_wallet->balance += $charge;
            $charge_wallet->save();

            //send mail
            return back()->with(['success' => 'Bitcoin sent successfully']);
        } catch (\Exception $e) {
            report($e);
            $user_wallet->balance = $old_balance;
            $user_wallet->save();

            $btc_transaction->status = 'failed';
            $btc_transaction->save();
            //set the transaction status to failed

            return back()->with(['error' => 'An error occured while processing the transaction please confirm the details and try again']);
        }
    }

    public function webhook(Request $request)
    {
        $confirmed = 6;  //set to 6 during live
        //Get the address
        $address = $request->address;
        //Get the transaction id
        $txn_id = $request->txid;
        //Check if the trnsaction already exists
        $btc_txn = BitcoinTransaction::where('hash', $request->txid)->first();

        if ($btc_txn == null) { //New Transaction e.g recieve
            //Get transaction details
            $result = $this->instance->transactionApiBtcTransactionsTxid()->get(Constants::$BTC_MAINNET, $txn_id);
            $txn_details = $result->payload;

            //if no confirmations and unconfirmed == true
            //loop through the outputs and cross check with the address from the webhook, then create transactions on that address
            //and set the status to unconfirmed
            if ($request->unconfirmed == true) {
                //get txins and the amount
                $txins = '';
                foreach ($txn_details->txins as $input) {
                    $txins .= $input->addresses[0] . ' ->' . $input->amount;
                }

                foreach ($txn_details->txouts as $output) {
                    //echo $output->addresses[0] . ' '. $output->amount . '<br>';
                    $addr = $output->addresses[0]; //since address is an array

                    if ($addr == $address) {
                        //Get user wallet
                        $user_wallet = BitcoinWallet::where('address', $addr)->firstOrFail();
                        //Get the user
                        $user = $user_wallet->user;
                        //Create txn
                        $btc_transaction = new BitcoinTransaction();
                        $btc_transaction->user_id = $user->id;
                        $btc_transaction->primary_wallet_id = $user_wallet->primary_wallet_id;
                        $btc_transaction->wallet_id = $user_wallet->address; //The wallet of the owner user
                        $btc_transaction->hash = $txn_details->txid;
                        $btc_transaction->credit = $output->amount;
                        $btc_transaction->fee = 0;
                        $btc_transaction->charge = 0;
                        $btc_transaction->previous_balance = $user_wallet->getOriginal('balance');
                        $btc_transaction->current_balance = $user_wallet->balance;
                        $btc_transaction->transaction_type_id = 22;
                        $btc_transaction->counterparty = substr($txins, 0, 100);
                        $btc_transaction->narration = 'Received bitcoin from ' . substr($txins, 0, 100);
                        $btc_transaction->confirmations = 0;
                        $btc_transaction->status = 'unconfirmed';
                        $btc_transaction->save();
                    }
                }
            } else {
                \Log::info('this txn needs help ' . $request->txid);
            }

            //if it is confirmed, update user balance and set the transaction status to sucecss and current balance on the txn

        } else { //Old transaction send / recieve transaction waiting for confirmation
            if (!$request->unconfirmed) {

                /* if confirmed and status is unconfirmed, Update users balance and set to success  and also current balance on the txn*/
                if ($request->confirmations == $confirmed && $btc_txn->status == 'unconfirmed') {
                    $user_wallet = $btc_txn->user->bitcoinWallet;
                    $old_balance = $user_wallet->balance;
                    $user_wallet->balance += $btc_txn->credit;
                    $user_wallet->save();

                    $btc_txn->confirmations = $request->confirmations;
                    $btc_txn->status = 'success';
                    $btc_txn->previous_balance = $old_balance;
                    $btc_txn->current_balance = $user_wallet->balance;
                    $btc_txn->save();

                    //Update user bitcoin if the transaction is unconfirmed


                    //if status is pending and confirmed, set status to success  for send txns
                } elseif ($request->confirmations == $confirmed && $btc_txn->status == 'pending') {
                    $btc_txn->confirmations = $request->confirmations;
                    $btc_txn->status = 'success';
                    $btc_txn->save();
                }
            }
        }
    }
}
