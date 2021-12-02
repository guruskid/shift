<?php

namespace App\Http\Controllers\Api;

use App\Card;
use App\CardCurrency;
use App\CryptoRate;
use App\HdWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LiveRateController;
use App\Setting;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BtcWalletController extends Controller
{
    public function btcPrice()
    {

        $client = new Client();
        // $url = env('TATUM_URL') . '/tatum/rate/BTC?basePair=USD';
        // $res = $client->request('GET', $url, [ 'headers' => ['x-api-key' => env('TATUM_KEY')] ]);
        // $res = json_decode($res->getBody());
        // $btc_rate = (int)$res->value;

        // $trading_per = Setting::where('name', 'trading_btc_per')->first()->value;
        // $tp = ($trading_per / 100) * $btc_rate;
        // $btc_rate -= $tp;
        $btc_rate = LiveRateController::btcRate();

        $usd_ngn = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;

        return response()->json([
            'success' => true,
            'btc_usd' => $btc_rate,
            'usd_ngn' => $usd_ngn
        ]);
    }

    public function fees()
    {
        $address = HdWallet::where('currency_id', 1)->first()->address;
        $amount = 0.002;
        $client = new Client();
        $hd_wallet = HdWallet::where(['currency_id' => 1])->first();
        $amount = number_format((float) $amount, 8);

        $url = env('TATUM_URL') . '/offchain/blockchain/estimate';

        $get_fees = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' =>  [
                "senderAccountId" => Auth::user()->btcWallet->account_id,
                "address" => $address,
                "amount" => $amount,
                "xpub" => $hd_wallet->xpub
            ]
        ]);


        $charge = Setting::where('name', 'bitcoin_charge')->first()->value;
        $res = json_decode($get_fees->getBody());
        $total_fees = $charge + $res->medium;

        $url = env('TATUM_URL') . '/tatum/rate/BTC?basePair=USD';
        $res = $client->request('GET', $url, [ 'headers' => ['x-api-key' => env('TATUM_KEY')] ]);
        $res = json_decode($res->getBody());
        $btc_rate = (int)$res->value;

        return response()->json([
            'success' => true,
            'send_fee' => $total_fees,
            'btc_to_usd' => $btc_rate,
        ]);
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

        if (Auth::user()->btcWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'A Bitcoin wallet exists for this account'
            ]);
        }

        $password = Hash::make($r->wallet_password);
        $user = Auth::user();
        $external_id = $user->email . '-' . uniqid();

        $btc_hd = HdWallet::where('currency_id', 1)->first();
        $btc_xpub = $btc_hd->xpub;

        $client = new Client();
        $url = env('TATUM_URL') . "/ledger/account/batch";

        /* try { */
        $response = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' => [
                "accounts" => [
                    [
                        "currency" => "BTC",
                        "xpub" => $btc_xpub,
                        "customer" => [
                            "accountingCurrency" => "USD",
                            "customerCountry" => "NG",
                            "externalId" => $external_id,
                            "providerCountry" => "NG"
                        ],
                        "compliant" => false,
                        "accountingCurrency" => "USD"
                    ]
                ]
            ],
        ]);

        $body = json_decode($response->getBody());


        $btc_account_id = $body[0]->id;
        $user->customer_id = $body[0]->customerId;
        $user->pin = $password;
        $user->external_id = $external_id;
        $user->save();

        $address_url = env('TATUM_URL') . "/offchain/account/address/batch";
        $res = $client->request('POST', $address_url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' => [
                "addresses" => [
                    ["accountId" => $btc_account_id]
                ]
            ],
        ]);

        $address_body = json_decode($res->getBody());

        $user->btcWallet()->create([
            'account_id' => $btc_account_id,
            'name' => $user->username,
            'currency_id' => 1,
            'address' => $address_body[0]->address,
        ]);
        return response()->json([
            'success' => true,
        ]);
    }


    public function balance()
    {
        if (!Auth::user()->btcWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'No bitcoin wallet exists for this account'
            ]);
        }
        $card = Card::find(102);
        $rates = $card->currency->first();

        $client = new Client();
        // $url = env('TATUM_URL') . '/tatum/rate/BTC?basePair=USD';
        // $res = $client->request('GET', $url, [ 'headers' => ['x-api-key' => env('TATUM_KEY')] ]);
        // $res = json_decode($res->getBody());
        // $btc_rate = (int)$res->value;


        // $trading_per = Setting::where('name', 'trading_btc_per')->first()->value;
        // $tp = ($trading_per / 100) * $btc_rate;
        // $btc_rate -= $tp;
        $btc_rate = LiveRateController::btcRate();


        $url = env('TATUM_URL') . '/ledger/account/customer/' . Auth::user()->customer_id . '?pageSize=50';
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);

        $accounts = json_decode($res->getBody());

        $btc_wallet = Auth::user()->btcWallet;
        $btc_wallet->balance = $accounts[0]->balance->availableBalance;
        $btc_wallet->usd = $btc_wallet->balance  * $btc_rate;


        
        $btc_ngn = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;

        return response()->json([
            'success' => true,
            'btc_wallet' => Auth::user()->btcWallet->address,
            'btc_value' => number_format((float)$btc_wallet->balance, 8),
            'ngn_value' => (int)$btc_ngn,
            'usd_value' => $btc_wallet->usd
        ]);
    }

    public function transactions()
    {
        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/transaction/account?pageSize=50';
        $get_txns = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            "json" => ["id" => Auth::user()->btcWallet->account_id]
        ]);

        $transactions = json_decode($get_txns->getBody());
        foreach ($transactions as $t) {
            $x = \Str::limit($t->created, 10, '');
            $time = \Carbon\Carbon::parse((int)$x);
            $t->created_at = $time->setTimezone('Africa/Lagos');
            $t->created_at = $t->created_at->format('d M, y');

            if (!isset($t->senderNote)) {
                $t->senderNote = 'Sending BTC';
            }
        }

        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);
    }
}
