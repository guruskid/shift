<?php

namespace App\Http\Controllers\Api;

use App\Card;
use App\CardCurrency;
use App\HdWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Setting;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BtcWalletController extends Controller
{
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

        if (Auth::user()->btcWallet->count() > 0) {
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
        $res = json_decode(file_get_contents("https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd"));
        $btc_rate = $res->bitcoin->usd;

        $trading_per = Setting::where('name', 'trading_btc_per')->first()->value;
        $tp = ($trading_per / 100) * $btc_rate;
        $btc_rate -= $tp;

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/customer/' . Auth::user()->customer_id . '?pageSize=50';
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);

        $accounts = json_decode($res->getBody());

        $btc_wallet = Auth::user()->btcWallet;
        $btc_wallet->balance = $accounts[0]->balance->availableBalance;
        $btc_wallet->usd = $btc_wallet->balance  * $btc_rate;


        $sell =  CardCurrency::where(['card_id' => 102, 'currency_id' => $rates->id, 'buy_sell' => 2])->first()->paymentMediums()->first();
        $rates->sell = json_decode($sell->pivot->payment_range_settings);

        $btc_ngn = $btc_wallet->usd * $rates->sell[0]->rate;

        return response()->json([
            'success' => true,
            'btc_wallet' => Auth::user()->btcWallet->address,
            'btc_value' => number_format((float)$btc_wallet->balance, 8),
            'ngn_value' => (int)$btc_ngn,
            'usd_value' => $btc_wallet->usd
        ]);
    }
}
