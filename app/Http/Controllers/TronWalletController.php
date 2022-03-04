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
                'msg' => 'Tron wallet already exists for this account'
            ]);
        }

        if (!Auth::user()->btcWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please create a BTC wallet before continuing'
            ]);
        }

        $contract = Contract::where(['currency_id' => 5, 'status' => 'pending', 'type' => 'address'])->first();

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
        $link_eth_url = env('TATUM_URL') . '/offchain/account/' . $tron_account_id . '/address/' . $tron_address;
        $res = $client->request('POST', $link_eth_url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
        ]);

        $contract->status = 'completed';
        $contract->save();

        Auth::user()->tronWallet()->create([
            'account_id' => $tron_account_id,
            'currency_id' => 5,
            'name' => Auth::user()->email,
            'address' => $tron_address,
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Tron wallet created successfully'
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

    // Api
    public function walletApi(Request $r)
    {

        if (!Auth::user()->tronWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please create a Tron wallet to continue'
            ]);
        }


        $sell_rate = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;
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
        $tron_wallet->ngn = $tron_wallet->usd * $sell_rate;

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

        return response()->json([
            'success' => true,
            'date' => [
                'sell_rate' => $sell_rate,
                'tron_wallet' => $tron_wallet,
                'tron_rate' => $tron_rate,
                'transactions' =>$transactions
            ]
        ]);
    }

    public function trade()
    {
        $sell_rate = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;
        $tron_usd = LiveRateController::tronRate();
        $tron_wallet = Auth::user()->tronWallet;
        $charge = Setting::where('name', 'tron_sell_charge')->first()->value;

        $trading_per = Setting::where('name', 'trading_tron_per')->first()->value;
        $tp = ($trading_per / 100) * $tron_usd;

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . $tron_wallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);

        $accounts = json_decode($res->getBody());

        $tron_wallet->balance = $accounts->balance->availableBalance;
        $tron_wallet->usd = $tron_wallet->balance  * $tron_usd;

        $hd_wallet = HdWallet::where('currency_id', 5)->first();

        return view('newpages.trade_tron', compact('sell_rate', 'tron_wallet', 'hd_wallet', 'tron_usd', 'charge'));
    }

    // Api
    public function tradeApi()
    {
        $sell_rate = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;
        $tron_usd = LiveRateController::tronRate();
        $tron_wallet = Auth::user()->tronWallet;
        $charge = Setting::where('name', 'tron_sell_charge')->first()->value;

        $trading_per = Setting::where('name', 'trading_tron_per')->first()->value;
        $tp = ($trading_per / 100) * $tron_usd;

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . $tron_wallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);

        $accounts = json_decode($res->getBody());

        $tron_wallet->balance = $accounts->balance->availableBalance;
        $tron_wallet->usd = $tron_wallet->balance  * $tron_usd;
        $tron_wallet->ngn = $tron_wallet->usd * $sell_rate;

        return response()->json([
            'success' => true,
            'data' => [
                'sell_rate' => $sell_rate,
                'tron_wallet' => $tron_wallet,
                'tron_usd' => $tron_usd,
                'charge' => $charge
            ]
        ]);
    }

    public function fees($address, $amount)
    {
        $fees = 15;

        $charge = Setting::where('name', 'tron_send_charge')->first()->value;

        return response()->json([
            'success' => true,
            "fee" => $fees + $charge,
        ]);
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

        if (!Auth::user()->tronWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please create a Tron wallet to continue'
            ]);
        }


        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . Auth::user()->tronWallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);

        $accounts = json_decode($res->getBody());

        $user_wallet = Auth::user()->tronWallet;
        $user_wallet->balance = $accounts->balance->availableBalance;

        $hd_wallet = HdWallet::where(['currency_id' => 5])->first();

        if ($request->amount > $user_wallet->balance) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient balance'
            ]);
        }

        $blockchain_fee = 100;
        $fee_wallet_balance = CryptoHelperController::feeWalletBalance(5);
        if ($fee_wallet_balance < $blockchain_fee) {
            return response()->json([
                'success' => false,
                'msg' => 'Service not available, please try again later'
            ]);
        }

        $fees_wallet = FeeWallet::where(['crypto_currency_id' => 5, 'name' => 'tron_fees'])->first();
        $charge_wallet = FeeWallet::where(['crypto_currency_id' => 5, 'name' => 'tron_charge'])->first();
        $service_wallet = FeeWallet::where(['crypto_currency_id' => 5, 'name' => 'tron_service'])->first();

        // percentage deduction in price
        $trading_per = Setting::where('name', 'trading_tron_per')->first()->value;
        $service_fee = ($trading_per / 100) * $request->amount;

        //Get fees for the txn on the chain


        //percentage charge
        $charge = Setting::where('name', 'tron_sell_charge')->first()->value;
        $charge = ($charge / 100) * $request->amount;

        //Current eth price
        $tron_usd = LiveRateController::tronRate();
        $usd_ngn = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;

        $total = $request->amount - $charge - $service_fee;
        $usd = $request->amount * $tron_usd;
        $ngn = $usd * $usd_ngn;

        if ($total <= 0) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient trade amount when fee was deducted'
            ]);
        }

        $reference = \Str::random(5) . Auth::user()->id;

        //Store Withdrawal
        $url = env('TATUM_URL') . '/offchain/withdrawal';
        $store = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' =>  [
                "senderAccountId" => Auth::user()->tronWallet->account_id,
                "address" => $hd_wallet->address,
                "amount" => number_format((float) $request->amount, 8),
                "compliant" => false,
                "fee" => "0",
                "paymentId" => uniqid(),
                "senderNote" => "Selling Tron 1"
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
                    "chain" => "TRON",
                    "custodialAddress" => Auth::user()->tronWallet->address,
                    "contractType" => [3, 3, 3],
                    "recipient" => [$hd_wallet->address, $service_wallet->address, $charge_wallet->address],
                    "amount" => [number_format((float) $total, 4), number_format((float) $service_fee, 4), number_format((float) $charge, 4)],
                    "signatureId" => $hd_wallet->private_key,
                    "tokenId" => ["0", "0", "0"],
                    "tokenAddress" => ["0", "0", "0"],
                    "feeLimit" => 50,
                    "from" => $fees_wallet->address,
                ]
            ]);

            $send_res = json_decode($send->getBody());

            if (Arr::exists($send_res, 'signatureId')) {
            } else {
                //Cancel TXN
                $cancel = $client->request('delete', env('TATUM_URL') . '/offchain/withdrawal/' . $store_res->id, [
                    'headers' => ['x-api-key' => env('TATUM_KEY')],
                ]);
                return $send_res;
            }
        } catch (\Exception $e) {
            report($e);
            $cancel = $client->request('delete', env('TATUM_URL') . '/offchain/withdrawal/' . $store_res->id, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
            ]);

            return response()->json(['success' => false, 'msg' => 'An error occured, please try again']);
        }

        $t = Auth::user()->transactions()->create([
            'card_id' => 141,
            'type' => 'sell',
            'amount' => $usd,
            'amount_paid' => $ngn,
            'quantity' => number_format((float) $request->amount, 8),
            'card_price' => $tron_usd,
            'status' => 'success',
            'uid' => uniqid(),
            'user_email' => Auth::user()->email,
            'card' => 'tron',
            'agent_id' => 1
        ]);

        $user_naira_wallet = Auth::user()->nairaWallet;
        $user = Auth::user();
        $reference = \Str::random(2) . '-' . $t->id;
        $n = NairaWallet::find(1);

        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $t->amount_paid;
        $nt->user_id = Auth::user()->id;
        $nt->type = 'naira wallet';
        $nt->previous_balance = Auth::user()->nairaWallet->amount;
        $nt->current_balance = Auth::user()->nairaWallet->amount + $t->amount_paid;
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

        Auth::user()->nairaWallet->amount += $t->amount_paid;
        Auth::user()->nairaWallet->save();

        return response()->json([
            'success' => true,
            'msg' => 'Tron sold successfully'
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

        if (!Auth::user()->tronWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please create a Tron wallet to continue'
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
        $url = env('TATUM_URL') . '/ledger/account/' . Auth::user()->tronWallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);

        $accounts = json_decode($res->getBody());

        $user_wallet = Auth::user()->tronWallet;
        $user_wallet->balance = $accounts->balance->availableBalance;

        $hd_wallet = HdWallet::where(['currency_id' => 5])->first();
        $fees = 15;
        $charge = Setting::where('name', 'tron_send_charge')->first()->value;


        if (($request->amount + $fees + $charge) > $user_wallet->balance) {
            return response()->json([
                'success' => false,
                'msg' => "Insufficient balance"
            ]);
        }

        // $fee_limit = 200;
        // $fee_wallet_balance = CryptoHelperController::feeWalletBalance(5);
        // if ($fee_wallet_balance < $fee_limit) {
        //     return response()->json([
        //         'success' => false,
        //         'msg' => 'Service not available, please try again later'
        //     ]);
        // }

        $charge_wallet = FeeWallet::where(['crypto_currency_id' => 5, 'name' => 'tron_charge'])->first();
        $fee_wallet = FeeWallet::where(['crypto_currency_id' => 5, 'name' => 'tron_fees'])->first();

        $blockchain_fee = 100;
        $fee_wallet_balance = CryptoHelperController::feeWalletBalance(5);
        if ($fee_wallet_balance < $blockchain_fee) {
            return response()->json([
                'success' => false,
                'msg' => 'Service not available, please try again later'
            ]);
        }


        $total = $request->amount;

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
                "senderAccountId" => Auth::user()->tronWallet->account_id,
                "address" => $request->address,
                "amount" => number_format((float) $request->amount + $fees + $charge, 8),
                "compliant" => false,
                "fee" => "0",
                "paymentId" => uniqid(),
                "senderNote" => "Sending tron"
            ]
        ]);

        $store_res = json_decode($store->getBody());
        if ($store->getStatusCode() != 200) {
            return response()->json([
                'success' => false,
                'msg' => 'An error occured while sending, please try again'
            ]);
        }

        try {
            $url = env('TATUM_URL') . '/blockchain/sc/custodial/transfer/batch';
            $send = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
                'json' =>  [
                    "chain" => "TRON",
                    "custodialAddress" => Auth::user()->tronWallet->address,
                    "contractType" => [3, 3, 3],
                    "recipient" => [$request->address,  $charge_wallet->address, $fee_wallet],
                    "amount" => [number_format((float) $total, 4), number_format((float) $charge, 4), number_format((float) $fees, 4)],
                    "signatureId" => $hd_wallet->private_key,
                    "tokenId" => ["0",  "0"],
                    "tokenAddress" => ["0",  "0"],
                    "feeLimit" => $blockchain_fee,
                    "from" => $fee_wallet->address,
                ]
            ]);

            $send_res = json_decode($send->getBody());

            if (!Arr::exists($send_res, 'signatureId')) {
                //Cancel TXN
                $cancel = $client->request('delete', env('TATUM_URL') . '/offchain/withdrawal/' . $store_res->id, [
                    'headers' => ['x-api-key' => env('TATUM_KEY')],
                ]);
                return $send_res;
            }

        } catch (\Exception $e) {
            report($e);
            $cancel = $client->request('delete', env('TATUM_URL') . '/offchain/withdrawal/' . $store_res->id, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
            ]);

            return response()->json(['success' => false, 'msg' => 'An error occured, please try again']);
        }

        return response()->json(['success' => true, 'msg' => 'Tron sent successfully' ]);
    }
}
