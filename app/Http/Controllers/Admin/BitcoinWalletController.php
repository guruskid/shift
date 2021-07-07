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
        $this->instance = new \RestApis\Factory(env('BITCOIN_WALLET_API_KEY'));
    }

    public function index()
    {
        $charges = BitcoinWallet::where('name', 'bitcoin charges')->first()->balance ?? 0;
        $service_fee = BitcoinWallet::where('name', 'bitcoin trade fee')->first()->balance ?? 0;
        $hd_wallets_balance = BitcoinWallet::where('type', 'primary')->sum('balance');

        $users_wallet_balance = BitcoinWallet::where('type', 'secondary')->where('user_id', '!=', 1)->sum('balance');
        $live_balance = 0; //get from api
        $transactions = BitcoinTransaction::latest()->paginate(200);

        $hd_wallets = BitcoinWallet::where('primary_wallet_id', 0)->get();
        foreach ($hd_wallets as $wallet ) {
            $result = $this->instance->walletApiBtcGetWallet()->getHd(Constants::$BTC_MAINNET, $wallet->name);
            $live_balance += $result->payload->totalBalance;
        }
        /* $live_balance = 30; */


        return view('admin.bitcoin_wallet.index', compact('charges', 'service_fee', 'hd_wallets_balance', 'transactions', 'users_wallet_balance', 'live_balance'));
    }

    public function wallets()
    {
        $wallets = BitcoinWallet::where('user_id', '!=', 1)->latest()->get();
        $bitcoin_charge = Setting::where('name', 'bitcoin_charge')->first();
        $bitcoin_buy_charge = Setting::where('name', 'bitcoin_buy_charge')->first();
        $bitcoin_sell_charge = Setting::where('name', 'bitcoin_sell_charge')->first();

        return view('admin.bitcoin_wallet.wallets', compact(['wallets', 'bitcoin_charge', 'bitcoin_buy_charge', 'bitcoin_sell_charge']));
    }

    public function liveBalanceTransactions(Request $request)
    {

        if ($request->start && $request->end) {

            $credit_transactions = BitcoinTransaction::where('transaction_type_id', 22)->where('status', 'success')
            ->where('created_at', '>=', $request->start)->where('created_at', '<=', $request->end)
            ->paginate(200);

            $debit_transactions = BitcoinTransaction::where('transaction_type_id', 21)->where('status', 'success')
            ->where('created_at', '>=', $request->start)->where('created_at', '<=', $request->end)
            ->paginate(200);
        } else {
            $credit_transactions = BitcoinTransaction::where('transaction_type_id', 22)->where('status', 'success')->paginate(200);
            $debit_transactions = BitcoinTransaction::where('transaction_type_id', 21)->where('status', 'success')->paginate(200);
        }



        return view('admin.bitcoin_wallet.live_summary', compact('credit_transactions', 'debit_transactions'));
    }

    public function hdWallets()
    {
        $fees_req = $this->instance->transactionApiBtcNewTransactionFee()->get(Constants::$BTC_MAINNET);
        $fees = $fees_req->payload->recommended;
        $hd_wallets = BitcoinWallet::where('type', 'primary')->get();

        return view('admin.bitcoin_wallet.hd_wallets', compact('hd_wallets', 'fees'));
    }

    public function charges()
    {
        $fees_req = $this->instance->transactionApiBtcNewTransactionFee()->get(Constants::$BTC_MAINNET);
        $fees = $fees_req->payload->recommended;
        $transactions = BitcoinTransaction::where('charge', '!=', 0)->where('status', 'success')->latest()->paginate(200);
        $charges = BitcoinWallet::where('name', 'bitcoin charges')->first()->balance ?? 0;
        $bitcoin_charge = Setting::where('name', 'bitcoin_charge')->first();
        $bitcoin_buy_charge = Setting::where('name', 'bitcoin_buy_charge')->first();
        $bitcoin_sell_charge = Setting::where('name', 'bitcoin_sell_charge')->first();

        return view('admin.bitcoin_wallet.charges', compact('transactions', 'fees', 'bitcoin_charge', 'charges', 'bitcoin_buy_charge', 'bitcoin_sell_charge'));
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
                'value' => $data['bitcoin_charge']
            ]
        );

        $buy_charge = Setting::updateorCreate(
            [

                'name' => 'bitcoin_buy_charge',
            ],
            [
                'value' => $data['bitcoin_buy_charge']
            ]
        );

        $sell_charge = Setting::updateorCreate(
            [

                'name' => 'bitcoin_sell_charge',
            ],
            [
                'value' => $data['bitcoin_sell_charge']
            ]
        );

        return back()->with(['success' => 'Bitcoin charge set successfully']);
    }

    public function transferCharges(Request $r)
    {
        $data = $r->validate([
            'fees' => 'required',
            'amount' => 'required',
            'pin' => 'required',
            'address' => 'required',
            'wallet' => 'required',
        ]);

        if (!Hash::check($data['pin'], Auth::user()->bitcoinWallet->password)) {
            return back()->with(['error' => 'Incorrect pin']);
        }

        $total = $data['amount'] + $data['fees'];
        $charges_wallet = BitcoinWallet::where('name', $data['wallet'])->first();
        $primary_wallet = BitcoinWallet::where('type', 'primary')->first();


        if ($total > $charges_wallet->balance) {
            return back()->with(['error' => 'Insufficient balance']);
        }

        $c_old_balance = $charges_wallet->balance;
        $charges_wallet->balance -= $total;
        $charges_wallet->save();

        $btc_transaction = new BitcoinTransaction();
        $btc_transaction->user_id = 1;
        $btc_transaction->primary_wallet_id = $primary_wallet->id;
        $btc_transaction->wallet_id = $charges_wallet->address; //The wallet of the owner user
        $btc_transaction->hash = 'none';
        $btc_transaction->debit = $total;
        $btc_transaction->fee = $data['fees'];
        $btc_transaction->charge = 0;
        $btc_transaction->previous_balance = $c_old_balance;
        $btc_transaction->current_balance = $charges_wallet->balance;
        $btc_transaction->transaction_type_id = 21;
        $btc_transaction->counterparty = $data['address'];
        $btc_transaction->narration = 'Sending bitcoin to ' . $data['address'] . ' Authorized by ' . Auth::user()->first_name;
        $btc_transaction->confirmations = 0;
        $btc_transaction->status = 'pending';
        $btc_transaction->save();

        $send_total = number_format((float)$data['amount'], 8);
        $outputs = new \RestApis\Blockchain\BTC\Snippets\Output();
        $input = new \RestApis\Blockchain\BTC\Snippets\Input();
        $outputs->add($data['address'], $send_total);
        $input->add($primary_wallet->address, $send_total);

        $fee = new \RestApis\Blockchain\BTC\Snippets\Fee();
        $fee->set($data['fees']);

        try {
            //update status and hash if it goes through
            $result = $this->instance->transactionApiBtcNewTransactionHdWallet()->create(Constants::$BTC_MAINNET, $primary_wallet->name, $primary_wallet->password,  $outputs,  $fee);
            $btc_transaction->hash = $result->payload->txid;
            $btc_transaction->status = 'success';
            $btc_transaction->save();

            //send mail
            return back()->with(['success' => 'Charges sent successfully']);
        } catch (\Exception $e) {
            report($e);
            $charges_wallet->balance = $c_old_balance;
            $charges_wallet->save();

            $btc_transaction->status = 'failed';
            $btc_transaction->save();
            //set the transaction status to failed

            return back()->with(['error' => 'An error occured while processing the transaction please confirm the details and try again']);
        }
    }

    public function serviceFee()
    {
        $fees_req = $this->instance->transactionApiBtcNewTransactionFee()->get(Constants::$BTC_MAINNET);
        $fees = $fees_req->payload->recommended;

        $service_fee = BitcoinWallet::where('name', 'bitcoin trade fee')->first()->balance;
        $transactions = BitcoinTransaction::whereIn('transaction_type_id', [19, 20])->where('fee', '!=', 0)->paginate(20);
        $tp = Setting::where('name', 'trading_btc_per')->first()->value;

        return view('admin.bitcoin_wallet.service', compact('transactions', 'service_fee', 'tp', 'fees'));
    }

    public function setFee(Request $request)
    {
        $tp = Setting::where('name', 'trading_btc_per')->first();

        $tp->value = $request->fee;
        $tp->save();

        return back()->with(['success' => 'Bitcoin fee set successfully']);
    }

    public function sendFromHd(Request $r)
    {
        $data = $r->validate([
            'primary_wallet' => 'required|integer',
            'address' => 'required',
            'amount' => 'required',
            'wallet_password' => 'required',
            /* 'pin' => 'required', */
            'account_password' => 'required',
            'fees' => 'required'
        ]);

        $total = $data['amount'] + $data['fees'];
        $primary_wallet = BitcoinWallet::find($data['primary_wallet']);

        if (!Hash::check($data['wallet_password'], $primary_wallet->password)) {
            return back()->with(['error' => 'Incorrect pin primary wallet password']);
        }

        /* if (!Hash::check($data['pin'], Auth::user()->bitcoinWallet->password)) {
            return back()->with(['error' => 'Incorrect personal wallet pin']);
        } */

        if (!Hash::check($data['account_password'], Auth::user()->password)) {
            return back()->with(['error' => 'Incorrect account password']);
        }

        if ($total > $primary_wallet->balance) {
            return back()->with(['error' => 'Insufficient balance']);
        }

        $old_balance = $primary_wallet->balance;
        $primary_wallet->balance -= $total;
        $primary_wallet->save();

        $btc_transaction = new BitcoinTransaction();
        $btc_transaction->user_id = 1;
        $btc_transaction->primary_wallet_id = $primary_wallet->id;
        $btc_transaction->wallet_id = $primary_wallet->address; //The wallet of the owner user
        $btc_transaction->hash = 'none';
        $btc_transaction->debit = $total;
        $btc_transaction->fee = $data['fees'];
        $btc_transaction->charge = 0;
        $btc_transaction->previous_balance = $old_balance;
        $btc_transaction->current_balance = $primary_wallet->balance;
        $btc_transaction->transaction_type_id = 21;
        $btc_transaction->counterparty = $data['address'];
        $btc_transaction->narration = 'Sending bitcoin to ' . $data['address'] . ' Authorized by ' . Auth::user()->first_name;
        $btc_transaction->confirmations = 0;
        $btc_transaction->status = 'pending';
        $btc_transaction->save();

        $outputs = new \RestApis\Blockchain\BTC\Snippets\Output();
        $input = new \RestApis\Blockchain\BTC\Snippets\Input();
        $outputs->add($data['address'], $data['amount']);
        $input->add($primary_wallet->address, $data['amount']);

        $fee = new \RestApis\Blockchain\BTC\Snippets\Fee();
        $fee->set($data['fees']);

        try {
            //update status and hash if it goes through
            $result = $this->instance->transactionApiBtcNewTransactionHdWallet()->create(Constants::$BTC_MAINNET, $primary_wallet->name, $primary_wallet->password, $outputs,  $fee);
            $btc_transaction->hash = $result->payload->txid;
            $btc_transaction->status = 'success';
            $btc_transaction->save();

            //send mail
            return back()->with(['success' => 'Bitcoin sent successfully']);
        } catch (\Exception $e) {
            report($e);
            $primary_wallet->balance = $old_balance;
            $primary_wallet->save();

            $btc_transaction->status = 'failed';
            $btc_transaction->save();
            //set the transaction status to failed

            return back()->with(['error' => 'An error occured while processing the transaction please confirm the details and try again']);
        }
    }

    public function transactions()
    {
        $transactions = BitcoinTransaction::latest()->paginate(200);

        return view('admin.bitcoin_wallet.transactions', compact(['transactions']));
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
        $u_old_n_balance =  $user_naira_wallet->amount;
        $u_old_b_balance = $user->bitcoinWallet->balance;

        if ($transaction->type == 'buy') {
            $charge = Setting::where('name', 'bitcoin_buy_charge')->first()->value ?? 0;
            $charge = ($charge / 100) * $transaction->quantity;
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
            $nt->previous_balance = $u_old_n_balance;
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
            $charge = ($charge / 100) * $transaction->quantity;
            $btc_txn_type = 20;
            if ($user_btc_wallet->balance < ($transaction->quantity /* + $charge */)) {
                return redirect()->back()->with(['error' => 'Insufficient user bitcoin wallet balance, when charge was included']);
            }
            $user_btc_wallet->balance -= ($transaction->quantity /* + $charge */);
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
            $nt->previous_balance = $u_old_n_balance;
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
            $btc_transaction->debit = ($transaction->quantity);
        }
        $btc_transaction->fee = 0;
        $btc_transaction->charge = $charge;
        $btc_transaction->previous_balance = $u_old_b_balance;
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
