<?php

namespace App\Http\Controllers;

use App\Contract;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EthWalletController extends Controller
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

        if (Auth::user()->ethWallet) {
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

        $contract = Contract::where(['currency_id' => 2, 'status' => 'pending'])->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'msg' => 'Service not available. Please contact the customer care for help'
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
                        "currency" => "ETH",
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
        $eth_account_id = $body[0]->id;

        $eth_hash = $contract->hash;

        //Get address form the txn hash
        $eth_address_url = env('TATUM_URL') . "/blockchain/sc/address/ETH/".$eth_hash;
        $res = $client->request('GET', $eth_address_url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
        ]);

        $get_eth_address_res = json_decode($res->getBody());
        $eth_address = $get_eth_address_res->contractAddress;

        //Link address to ledger account
        $link_eth_url = env('TATUM_URL') . '/offchain/account/'.$eth_account_id.'/address/'.$eth_address;
        $res = $client->request('POST', $link_eth_url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
        ]);

        $contract->status = 'completed';
        $contract->save();


        Auth::user()->ethWallet()->create([
            'account_id' => $eth_account_id,
            'currency_id' => 2,
            'name' => Auth::user()->username,
            'address' => $eth_address,
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Ethereum wallet created successfully'
        ]);

    }
}
