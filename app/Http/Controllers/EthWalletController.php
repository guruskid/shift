<?php

namespace App\Http\Controllers;

use App\Contract;
use App\FeeWallet;
use App\HdWallet;
use App\Setting;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

        if (!Auth::user()->ethWallet) {
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
        $eth_address_url = env('TATUM_URL') . "/blockchain/sc/address/ETH/" . $eth_hash;
        $res = $client->request('GET', $eth_address_url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
        ]);

        $get_eth_address_res = json_decode($res->getBody());
        $eth_address = $get_eth_address_res->contractAddress;

        //Link address to ledger account
        $link_eth_url = env('TATUM_URL') . '/offchain/account/' . $eth_account_id . '/address/' . $eth_address;
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

    public function wallet(Request $r)
    {

        if (!Auth::user()->ethWallet) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please an Ethereum wallet to continue']);
        }

        $eth_rate = LiveRateController::ethRate();
        $eth_wallet = Auth::user()->ethWallet;

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . $eth_wallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);

        $accounts = json_decode($res->getBody());

        $eth_wallet->balance = $accounts->balance->availableBalance;
        $eth_wallet->usd = $eth_wallet->balance  * $eth_rate;

        $url = env('TATUM_URL') . '/ledger/transaction/account?pageSize=50';
        $get_txns = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            "json" => ["id" => Auth::user()->ethWallet->account_id]
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


        return view('newpages.ethereum-wallet', compact('eth_wallet', 'transactions', 'eth_rate'));
    }

    public function fees($address, $amount)
    {
        $client = new Client();
        $amount = number_format((float) $amount, 5);

        $url = env('TATUM_URL') . '/ethereum/gas';

        $get_fees = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' =>  [
                "from" => Auth::user()->ethWallet->address,
                "to" => $address,
                "amount" => $amount,
            ]
        ]);

        $res = json_decode($get_fees->getBody());
        $fees = ($res->gasPrice * 100000) / 1e18;

        $charge = Setting::where('name', 'ethereum_charge')->first()->value;
        $res = json_decode($get_fees->getBody());
        return response()->json([
            "fee" => $fees + $charge,
        ]);
    }


    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|min:0',
            'address' => 'required|string',
            'pin' => 'required',
            'fees' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        if (!Auth::user()->ethWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please create an Ethereum wallet to continue'
            ]);
        }

        //Check password
        if (!Hash::check($request->pin, Auth::user()->pin)) {
            return response()->json([
                'success' => false,
                'msg' => 'Incorrect wallet pin'
            ]);
        }

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . Auth::user()->ethWallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);

        $accounts = json_decode($res->getBody());

        $user_wallet = Auth::user()->ethWallet;
        $user_wallet->balance = $accounts->balance->availableBalance;

        $hd_wallet = HdWallet::where(['currency_id' => 2])->first();

        if ($request->amount > $user_wallet->balance) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient balance'
            ]);
        }

        $fees_wallet = FeeWallet::where(['crypto_currency_id' => 2, 'name' => 'eth_fees'])->first();
        $charge_wallet = FeeWallet::where(['crypto_currency_id' => 2, 'name' => 'eth_charge'])->first();

        $url = env('TATUM_URL') . '/ethereum/gas';
        $get_fees = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' =>  [
                "from" => Auth::user()->ethWallet->address,
                "to" => $request->address,
                "amount" => number_format((float)$request->amount,8),
            ]
        ]);

        $res = json_decode($get_fees->getBody());

        $fees = ($res->gasPrice * 100000) / 1e18;
        $charge = Setting::where('name', 'ethereum_charge')->first()->value;

        $total = $request->amount - $charge - $fees;

        if ($total <= 0) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient transfer amount when fee was deducted'
            ]);
        }

        //Store Withdrawal
        $url = env('TATUM_URL') . '/offchain/withdrawal';
        $store = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' =>  [
                "senderAccountId" => Auth::user()->ethWallet->account_id,
                "address" => $request->address,
                "amount" => number_format((float) $request->amount, 8),
                "compliant" => false,
                "fee" => "0",
                "paymentId" => uniqid(),
                "senderNote" => "Sending ETH 1"
            ]
        ]);

        $store_res = json_decode($store->getBody());
        if ($store->getStatusCode() != 200) {
            return response()->json([
                'success' => false,
                'msg' => 'An error occured while withdrawing'
            ]);
        }


        try {
            $url = env('TATUM_URL') . '/blockchain/sc/custodial/transfer/batch';
            $send = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
                'json' =>  [
                    "chain" => "ETH",
                    "custodialAddress" => Auth::user()->ethWallet->address,
                    "contractType" => [3, 3, 3],
                    "recipient" => [$request->address, $fees_wallet->address, $charge_wallet->address],
                    "amount" => [number_format((float) $total, 8), number_format((float) $fees, 8), number_format((float) $charge, 8)],
                    "signatureId" => $hd_wallet->private_key,
                    "tokenId" => ["0", "0", "0"],
                    "tokenAddress" => ["0", "0", "0"]
                ]
            ]);

            $send_res = json_decode($send->getBody());

            if (Arr::exists($send_res, 'signatureId')) {
                return response()->json(['success' => true, 'msg' => 'ETH transferred successfully']);
            } else {
                //Cancel TXN
                $cancel =  Http::withHeaders(['x-api-key' => env('TATUM_KEY')])
                    ->delete(env('TATUM_URL') . '/offchain/withdrawal/' . $store_res->id);
                return $send_res;
            }
        } catch (\Exception $e) {
            report($e);
            $cancel = $client->request('delete', env('TATUM_URL') . '/offchain/withdrawal/' . $store_res->id, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
            ]);

            return response()->json(['success' => false, 'msg' => 'An error occured, please try againss']);
        }
    }
}
