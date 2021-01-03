<?php

namespace App\Http\Controllers\Admin;

use App\BitcoinTransaction;
use App\BitcoinWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NairaTransaction;
use App\NairaWallet;
use App\Setting;
use App\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RestApis\Blockchain\Constants;

class BitcoinWalletController extends Controller
{

    public function __construct()
    {
        $this->instance = $instance = new \RestApis\Factory(env('BITCOIN_WALLET_API_KEY'));
    }

    public function index()
    {
        $charges = BitcoinWallet::where('name', 'bitcoin charges')->first()->balance ?? 0;
        $hd_wallets_balance = BitcoinWallet::where('type', 'primary')->sum('balance');
        $users_wallet_balance = BitcoinWallet::where('type', 'secondary')->where('user_id', '!=', 1)->sum('balance');
        $live_balance = 10; //get from api
        $transactions = BitcoinTransaction::latest()->paginate(200);

        return view('admin.bitcoin_wallet.index', compact('charges', 'hd_wallets_balance', 'transactions', 'users_wallet_balance', 'live_balance'));
    }

    public function wallets()
    {
        $wallets = BitcoinWallet::where('user_id', '!=', 1)->latest()->get();
        $bitcoin_charge = Setting::where('name', 'bitcoin_charge')->first();
        $bitcoin_buy_charge = Setting::where('name', 'bitcoin_buy_charge')->first();
        $bitcoin_sell_charge = Setting::where('name', 'bitcoin_sell_charge')->first();

        return view('admin.bitcoin_wallet.wallets', compact(['wallets', 'bitcoin_charge', 'bitcoin_buy_charge', 'bitcoin_sell_charge']));
    }

    public function hdWallets()
    {
        $hd_wallets = BitcoinWallet::where('type', 'primary')->get();

        return view('admin.bitcoin_wallet.hd_wallets', compact('hd_wallets'));
    }

    public function charges()
    {
        $transactions = BitcoinTransaction::where('charge', '!=', 0)->latest()->paginate(200);
        $charges = BitcoinWallet::where('name', 'bitcoin charges')->first()->balance ?? 0;
        $bitcoin_buy_charge = Setting::where('name', 'bitcoin_buy_charge')->first();
        $bitcoin_sell_charge = Setting::where('name', 'bitcoin_sell_charge')->first();

        return view('admin.bitcoin_wallet.charges', compact('transactions', 'charges', 'bitcoin_buy_charge', 'bitcoin_sell_charge'));

    }

    public function transactions()
    {
        $transactions = BitcoinTransaction::latest()->paginate(200);

        return view('admin.bitcoin_wallet.transactions', compact(['transactions']));
    }


    public function setCharge(Request $r)
    {
        $data = $r->validate([
            'bitcoin_charge' => 'required',
            'bitcoin_buy_charge' => 'required',
            'bitcoin_sell_charge' => 'required',
        ]);

        $id = Setting::latest()->first()->id;
        /* $id += 3; */
        $send_charge = Setting::updateorCreate(
            [

                'name' => 'bitcoin_charge',
            ],
            [
                'id' => $id +1,
                'value' => $data['bitcoin_charge']
            ]
        );

        $buy_charge = Setting::updateorCreate(
            [

                'name' => 'bitcoin_buy_charge',
            ],
            [
                'id' => $id+2,
                'value' => $data['bitcoin_buy_charge']
            ]
        );

        $sell_charge = Setting::updateorCreate(
            [

                'name' => 'bitcoin_sell_charge',
            ],
            [
                'id' => $id+4,
                'value' => $data['bitcoin_sell_charge']
            ]
        );

        return back()->with(['success' => 'Bitcoin charge set successfully']);
    }

    public function createHdWallet(Request $r)
    {
        $data = $r->validate([
            'wallet_password' => 'required|min:10|confirmed',
            'account_password' => 'required',
            'name' => 'required|string|unique:bitcoin_wallets,name'
        ]);

        if (!Hash::check($data['account_password'], Auth::user()->password)) {
            return back()->with(['error' => 'Wrong Account password']);
        }
        $password = Hash::make($data['wallet_password']);
        /*   $result = $this->instance->walletApiBtcCreateAddress()->createHd(Constants::$BTC_MAINNET, $data['name'], $password,1);
        dd($result->payload->addresses[0]->path); */

        try {
            $result = $this->instance->walletApiBtcCreateAddress()->createHd(Constants::$BTC_MAINNET, $data['name'], $password, 1);
            $wallet = new BitcoinWallet();
            $address = $result->payload->addresses[0];
            $wallet->user_id = 1;
            $wallet->path = $address->path;
            $wallet->address = $address->address;
            $wallet->type = 'primary';
            $wallet->name = $data['name'];
            $wallet->password = $password;
            $wallet->balance = 0;
            $wallet->primary_wallet_id = 0;
            $wallet->save();

            $callback = route('user.wallet-webhook');

            $result = $this->instance->webhookBtcCreateAddressTransaction()->create(Constants::$BTC_MAINNET, $callback, $wallet->address, 6);
        } catch (\Throwable  $e) {
            report($e);
            return back()->with(['error' => 'An error occured, please try again']);
        }
        return back()->with(['success' => 'HD wallet created successfully']);
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
        $charge = 0;
        $charge_wallet = BitcoinWallet::where('name', 'bitcoin charges')->first();

        if (!Hash::check($data['primary_wallet_pin'], $primary_wallet->password)) {
            return redirect()->back()->with(['error' => 'Incorrect primary wallet password']);
        }



        /* Confirm User has wallet */
        if (!$transaction->user->bitcoinWallet) {
            return redirect()->back()->with(['error' => 'No bitcoin wallet associated with this account']);
        }

        /* Update User Balance */

        if ($transaction->type == 'buy') {
            $charge = Setting::where('name', 'bitcoin_buy_charge')->first()->value ?? 0;
            /* Cross Check Balance */
            if ($primary_wallet->balance < $transaction->quantity) {
                return redirect()->back()->with(['error' => 'Insufficient primary wallet balance']);
            }

            if ($user_naira_wallet->amount < $transaction->amount_paid) {
                return redirect()->back()->with(['error' => 'Insufficient user naira wallet balance']);
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

            $user_btc_wallet->balance += ($transaction->quantity - $charge);
            $user_btc_wallet->save();

            $primary_wallet->balance -= $transaction->quantity;
            $primary_wallet->save();
        } elseif ($transaction->type == 'sell') {
            $charge = Setting::where('name', 'bitcoin_sell_charge')->first()->value ?? 0;
            $btc_txn_type = 20;
            if ($user_btc_wallet->balance < ($transaction->quantity + $charge)) {
                return redirect()->back()->with(['error' => 'Insufficient user bitcoin wallet balance, when charge was included']);
            }
            $user_btc_wallet->balance -= ($transaction->quantity + $charge);
            $user_btc_wallet->save();

            $primary_wallet->balance += $transaction->quantity;
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
            $nt->previous_balance = $user_naira_wallet->getOriginal('amount');
            $nt->current_balance = $user_naira_wallet->amount;
            $nt->charge = 0;
            $nt->transaction_type_id = 4;
            $nt->dr_wallet_id = $n->id;
            $nt->cr_wallet_id = $user_naira_wallet->id;
            $nt->dr_acct_name = 'Dantown';
            $nt->cr_acct_name = $user->first_name . ' ' . $user->last_name;
            $nt->narration = 'Credit for sell transaction with id ' . $transaction->uid;
            $nt->trans_msg = 'This transaction was handled by ' . Auth::user()->first_name;
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
        $btc_transaction->previous_balance = $user_btc_wallet->getOriginal('balance');
        $btc_transaction->current_balance = $user_btc_wallet->balance;
        $btc_transaction->transaction_type_id = $btc_txn_type;
        $btc_transaction->counterparty = 'Dantown Assets';
        $btc_transaction->narration = 'Approved by' . Auth::user()->id;
        $btc_transaction->confirmations = 3;
        $btc_transaction->save();

        $transaction->status = 'success';
        $transaction->save();

        return back()->with(['success' => 'Transaction completed successfully ']);
        /* Redirect User Back to where he came from */
    }
}
