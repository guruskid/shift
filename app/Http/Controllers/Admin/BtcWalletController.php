<?php

namespace App\Http\Controllers\Admin;

use App\BtcMigration;
use App\HdWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Wallet;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BtcWalletController extends Controller
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

        return view('admin.bitcoin_wallet.index', compact('service_wallet', 'charges_wallet', 'migration_wallet', 'hd_wallet', 'transactions'));
    }


    //Migration Wallet Starts here

    public function migrationWallet()
    {
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

        return view('admin.bitcoin_wallet.migration', compact('transactions', 'pending', 'wallet'));
    }


    public function confirmMigration(Request $request, BtcMigration $migration)
    {

        if ($migration->status != 'pending') {
            return back()->with(['error' => 'Invalid transaction']);
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
            return back()->with(['error' => 'Insufficient migration balance']);
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

            return back()->with(['error' => 'An error occured while processing the transaction please confirm the details and try again']);
        }

        return back()->with(['success' => 'Transaction completed']);

    }

    /* Migration Wallet ends here */

    public function send(Request $request)
    {
        $data = $request->validate([
            'wallet' => 'required',
            'address' => 'required',
            'btc' => 'required',
            'pin' => 'required',
            'password' => 'required',

        ]);

        if (!Hash::check($data['pin'], Auth::user()->pin)) {
            return back()->with(['error' => 'Incorrect wallet pin']);
        }

        if (!Hash::check($data['password'], Auth::user()->password)) {
            return back()->with(['error' => 'Incorrect password']);
        }

        if ($request->wallet == 'hd') {
            $wallet = HdWallet::where('currency_id', 1)->first();
        } else {
            $wallet = Wallet::find($request->wallet);
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
            return back()->with(['error' => 'Insufficient balance']);
        }

        $hd_wallet = HdWallet::where(['currency_id' => 1])->first();
        $url = env('TATUM_URL') . '/offchain/blockchain/estimate';
        /* try { */

        $get_fees = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' =>  [
                "senderAccountId" => $wallet->account_id,
                "address" => $request->address,
                "amount" => $request->btc,
                "xpub" => $hd_wallet->xpub
            ]
        ]);
        $res = json_decode($get_fees->getBody());

        $fees = $res->fast;

        $send_total =  $request->btc - $fees;

        if ($send_total < 0) {
            return back()->with(['error' => 'Insufficient amount']);
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
                return back()->with(['success' => 'Bitcoin sent successfully']);
            } else {
                return back()->with(['error' => 'An error occured, please try again']);
            }
        } catch (\Exception $e) {
            //report($e);
            \Log::info($e->getResponse()->getBody());
            //dd('wait');
            return back()->with(['error' => 'An error occured while processing the transaction please confirm the details and try again']);
        }
    }
}
