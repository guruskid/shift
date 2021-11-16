<?php

namespace App\Http\Controllers;

use App\Contract;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TronWalletController extends Controller
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

        if (Auth::user()->tronWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'ETH wallet already exists for this account'
            ]);
        }

        if (!Auth::user()->btcWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please create a BTC wallet before continuing'
            ]);
        }

        $contract = Contract::where(['currency_id' => 5, 'status' => 'pending'])->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'msg' => 'Service not available. Please contact the customer happiness for help'
            ]);
        }

        $client = new Client();
        $url = env('TATUM_URL') . "/ledger/account/batch";

        /* try { */
        $response = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' => [
                "accounts" => [
                    [
                        "currency" => "TRON",
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
        $tron_account_id = $body[0]->id;

        $tron_address = $contract->hash;


        //Link address to ledger account
        $link_eth_url = env('TATUM_URL') . '/offchain/account/'.$tron_account_id.'/address/'.$tron_address;
        $res = $client->request('POST', $link_eth_url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
        ]);

        $contract->status = 'completed';
        $contract->save();

        Auth::user()->tronWallet()->create([
            'account_id' => $tron_account_id,
            'currency_id' => 5,
            'name' => Auth::user()->username,
            'address' => $tron_address,
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Ethereum wallet created successfully'
        ]);

    }

    public function wallet(Request $r)
    {

        if (!Auth::user()->tronWallet) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please a Tron wallet to continue']);
        }

        $tron_rate = LiveRateController::tronRate();
        $tron_wallet = Auth::user()->tronWallet;

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . $tron_wallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);

        $accounts = json_decode($res->getBody());

        $tron_wallet->balance = $accounts->balance->availableBalance;
        $tron_wallet->usd = $tron_wallet->balance  * $tron_rate;

        $url = env('TATUM_URL') . '/ledger/transaction/account?pageSize=50';
        $get_txns = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            "json" => ["id" => Auth::user()->tronWallet->account_id]
        ]);

        $transactions = json_decode($get_txns->getBody());
        foreach ($transactions as $t) {
            $x = \Str::limit($t->created, 10, '');
            $time = \Carbon\Carbon::parse((int)$x);
            $t->created = $time->setTimezone('Africa/Lagos');

            if (!isset($t->senderNote)) {
                $t->senderNote = 'Sending Tron';
            }
        }


        return view('newpages.tron-wallet', compact('tron_wallet', 'transactions', 'tron_rate'));
    }
}
