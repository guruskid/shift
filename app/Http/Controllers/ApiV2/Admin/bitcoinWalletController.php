<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\AdminAddresses;
use App\BitcoinTransaction;
use App\BitcoinWallet;
use App\BtcMigration;
use App\CryptoCurrency;
use App\HdWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Setting;
use App\Wallet;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class bitcoinWalletController extends Controller
{

    public function index()
    {
        $hd_wallet = HdWallet::where('currency_id', 1)->first();
        $service_wallet = Wallet::where(['name' => 'service', 'user_id' => 1, 'currency_id' => 1])->first();
        $charges_wallet = Wallet::where(['name' => 'charges', 'user_id' => 1, 'currency_id' => 1])->first();
        $migration_wallet = Wallet::where(['name' => 'migration', 'user_id' => 1, 'currency_id' => 1])->first();

        $client = new Client();
        $url_hd = env('TATUM_URL') . '/ledger/account/' . $hd_wallet->account_id;
        $res_hd = $client->request('GET', $url_hd, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_hd = json_decode($res_hd->getBody());
        $hd_wallet->balance = $res_hd->balance->availableBalance;

        $url_service = env('TATUM_URL') . '/ledger/account/' . $service_wallet->account_id;
        $res_service = $client->request('GET', $url_service, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_service = json_decode($res_service->getBody());
        $service_wallet->balance = $res_service->balance->availableBalance;

        $url_charges = env('TATUM_URL') . '/ledger/account/' . $charges_wallet->account_id;
        $res_charges = $client->request('GET', $url_charges, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_charges = json_decode($res_charges->getBody());
        $charges_wallet->balance = $res_charges->balance->availableBalance;

        $url_migration = env('TATUM_URL') . '/ledger/account/' . $migration_wallet->account_id;
        $res_migration = $client->request('GET', $url_migration, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_migration = json_decode($res_migration->getBody());
        $migration_wallet->balance = $res_migration->balance->availableBalance;


        $url = env('TATUM_URL') . '/ledger/transaction/account?pageSize=50';
        $get_txns = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            "json" => ["id" => $hd_wallet->account_id]
        ]);

        $transactions = json_decode($get_txns->getBody());
        foreach ($transactions as $t) {
            $x = \Str::limit($t->created, 10, '');
            $time = \Carbon\Carbon::parse((int)$x);
            $t->created = $time->setTimezone('Africa/Lagos');
        }
        $address = AdminAddresses::where('crypto_currency_id', 1)->get();
        $crypto_currencies = CryptoCurrency::all();
        return response()->json([
            'service_wallet' => $service_wallet,
            'charges_wallet' => $charges_wallet,
            'migration_wallet' => $migration_wallet,
            'hd_wallet' => $hd_wallet,
            'transactions' => $transactions,
            'address' => $address,
            'crypto_currencies' => $crypto_currencies
    ]);

    }
    //
    public function createHdWallet(Request $data)
    {

        $validate = Validator::make($data->all(), [
            'wallet_password' => 'required|min:10|confirmed',
            'account_password' => 'required',
            'name' => 'required|string|unique:bitcoin_wallets,name'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }



        if (!Hash::check($data['account_password'], Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Wrong Account password'
            ]);
        }
        $password = Hash::make($data['wallet_password']);


        try {
            $result = $this->instance->walletApiBtcCreateAddress()->createHd(Constants::$BTC_MAINNET, $data['name'], $password, 1);
            $wallet = new BitcoinWallet();
            $address = $result->payload->addresses[0];
            $wallet->user_id = Auth::user()->id;
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
            return response()->json(['error' => 'An error occured, please try again']);
        }
        return response()->json(['success' => 'HD wallet created successfully']);
    }

    public function sendFromHd(Request $data)
    {
        // $data = $r->validate([
        //     'primary_wallet' => 'required|integer',
        //     'address' => 'required',
        //     'amount' => 'required',
        //     'wallet_password' => 'required',
        //     /* 'pin' => 'required', */
        //     'account_password' => 'required',
        //     'fees' => 'required'
        // ]);

        $validate = Validator::make($data->all(), [
            'primary_wallet' => 'required|integer',
            'address' => 'required',
            'amount' => 'required',
            'wallet_password' => 'required',
            /* 'pin' => 'required', */
            'account_password' => 'required',
            'fees' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $total = $data['amount'] + $data['fees'];
        $primary_wallet = BitcoinWallet::find($data['primary_wallet']);

        if (!Hash::check($data['wallet_password'], $primary_wallet->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Incorrect pin primary wallet password'
            ]);
        }

        if (!Hash::check($data['account_password'], Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Incorrect account password'
            ]);
        }

        if ($total > $primary_wallet->balance) {
            return response()->json([
                'success' => false,
                'error' => 'Insufficient balance'
            ]);
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
            return response()->json([
                'success' => true,
                'mgs' => 'Bitcoin sent successfully'
            ]);
        } catch (\Exception $e) {
            report($e);
            $primary_wallet->balance = $old_balance;
            $primary_wallet->save();

            $btc_transaction->status = 'failed';
            $btc_transaction->save();
            //set the transaction status to failed

            return response()->json([
                'success' => false,
                'error' => 'An error occured while processing the transaction please confirm the details and try again'
            ]);
        }
    }

    public function setCharge(Request $data)
    {

        $validate = Validator::make($data->all(), [
            'wallet_password' => 'required|min:10|confirmed',
            'account_password' => 'required',
            'name' => 'required|string|unique:bitcoin_wallets,name'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

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

        return response()->json(['success' => 'Bitcoin charge set successfully']);
    }

    public function addAddress(Request $data)
    {
        if ($data->crypto == null) {
            return response([
                'success' => false,
                'error' => 'No Address Selected Try Again'
            ]);
        }

        $validate = Validator::make($data->all(), [
            'crypto' => 'required',
            'address' => 'required',
            'pin' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }


        if (!Hash::check($data['pin'], Auth::user()->pin)) {
            return response()->json([
                'success' => false,
                'error' => 'Incorrect wallet pin'

            ]);
        }

        AdminAddresses::create([
            'crypto_currency_id' =>$data['crypto'],
            'address' => $data['address']
        ]);

        return response()->json([
            'success' => true,
            'mgs' => 'Successfully Added Address'
        ]);
    }

    public function send(Request $data)
    {

        $validate = Validator::make($data->all(), [
            'wallet' => 'required',
            'address' => 'required',
            'btc' => 'required',
            'pin' => 'required',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }


        if (!Hash::check($data['pin'], Auth::user()->pin)) {
            return response()->json([
                'success' => false,
                'error' => 'Incorrect wallet pin'
            ]);
        }

        if (!Hash::check($data['password'], Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Incorrect password'
            ]);
        }

        if ($data->wallet == 'hd') {
            $wallet = HdWallet::where('currency_id', 1)->first();
        } else {
            $wallet = Wallet::find($data->wallet);
        }


        //Get balance
        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . $wallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res = json_decode($res->getBody());
        $wallet->balance = $res->balance->availableBalance;

        if ($data['btc'] > $wallet->balance) {
            return response()->json([
                'success' => false,
                'error' => 'Insufficient balance'
            ]);
        }

        $hd_wallet = HdWallet::where(['currency_id' => 1])->first();
        $url = env('TATUM_URL') . '/offchain/blockchain/estimate';
        /* try { */

        $get_fees = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' =>  [
                "senderAccountId" => $wallet->account_id,
                "address" => $data->address,
                "amount" => $data->btc,
                "xpub" => $hd_wallet->xpub
            ]
        ]);
        $res = json_decode($get_fees->getBody());

        $fees = $res->medium;

        $send_total =  $data->btc - $fees;

        if ($send_total < 0) {
            return response()->json([
                'success' => false,
                'error' => 'Insufficient amount'
            ]);
        }
        //dd(number_format((float) $send_total, 8));
        $send_total = number_format((float) $send_total, 8);
        $fees = number_format((float) $fees, 6);
        /* Send */
        try {
            $url = env('TATUM_URL') . '/offchain/bitcoin/transfer';
            $send_btc = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
                'json' =>  [
                    "senderAccountId" => $wallet->account_id,
                    "address" => $data['address'],
                    "amount" => $send_total,
                    "compliant" => false,
                    "fee" => $fees,
                    "signatureId" => $hd_wallet->signature_id,
                    "xpub" => $hd_wallet->xpub,
                    "senderNote" => "Send BTC"
                ]
            ]);

            $res = json_decode($send_btc->getBody());


            if (Arr::exists($res, 'signatureId')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bitcoin sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'An error occured, please try again'
                ]);

            }
        } catch (\Exception $e) {
            //report($e);
            \Log::info($e->getResponse()->getBody());
            //dd('wait');
            return response()->json([
                'success' => false,
                'error' => 'An error occured while processing the transaction please confirm the details and try again'
            ]);
        }
    }

    public function migrationWallet()
    {

        // dd('test');
        $wallet = Wallet::where(['name' => 'migration', 'user_id' => 1, 'currency_id' => 1])->first();
        $client = new Client();

        $url_migration = env('TATUM_URL') . '/ledger/account/' . $wallet->account_id;
        $res_migration = $client->request('GET', $url_migration, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_migration = json_decode($res_migration->getBody());
        $wallet->balance = $res_migration->balance->availableBalance;


        $url = env('TATUM_URL') . '/ledger/transaction/account?pageSize=50';
        $get_txns = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            "json" => ["id" => $wallet->account_id]
        ]);

        $transactions = json_decode($get_txns->getBody());
        foreach ($transactions as $t) {
            $x = \Str::limit($t->created, 10, '');
            $time = \Carbon\Carbon::parse((int)$x);
            $t->created = $time->setTimezone('Africa/Lagos');
        }


        $pending = BtcMigration::where('status', 'pending')->get();

        return response()->json([
            'transactions'  => $transactions,
            'pending' => $pending,
            'wallet' => $wallet
        ]);
    }

    public function confirmMigration(Request $request, BtcMigration $migration)
    {

        // dd('test');

        if ($migration->status != 'pending') {
            return response()->json([
                'success' => false,
                'error' => 'Invalid transaction'
            ]);
        }


        $wallet = Wallet::where(['name' => 'migration', 'user_id' => 1, 'currency_id' => 1])->first();
        $client = new Client();

        $url_migration = env('TATUM_URL') . '/ledger/account/' . $wallet->account_id;
        $res_migration = $client->request('GET', $url_migration, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_migration = json_decode($res_migration->getBody());
        $wallet->balance = $res_migration->balance->availableBalance;

        if ($migration->amount > $wallet->balance) {
            return response()->json([
                'success' => false,
                'error' => 'Insufficient migration balance'
            ]);
        }

        try {
            $user_wallet = $migration->user->btcWallet;
            $url = env('TATUM_URL') . '/ledger/transaction';

            $send = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
                'json' =>  [
                    "senderAccountId" => $wallet->account_id,
                    "recipientAccountId" => $user_wallet->account_id,
                    "amount" => number_format((float) $migration->amount, 8),
                    "anonymous" => false,
                    "compliant" => false,
                    "transactionCode" => uniqid(),
                    "paymentId" => uniqid(),
                    "baseRate" => 1,
                    "senderNote" => 'Migration transfer',
                ]
            ]);
            $migration->status = 'completed';
            $migration->save();

            $user_wallet->balance = 0;
            $user_wallet->save();
        } catch (\Exception $e) {
            report($e);
            \Log::info($e->getResponse()->getBody());

            return response()->json([
                'success' => false,
                'error' => 'An error occured while processing the transaction please confirm the details and try again'
            ]);
        }

        return response()->json([
            'success' => true,
            'mgs' => 'Transaction completed']);

    }

    public function addTxn(Request $request)
    {


        $validate = Validator::make($request->all(), [
            'wallet' => 'required',
            'address' => 'required',
            'btc' => 'required',
            'pin' => 'required',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }


        $bitcoin_wallet = BitcoinWallet::where('address', $request->address)->first();


        $user = $bitcoin_wallet->user;


        $txn = BitcoinTransaction::where(['hash' => $request->hash, 'wallet_id' => $request->address])->get();
        if ($txn->count() > 0) {
            return response()->json([
                'error' => 'Transaction already exists'
            ]);
        }



        $new_txn = new BitcoinTransaction();
        $new_txn->user_id = $user->id;
        $new_txn->primary_wallet_id = 1;
        $new_txn->wallet_id = $request->address;
        $new_txn->hash = $request->hash;
        $new_txn->credit = $request->amount;
        $new_txn->debit = 0;
        $new_txn->fee = 0;
        $new_txn->charge = 0;
        $new_txn->previous_balance = $bitcoin_wallet->balance;
        $new_txn->current_balance = $bitcoin_wallet->balance + $request->amount;
        $new_txn->transaction_type_id = 22;
        $new_txn->counterparty = Auth::user()->email;
        $new_txn->narration = Auth::user()->first_name;
        $new_txn->confirmations = 7;
        $new_txn->status = 'success';

        $new_txn->save();

        $bitcoin_wallet->balance += $request->amount;
        $bitcoin_wallet->save();

        return response()->json([
            'success' => true,
            'msg' => 'Transaction added'
        ]);
    }

    public function wallets()
    {
        $wallets = BitcoinWallet::where('user_id', '!=', 1)->latest()->get();
        $bitcoin_charge = Setting::where('name', 'bitcoin_charge')->first();
        $bitcoin_buy_charge = Setting::where('name', 'bitcoin_buy_charge')->first();
        $bitcoin_sell_charge = Setting::where('name', 'bitcoin_sell_charge')->first();

        return response()->json([
            'wallets' => $wallets,
            'bitcoin_charge' => $bitcoin_charge,
            'bitcoin_buy_charge' => $bitcoin_buy_charge,
            'bitcoin_sell_charge' => $bitcoin_sell_charge
        ]);
    }

    public function hdWallets(Request $request)
    {
        $hd_wallets = BitcoinWallet::where('type', 'primary')->get();

        if ($request->start && $request->end) {

            $credit_transactions = BitcoinTransaction::where('transaction_type_id', 20)->where('status', 'success')
            ->where('created_at', '>=', $request->start)->where('created_at', '<=', $request->end)->latest()
            ->paginate(200);

            $debit_transactions = BitcoinTransaction::whereIn('transaction_type_id', [19, 21])->where('status', 'success')
            ->where('created_at', '>=', $request->start)->where('created_at', '<=', $request->end);


            $debit_transactions = $debit_transactions->where(['transaction_type_id' => 21, 'user_id' => 1])->latest()->paginate(200);
        } else {
            $credit_transactions = BitcoinTransaction::where('transaction_type_id', 20)->where('status', 'success')->latest()->paginate(200);
            $debit_transactions = BitcoinTransaction::whereIn('transaction_type_id', [19, 21])->where('status', 'success');

            $debit_transactions = $debit_transactions->where(['transaction_type_id' => 21, 'user_id' => 1])->latest()->paginate(200);
        }

        return response()->json([
            'hd_wallets' => $hd_wallets,
            'credit_transactions' => $credit_transactions,
            'debit_transactions' => $debit_transactions,
        ]);
    }

    public function charges()
    {

        $transactions = BitcoinTransaction::where('charge', '!=', 0)->where('status', 'success')->latest()->paginate(200);
        $charges = BitcoinWallet::where('name', 'bitcoin charges')->first()->balance ?? 0;
        $bitcoin_charge = Setting::where('name', 'bitcoin_charge')->first();
        $bitcoin_buy_charge = Setting::where('name', 'bitcoin_buy_charge')->first();
        $bitcoin_sell_charge = Setting::where('name', 'bitcoin_sell_charge')->first();

        return response()->json(['transactions' => $transactions,
            'bitcoin_charge' => $bitcoin_charge,
            'charges' => $charges,
            'bitcoin_buy_charge' => $bitcoin_buy_charge,
            'bitcoin_sell_charge' => $bitcoin_sell_charge,
        ]);
    }

    public function transferCharges(Request $data)
    {

        $validate = Validator::make($data->all(), [
            'fees' => 'required',
            'amount' => 'required',
            'pin' => 'required',
            'address' => 'required',
            'wallet' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }


        if (!Hash::check($data['pin'], Auth::user()->bitcoinWallet->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Incorrect pin'
            ]);
        }

        $total = $data['amount'] + $data['fees'];
        $charges_wallet = BitcoinWallet::where('name', $data['wallet'])->first();
        $primary_wallet = BitcoinWallet::where('type', 'primary')->first();


        if ($total > $charges_wallet->balance) {
            return response()->json([
                'success' => false,
                'error' => 'Insufficient balance'
            ]);
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
            return response()->json([
                'success' => true,
                'mgs' => 'Charges sent successfully'
            ]);
        } catch (\Exception $e) {
            report($e);
            $charges_wallet->balance = $c_old_balance;
            $charges_wallet->save();

            $btc_transaction->status = 'failed';
            $btc_transaction->save();
            //set the transaction status to failed

            return response()->json([
                'success' => false,
                'error' => 'An error occured while processing the transaction please confirm the details and try again'
            ]);
        }


    }

    public function transactions()
    {
        $transactions = BitcoinTransaction::latest()->paginate(200);

        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);
    }

    public function setFee(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'fees' => 'required',

        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
            ], 401);
        }

        $tp = Setting::where('name', 'trading_btc_per')->first();

        $tp->value = $request->fee;
        $tp->save();

        return response()->json([
            'success' => true,
            'msg' => 'Bitcoin fee set successfully']
        );
    }


}
