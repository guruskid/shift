<?php

namespace App\Http\Controllers;

use App\HdWallet;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BnbWalletController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'pin' => 'required|min:4|max:4',
        ]);

        if (!Hash::check($request->pin, Auth::user()->pin)) {
            return response()->json([
                'success' => false,
                'msg' => 'Incorrect wallet pin'
            ]);
        }

        if (Auth::user()->bnbWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'BNB wallet already exists for this account'
            ]);
        }

        if (!Auth::user()->btcWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please create a BTC wallet before continuing'
            ]);
        }


        $client = new Client();
        $url = env('TATUM_URL') . "/ledger/account/batch";
        $bnb_hd = HdWallet::where('currency_id', 4)->first();

        /* try { */
        $response = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' => [
                "accounts" => [
                    [
                        "currency" => "BNB",
                        "xpub" => $bnb_hd->xpub,
                        "customer" => [
                            "accountingCurrency" => "USD",
                            "customerCountry" => "NG",
                            "externalId" => Auth::user()->external_id,
                            "providerCountry" => "NG"
                        ],
                        "compliant" => false,
                        "accountingCurrency" => "USD"
                    ]
                ]
            ],
        ]);

        $body = json_decode($response->getBody());
        $bnb_account_id = $body[0]->id;

        $address_url = env('TATUM_URL') . "/offchain/account/address/batch";
        $res = $client->request('POST', $address_url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' => [
                "addresses" => [
                    [
                        "accountId" => $bnb_account_id,
                    ]
                ]
            ],
        ]);

        $address_body = json_decode($res->getBody());


        Auth::user()->ethWallet()->create([
            'account_id' => $bnb_account_id,
            'currency_id' => 4,
            'name' => Auth::user()->username,
            'address' => $address_body[0]->address,
            'pin' => $address_body[0]->memo
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Binance Coin wallet created successfully'
        ]);
    }


    public function wallet(Request $r)
    {

        if (!Auth::user()->bnbWallet) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please an Ethereum wallet to continue']);
        }

        $bnb_rate = LiveRateController::ethRate();
        $bnb_wallet = Auth::user()->bnbWallet;

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . $bnb_wallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);

        $accounts = json_decode($res->getBody());
        // dd($accounts);

        $bnb_wallet->balance = $accounts->balance->availableBalance;
        $bnb_wallet->usd = $bnb_wallet->balance  * $bnb_rate;

        $url = env('TATUM_URL') . '/ledger/transaction/account?pageSize=50';
        $get_txns = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            "json" => ["id" => Auth::user()->bnbWallet->account_id]
        ]);

        $transactions = json_decode($get_txns->getBody());
        foreach ($transactions as $t) {
            $x = \Str::limit($t->created, 10, '');
            $time = \Carbon\Carbon::parse((int)$x);
            $t->created = $time->setTimezone('Africa/Lagos');

            if (!isset($t->senderNote)) {
                $t->senderNote = 'Sending ETH';
            }
        }


        return view('newpages.binance-wallet', compact('bnb_wallet', 'transactions', 'bnb_rate'));
    }
}
