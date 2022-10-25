<?php

namespace App\Http\Controllers\Admin;

use App\AdminAddresses;
use App\Contract;
use App\CryptoCurrency;
use App\CryptoRate;
use App\FeeWallet;
use App\HdWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CryptoHelperController;
use App\Http\Controllers\LiveRateController;
use App\Transaction;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsdtController extends Controller
{
    public function index()
    {

        $client = new Client();

        $hd_wallet = HdWallet::where('currency_id', 7)->first();
        $url_hd = env('TATUM_URL') . '/ledger/account/' . $hd_wallet->account_id;
        $res_hd = $client->request('GET', $url_hd, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
        ]);
        $res_hd = json_decode($res_hd->getBody());
        $hd_wallet->balance = $res_hd->balance->availableBalance;
        // dd($res_hd);



        $charges_wallet = FeeWallet::where(['crypto_currency_id' => 7, 'name' => 'usdt_charge'])->first();
        $url_charges = env('TATUM_URL') . '/ledger/account/' . $charges_wallet->account_id;
        $res_charges = $client->request('GET', $url_charges, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
        ]);
        $res_charges = json_decode($res_charges->getBody());
        $charges_wallet->balance = $res_charges->balance->availableBalance;



        $service_wallet = FeeWallet::where(['crypto_currency_id' => 7, 'name' => 'usdt_service'])->first();
        $url_service = env('TATUM_URL') . '/ledger/account/' . $service_wallet->account_id;
        $res_service = $client->request('GET', $url_service, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
        ]);
        $res_service = json_decode($res_service->getBody());
        $service_wallet->balance = $res_service->balance->availableBalance;

        $blockchain_fee_wallet = FeeWallet::where('name', 'usdt_fees')->first();
        $blockchain_fee_wallet->balance = CryptoHelperController::feeWalletBalance(7);


        $addresses = Contract::where(['currency_id' => 7, 'status' => 'pending', 'type' => 'address'])->count();


        $url = env('TATUM_URL') . '/ledger/transaction/account?pageSize=50';
        $get_txns = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
            "json" => ["id" => $hd_wallet->account_id]
        ]);

        $transactions = json_decode($get_txns->getBody());
        foreach ($transactions as $t) {
            $x = \Str::limit($t->created, 10, '');
            $time = \Carbon\Carbon::parse((int)$x);
            $t->created = $time->setTimezone('Africa/Lagos');
        }
        $address = AdminAddresses::where('crypto_currency_id', 7)->get();
        $crypto_currencies = CryptoCurrency::all();
        return view('admin.usdt.index', compact('service_wallet', 'addresses', 'blockchain_fee_wallet', 'charges_wallet', 'crypto_currencies', 'address', 'hd_wallet', 'transactions'));
    }

    public function settings(Request $request)
    {
        $sell_rate = CryptoRate::where(['crypto_currency_id' => 2, 'type' => 'sell'])->first()->rate ?? 0;
        $buy_rate = LiveRateController::usdtBuy();

        if ($request->start and $request->end) {
            $sell_transactions = Transaction::where('card_id', 143)->where('created_at', '>=', $request->start)
                ->where('created_at', '<=', $request->end)->where('status', 'success')->get();

            $sell_btc = $sell_transactions->sum('quantity');
            $sell_usd = $sell_transactions->sum('amount');

            $ngn_sell_average = 0;
            $cumulative = 0;
            foreach ($sell_transactions as $t) {
                $cumulative += ($t->quantity * $t->ngn_rate * $t->card_price);
            }
            $ngn_sell_average = ($cumulative == 0 ? 1 : $cumulative) / ($sell_usd == 0 ? 1 : $sell_usd);

            return view('admin.usdt.settings', compact('sell_rate', 'buy_rate', 'ngn_sell_average'));
        }

        return view('admin.usdt.settings', compact('sell_rate', 'buy_rate'));
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|min:0',
            'wallet' => 'required',
            'address' => 'required|string',
            'pin' => 'required',
            'password' => 'required'
        ]);


        //Check pin
        if (!Hash::check($request->pin, Auth::user()->pin)) {
            return back()->with(['error' => 'Incorrect wallet pin']);
        }

        if (!Hash::check($data['password'], Auth::user()->password)) {
            return back()->with(['error' => 'Incorrect password']);
        }

        if ($request->wallet == 'hd') {
            $wallet = HdWallet::where('currency_id', 7)->first();
        } else {
            $wallet = FeeWallet::find($request->wallet);
        }

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . $wallet->account_id;

        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
        ]);

        $accounts = json_decode($res->getBody());


        $wallet->balance = $accounts->balance->availableBalance;

        $hd_wallet = HdWallet::where(['currency_id' => 7])->first();
        $fees_wallet = FeeWallet::where(['crypto_currency_id' => 7, 'name' => 'usdt_fees'])->first();

        if ($request->amount  > $wallet->balance) {
            return back()->with(['error' => 'Insufficient balance']);
        }

        //Store Withdrawal
        $url = env('TATUM_URL') . '/offchain/withdrawal';
        $store = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
            'json' =>  [
                "senderAccountId" => $wallet->account_id,
                "address" => $request->address,
                "amount" => $request->amount,
                "compliant" => false,
                "fee" => "0",
                "paymentId" => uniqid(),
                "senderNote" => "Sending USDT"
            ]
        ]);

        $store_res = json_decode($store->getBody());
        if ($store->getStatusCode() != 200) {
            return back()->with(['error' => 'An error occured while withdrawing']);
        }

        try {
            $url = env('TATUM_URL') . '/blockchain/sc/custodial/transfer';
            $send = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
                'json' =>  [
                    "chain" => "TRON",
                    "custodialAddress" => $wallet->address,
                    "contractType" => 0,
                    "recipient" => $request->address,
                    "amount" => $request->amount,
                    "signatureId" => $hd_wallet->private_key,
                    "tokenAddress" => "TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t",
                    "tokenId" => '0',
                    "from" => $fees_wallet->address,
                    "feeLimit" => 100,
                ]
            ]);

            $send_res = json_decode($send->getBody());

            if (isset($send_res->signatureId)) {
                return back()->with(['success' => 'USDT sent successfully']);
            } else {
                //Cancel TXN
                $client->request('delete', env('TATUM_URL') . '/offchain/withdrawal/' . $store_res->id, [
                    'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
                ]);
                return back()->with(['error' => 'A blockchain error occured, please try again']);
            }
        } catch (\Exception $e) {
            report($e);
            $client->request('delete', env('TATUM_URL') . '/offchain/withdrawal/' . $store_res->id, [
                'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
            ]);

            return back()->with(['error' => 'An error occured, please try again']);
        }
    }



    public function updateRate(Request $request)
    {
        CryptoRate::updateOrCreate(
            ['crypto_currency_id' => 2, 'type' => 'sell'],
            ['rate' => $request->rate]
        );

        CryptoRate::updateOrCreate(
            ['crypto_currency_id' => 7, 'type' => 'buy'],
            ['rate' => $request->buy_rate]
        );

        return redirect()->route('admin.tether.settings')->with(['success' => 'Rate updated']);
    }

    public function contracts()
    {
        $fees_wallet = FeeWallet::where('name', 'usdt_fees')->first();
        $fees_wallet->balance = CryptoHelperController::feeWalletBalance(7);

        $addresses = Contract::where(['currency_id' => 7, 'status' => 'pending', 'type' => 'address'])->count();

        $pending_transactions = Contract::where(['currency_id' => 7, 'status' => 'pending', 'type' => 'transaction'])->get();

        return view('admin.usdt.contracts', compact('fees_wallet', 'addresses', 'pending_transactions'));
    }

    public function deployContract(Request $request)
    {
        $fees_wallet = FeeWallet::where('name', 'usdt_fees')->first();
        $hd = HdWallet::where('currency_id', 7)->first();
        $amount = $request->count;

        $hd->from += $amount;
        $hd->to += $amount;
        $hd->save();

        $client = new Client();

        $url_contract = env('TATUM_URL') . '/gas-pump';
        try {
            $res_contract = $client->request('POST', $url_contract, [
                'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
                'json' =>  [
                    "chain" => "TRON",
                    "owner" => $fees_wallet->address,
                    "from" => (int)$hd->from,
                    "to" => (int)$hd->to,
                ]
            ]);
        } catch (\Exception $e) {
            report($e);
            return back()->with(['error' => 'An error occured while generating addresses']);
        }

        $res = json_decode($res_contract->getBody());
        $count = 0;
        foreach ($res as $r) {
            // check if address already exists
            if (!Contract::where('hash', $r)->exists()) {
                Contract::create([
                    'hash' => $r,
                    'index' => $hd->from + $count,
                    'type' => 'address',
                    'currency_id' => 7
                ]);
                $count++;
            }
        }

        return back()->with(['success' => $count . ' addresses created successfully']);
    }


    public function activate($id) //Deprecated
    {
        $contract = Contract::find($id);

        $client = new Client();

        try {

            $url_contract = env('TATUM_URL') . '/blockchain/sc/custodial/TRON/' . $contract->hash;
            $res_contract = $client->request('GET', $url_contract, [
                'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
            ]);
        } catch (\Exception $e) {
            report($e);
            return back()->with(['error' => 'An error occured while activating the contract. Please confirm if the transaction has been signed']);
        }

        $res = json_decode($res_contract->getBody());
        $count = 0;
        foreach ($res as $r) {
            // check if address already exists
            if (!Contract::where('hash', $r)->exists()) {
                Contract::create([
                    'hash' => $r,
                    'type' => 'address',
                    'currency_id' => 7
                ]);
                $count++;
            }
        }

        $contract->status = 'completed';
        $contract->save();

        return back()->with(['success' => $count . ' addresses created successfully']);
    }
}
