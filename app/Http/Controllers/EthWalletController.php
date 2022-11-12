<?php

namespace App\Http\Controllers;

use App\Contract;
use App\CryptoRate;
use App\FeeWallet;
use App\HdWallet;
use App\NairaTransaction;
use App\NairaWallet;
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

        if (!Auth::user()->btcWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please create a BTC wallet before continuing'
            ]);
        }


        $client = new Client();
        $url = env('TATUM_URL') . "/ledger/account/batch";
        $eth_hd = HdWallet::where('currency_id', 2)->first();

        /* try { */
        $response = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' => [
                "accounts" => [
                    [
                        "currency" => "ETH",
                        "xpub" => $eth_hd->xpub,
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

        $address_url = env('TATUM_URL') . "/offchain/account/address/batch";
        $res = $client->request('POST', $address_url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' => [
                "addresses" => [
                    [
                        "accountId" => $eth_account_id,
                    ]
                ]
            ],
        ]);

        $address_body = json_decode($res->getBody());


        Auth::user()->ethWallet()->create([
            'account_id' => $eth_account_id,
            'currency_id' => 2,
            'name' => Auth::user()->username,
            'address' => $address_body[0]->address,
            'pin' => $address_body[0]->derivationKey
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

    public function trade()
    {
        $sell_rate = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;
        $eth_usd = LiveRateController::ethRate();
        $eth_wallet = Auth::user()->ethWallet;
        $charge = Setting::where('name', 'ethereum_sell_charge')->first()->value;

        $trading_per = Setting::where('name', 'trading_eth_per')->first()->value;
        $tp = ($trading_per / 100) * $eth_usd;

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . $eth_wallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);

        $accounts = json_decode($res->getBody());

        $eth_wallet->balance = $accounts->balance->availableBalance;
        $eth_wallet->usd = $eth_wallet->balance  * $eth_usd;

        $hd_wallet = HdWallet::where('currency_id', 2)->first()->address;

        return view('newpages.trade_ethereum', compact('sell_rate', 'eth_usd', 'hd_wallet', 'charge'));
    }

    public function fees($address, $amount)
    {
        $client = new Client();
        $amount = number_format((float) $amount, 5);

        $url = env('TATUM_URL') . '/ethereum/gas';
        $hd_wallet = HdWallet::where('currency_id', 2)->first();
        $get_fees = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' =>  [
                "from" => Auth::user()->ethWallet->address ?? $hd_wallet->address,
                "to" => $address,
                "amount" => $amount,
            ]
        ]);

        $res = json_decode($get_fees->getBody());
        $fees = ($res->gasPrice * 100000) / 1e18;

        $charge = Setting::where('name', 'ethereum_send_charge')->first()->value;
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

        $charge_wallet = FeeWallet::where(['crypto_currency_id' => 2, 'name' => 'eth_charge'])->first();

        $url = env('TATUM_URL') . '/ethereum/gas';
        $get_fees = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' =>  [
                "from" => Auth::user()->ethWallet->address,
                "to" => $request->address,
                "amount" => number_format((float)$request->amount, 8),
            ]
        ]);

        $res = json_decode($get_fees->getBody());

        $fees = ($res->gasPrice * 100000) / 1e18;
        $charge = Setting::where('name', 'ethereum_send_charge')->first()->value;

        $total = $request->amount - $charge + $fees;
        $send_total = $request->amount - $charge;

        if ($total <= 0) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient transfer amount when fee was deducted'
            ]);
        }

        if ($total > $user_wallet->balance) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient balance'
            ]);
        }

        try {
            $url = env('TATUM_URL') . '/offchain/ethereum/transfer';
            $send_eth = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
                'json' =>  [
                    "senderAccountId" => Auth::user()->ethWallet->account_id,
                    "address" => $request->address,
                    "amount" => number_format((float) $send_total, 8),
                    "compliant" => false,
                    "signatureId" => $hd_wallet->signature_id,
                    "index" => Auth::user()->ethWallet->pin,
                    "senderNote" => "Send ETH"
                ]
            ]);

            if ($charge > 0) {
                $url = env('TATUM_URL') . '/ledger/transaction';
                $send_charges = $client->request('POST', $url, [
                    'headers' => ['x-api-key' => env('TATUM_KEY')],
                    'json' =>  [
                        "senderAccountId" => Auth::user()->ethWallet->account_id,
                        "recipientAccountId" => $charge_wallet->account_id,
                        "amount" => number_format((float) $charge, 8),
                        "anonymous" => false,
                        "compliant" => false,
                        "transactionCode" => uniqid(),
                        "paymentId" => uniqid(),
                        "baseRate" => 1,
                        "senderNote" => 'hidden'
                    ]
                ]);
            }

            $res = json_decode($send_eth->getBody());


            if (Arr::exists($res, 'signatureId')) {
                return response()->json([
                    'success' => true,
                    'msg' => 'Ethereum sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'msg' => 'An error occured, please try again'
                ]);
            }
        } catch (\Exception $e) {
            //report($e);
            \Log::info($e->getResponse()->getBody());
            return response()->json([
                'success' => false,
                'msg' => 'An error occured while processing the transaction, please confirm the details and try again'
            ]);
        }
    }

    public function sell(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|min:0',
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


        $charges_wallet = FeeWallet::where(['crypto_currency_id' => 2, 'name' => 'eth_charge'])->first();
        $service_wallet = FeeWallet::where(['crypto_currency_id' => 2, 'name' => 'eth_service'])->first();

        // percentage deduction in price
        $trading_per = Setting::where('name', 'trading_eth_per')->first()->value;
        $service_fee = ($trading_per / 100) * $request->amount;


        //percentage charge
        $charge = Setting::where('name', 'ethereum_sell_charge')->first()->value;
        $charge = ($charge / 100) * $request->amount;

        //Current eth price
        $eth_usd = LiveRateController::ethRate();
        $usd_ngn = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;

        $total = $request->amount - $charge  - $service_fee;
        $usd = $request->amount * $eth_usd;
        $ngn = $usd * $usd_ngn;

        if ($total <= 0) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient trade amount when fee was deducted'
            ]);
        }

        $url = env('TATUM_URL') . '/ledger/transaction';
        try {
            $send = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
                'json' =>  [
                    "senderAccountId" => Auth::user()->ethWallet->account_id,
                    "recipientAccountId" => $hd_wallet->account_id,
                    "amount" => number_format((float) $request->amount, 8),
                    "anonymous" => false,
                    "compliant" => false,
                    "transactionCode" => uniqid(),
                    "paymentId" => uniqid(),
                    "baseRate" => 1,
                ]
            ]);

            if ($charge > 0.0000001) {
                $send_charge = $client->request('POST', $url, [
                    'headers' => ['x-api-key' => env('TATUM_KEY')],
                    'json' =>  [
                        "senderAccountId" => $hd_wallet->account_id,
                        "recipientAccountId" => $charges_wallet->account_id,
                        "amount" => number_format((float) $charge, 9),
                        "anonymous" => false,
                        "compliant" => false,
                        "transactionCode" => uniqid(),
                        "paymentId" => uniqid(),
                        "baseRate" => 1,
                        "senderNote" => 'hidden'
                    ]
                ]);
            }

            if ($service_fee > 0.0000001) {
                $send_service = $client->request('POST', $url, [
                    'headers' => ['x-api-key' => env('TATUM_KEY')],
                    'json' =>  [
                        "senderAccountId" => $hd_wallet->account_id,
                        "recipientAccountId" => $service_wallet->account_id,
                        "amount" => number_format((float) $service_fee, 9),
                        "anonymous" => false,
                        "compliant" => false,
                        "transactionCode" => uniqid(),
                        "paymentId" => uniqid(),
                        "baseRate" => 1,
                        "senderNote" => 'hidden'
                    ]
                ]);
            }

            $send_res = json_decode($send->getBody());
        } catch (\Exception $e) {
            report($e);
            return response()->json(['success' => false, 'msg' => 'An error occured, please try again']);
        }

        $t = Auth::user()->transactions()->create([
            'card_id' => 137,
            'type' => 'sell',
            'amount' => $usd,
            'amount_paid' => $ngn,
            'quantity' => number_format((float) $request->amount, 8),
            'card_price' => $eth_usd,
            'status' => 'success',
            'uid' => uniqid(),
            'user_email' => Auth::user()->email,
            'card' => 'ethereum',
            'agent_id' => 1
        ]);
        $systemBalance = NairaWallet::sum('amount');
        $user_naira_wallet = Auth::user()->nairaWallet;
        $user = Auth::user();
        $reference = \Str::random(2) . '-' . $t->id;
        $n = NairaWallet::find(1);

        Auth::user()->nairaWallet->amount += $t->amount_paid;
        Auth::user()->nairaWallet->save();

        $currentSystemBalance = NairaWallet::sum('amount');

        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $t->amount_paid;
        $nt->user_id = Auth::user()->id;
        $nt->type = 'naira wallet';
        $nt->previous_balance = Auth::user()->nairaWallet->amount;
        $nt->current_balance = Auth::user()->nairaWallet->amount + $t->amount_paid;
        $nt->system_previous_balance = $systemBalance;
        $nt->system_current_balance =  $currentSystemBalance;
        $nt->charge = 0;
        $nt->transaction_type_id = 23;
        $nt->dr_wallet_id = $n->id;
        $nt->cr_wallet_id = $user_naira_wallet->id;
        $nt->dr_acct_name = 'Dantown';
        $nt->cr_acct_name = $user->first_name . ' ' . $user->last_name;
        $nt->narration = 'Credit for sell transaction with id ' . $t->uid;
        $nt->trans_msg = 'This transaction was handled automatically ';
        $nt->cr_user_id = $user->id;
        $nt->dr_user_id = 1;
        $nt->status = 'success';
        $nt->save();

        return response()->json([
            'success' => true,
            'msg' => 'Ethereum sold successfully'
        ]);
    }
}
