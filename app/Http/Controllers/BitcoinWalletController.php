<?php

namespace App\Http\Controllers;

use App\BitcoinTransaction;
use App\BitcoinWallet;
use App\Card;
use App\CardCurrency;
use App\Events\NewTransaction;
use App\Mail\DantownNotification;
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
        $res = json_decode(file_get_contents("https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd"));
        $btc_rate = $res->bitcoin->usd;
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

        if (!Hash::check($data['wallet_password'], Auth::user()->nairaWallet->password)) {
            return back()->with(['error' => 'Incorrect Naira wallet password']);
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

        if (!Auth::user()->bitcoinWallet) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please a bitcoin wallet to continue']);
        }

        if (!Auth::user()->nairaWallet) {
            return back()->with(['error' => 'Please create a Naira wallet to continue']);
        }

        if (Auth::user()->transactions()->where('status', 'waiting')->count() >= 3 || Auth::user()->transactions()->where('status', 'in progress')->count() >= 3) {
            return back()->with(['error' => 'You cant initiate a new transaction with more than 3 waiting or processing transactions']);
        }

        $charge = Setting::where('name', 'bitcoin_sell_charge')->first()->value ?? 0;

        if ($data['type'] == 'sell' && Auth::user()->bitcoinWallet->balance < ($data['quantity'] + $charge)) {
            return back()->with(['error' => 'Insufficient bitcoin wallet balance to initiate trade']);
        }

        if ($r->type == 'buy' && Auth::user()->nairaWallet->amount < $r->amount_paid) {
            return back()->with(['error' => 'Insufficient wallet balance to complete this transaction ']);
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

        return redirect()->route('user.transactions');
    }

    public function send(Request $r)
    {
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
        $fees = $data['fees'];
        $charge = Setting::where('name', 'bitcoin_charge')->first()->value;; // Get from Admin
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
        $btc_transaction->narration = 'Sending bitcoin to ' . $data['address'];
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
            $result = $this->instance->transactionApiBtcNewTransactionHdWallet()->create(Constants::$BTC_MAINNET, $primary_wallet->name, $primary_wallet->password, $input,  $outputs,  $fee);
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
        $confirmed = 6;  //set to 6 during live
        //Get the address
        $address = $request->address;
        //Get the transaction id
        $txn_id = $request->txid;
        //Check if the trnsaction already exists
        $btc_txn = BitcoinTransaction::where('hash', $request->txid)->first();

        if ($btc_txn == null) { //New Transaction e.g recieve
            //Get transaction details
            $result = $this->instance->transactionApiBtcTransactionsTxid()->get(Constants::$BTC_TESTNET, $txn_id);
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
                        $btc_transaction->counterparty = $txins;
                        $btc_transaction->narration = 'Received bitcoin from ' . $txins;
                        $btc_transaction->confirmations = 0;
                        $btc_transaction->status = 'unconfirmed';
                        $btc_transaction->save();
                    }
                }
            }

            //if it is confirmed, update user balance and set the transaction status to sucecss and current balance on the txn

        } else { //Old transaction send / recieve transaction waiting for confirmation
            if (!$request->unconfirmed) {

                /* if confirmed and status is unconfirmed, Update users balance and set to success  and also current balance on the txn*/
                if ($request->confirmations == $confirmed && $btc_txn->status == 'unconfirmed') {
                    $user_wallet = $btc_txn->user->bitcoinWallet;
                    $user_wallet->balance += $btc_txn->credit;
                    $user_wallet->save();

                    $btc_txn->confirmations = $request->confirmations;
                    $btc_txn->status = 'success';
                    $btc_txn->current_balance = $user_wallet->balance;
                    $btc_txn->save();

                    //Update user bitcoin if the transaction is unconfirmed


                    //if status is pending and confirmed, set status to success
                } elseif ($request->confirmations == $confirmed && $btc_txn->status == 'pending') {
                    $btc_txn->confirmations = $request->confirmations;
                    $btc_txn->status = 'success';
                    $btc_txn->save();
                }
            }
        }
    }
}
