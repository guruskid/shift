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

class UsdtController extends Controller
{
    public function create(Request $request)
    {
        if (Auth::user()->usdtWallet) {
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

        $contract = Contract::where(['currency_id' => 7, 'status' => 'pending', 'type' => 'address'])->first();

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
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
            'json' => [
                "accounts" => [
                    [
                        "currency" => "USDT_TRON",
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
        $account_id = $body[0]->id;

        $address = $contract->hash;


        //Link address to ledger account
        $link_eth_url = env('TATUM_URL') . '/offchain/account/' . $account_id . '/address/' . $address;
        $res = $client->request('POST', $link_eth_url, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
        ]);

        $contract->status = 'completed';
        $contract->save();

        Auth::user()->usdtWallet()->create([
            'account_id' => $account_id,
            'currency_id' => 7,
            'name' => Auth::user()->email,
            'address' => $address,
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'USDT wallet created successfully'
        ]);
    }

    public function wallet(Request $r)
    {

        if (!Auth::user()->usdtWallet) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please a Tron wallet to continue']);
        }

        $rate = LiveRateController::usdtRate();
        $wallet = Auth::user()->usdtWallet;

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . $wallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
        ]);

        $accounts = json_decode($res->getBody());

        $wallet->balance = $accounts->balance->availableBalance;
        $wallet->usd = $wallet->balance  * $rate;

        $url = env('TATUM_URL') . '/ledger/transaction/account?pageSize=50';
        $get_txns = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
            "json" => ["id" => Auth::user()->usdtWallet->account_id]
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


        return view('newpages.usdt-wallet', compact('wallet', 'transactions', 'rate'));
    }


    public function trade()
    {
        $sell_rate = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 7])->first()->rate;
        $amt_usd = LiveRateController::usdtRate();
        $wallet = Auth::user()->usdtWallet;
        $charge = Setting::where('name', 'usdt_sell_charge')->first()->value;

        $trading_per = Setting::where('name', 'trading_usdt_per')->first()->value;
        $tp = ($trading_per / 100) * $amt_usd;

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . $wallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
        ]);

        $accounts = json_decode($res->getBody());

        $wallet->balance = $accounts->balance->availableBalance;
        $wallet->usd = $wallet->balance  * $amt_usd;

        $hd_wallet = HdWallet::where('currency_id', 7)->first();

        return view('newpages.trade_usdt', compact('sell_rate', 'wallet', 'hd_wallet', 'amt_usd', 'charge'));
    }

    public function fees($address, $amount)
    {
        $fees = 1;

        $charge = Setting::where('name', 'usdt_send_charge')->first()->value;

        return response()->json([
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

        if (!Auth::user()->usdtWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please create a Tether wallet to continue'
            ]);
        }


        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . Auth::user()->usdtWallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
        ]);

        $accounts = json_decode($res->getBody());

        $user_wallet = Auth::user()->usdtWallet;
        $user_wallet->balance = $accounts->balance->availableBalance;

        $hd_wallet = HdWallet::where(['currency_id' => 7])->first();

        if ($request->amount > $user_wallet->balance) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient balance'
            ]);
        }

        $blockchain_fee = 100;
        $fee_wallet_balance = CryptoHelperController::feeWalletBalance(7);
        if ($fee_wallet_balance < $blockchain_fee) {
            return response()->json([
                'success' => false,
                'msg' => 'Service not available, please try again later'
            ]);
        }

        $fees_wallet = FeeWallet::where(['crypto_currency_id' => 7, 'name' => 'usdt_fees'])->first();
        $charge_wallet = FeeWallet::where(['crypto_currency_id' => 7, 'name' => 'usdt_charge'])->first();
        $service_wallet = FeeWallet::where(['crypto_currency_id' => 7, 'name' => 'usdt_service'])->first();

        // percentage deduction in price
        $trading_per = Setting::where('name', 'trading_usdt_per')->first()->value;
        $service_fee = ($trading_per / 100) * $request->amount;

        //Get fees for the txn on the chain


        //percentage charge
        $charge = Setting::where('name', 'usdt_sell_charge')->first()->value;
        $charge = ($charge / 100) * $request->amount;

        //Current eth price
        $amt_usd = LiveRateController::usdtRate();
        $usd_ngn = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 7])->first()->rate;

        $total = $request->amount - $charge - $service_fee;
        $usd = $request->amount * $amt_usd;
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
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
            'json' =>  [
                "senderAccountId" => Auth::user()->usdtWallet->account_id,
                "address" => $hd_wallet->address,
                "amount" => number_format((float) $request->amount, 8),
                "compliant" => false,
                "fee" => "0",
                "paymentId" => uniqid(),
                "senderNote" => "Selling Tether 1"
            ]
        ]);

        $store_res = json_decode($store->getBody());
        if ($store->getStatusCode() != 200) {
            return response()->json([
                'success' => false,
                'msg' => 'An error occured while performing operation'
            ]);
        }

        try {
            $url = env('TATUM_URL') . '/blockchain/sc/custodial/transfer/batch';
            $send = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
                'json' =>  [
                    "chain" => "TRON",
                    "custodialAddress" => Auth::user()->usdtWallet->address,
                    "contractType" => [0, 0, 0],
                    "recipient" => [$hd_wallet->address, $service_wallet->address, $charge_wallet->address],
                    "amount" => [number_format((float) $total, 6), number_format((float) $service_fee, 6), number_format((float) $charge, 6)],
                    "signatureId" => $hd_wallet->private_key,
                    "tokenId" => ["0", "0", "0"],
                    "tokenAddress" => ["TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t", "TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t", "TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t"],
                    "feeLimit" => $blockchain_fee,
                    "from" => $fees_wallet->address,
                ]
            ]);

            $send_res = json_decode($send->getBody());

            if (isset($send_res->signatureId)) {
            } else {
                //Cancel TXN
                $cancel = $client->request('delete', env('TATUM_URL') . '/offchain/withdrawal/' . $store_res->id, [
                    'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
                ]);
                return $send_res;
            }
        } catch (\Exception $e) {
            report($e);
            $cancel = $client->request('delete', env('TATUM_URL') . '/offchain/withdrawal/' . $store_res->id, [
                'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
            ]);

            return response()->json(['success' => false, 'msg' => 'An error occured, please try again']);
        }

        $t = Auth::user()->transactions()->create([
            'card_id' => 143,
            'type' => 'sell',
            'amount' => $usd,
            'amount_paid' => $ngn,
            'quantity' => number_format((float) $request->amount, 8),
            'card_price' => $amt_usd,
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

        if (!Auth::user()->usdtWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please create a USDT wallet to continue'
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
        $url = env('TATUM_URL') . '/ledger/account/' . Auth::user()->usdtWallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
        ]);

        $accounts = json_decode($res->getBody());

        $user_wallet = Auth::user()->usdtWallet;
        $user_wallet->balance = $accounts->balance->availableBalance;

        $hd_wallet = HdWallet::where(['currency_id' => 7])->first();
        $fees = 1;
        $charge = Setting::where('name', 'usdt_send_charge')->first()->value;


        if (($request->amount + $fees + $charge) > $user_wallet->balance) {
            return response()->json([
                'success' => false,
                'msg' => "Insufficient balance"
            ]);
        }

        $charge_wallet = FeeWallet::where(['crypto_currency_id' => 7, 'name' => 'usdt_charge'])->first();
        $fee_wallet = FeeWallet::where(['crypto_currency_id' => 7, 'name' => 'usdt_fees'])->first();

        $fee_limit = 100;
        $fee_wallet_balance = CryptoHelperController::feeWalletBalance(7);
        if ($fee_wallet_balance < $fee_limit) {
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
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
            'json' =>  [
                "senderAccountId" => Auth::user()->usdtWallet->account_id,
                "address" => $request->address,
                "amount" => number_format((float) $request->amount + $fees + $charge, 8),
                "compliant" => false,
                "fee" => "0",
                "paymentId" => uniqid(),
                "senderNote" => "Sending usdt"
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
                'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
                'json' =>  [
                    "chain" => "TRON",
                    "custodialAddress" => Auth::user()->usdtWallet->address,
                    "contractType" => [0, 0],
                    "recipient" => [$request->address,  $charge_wallet->address],
                    "amount" => [number_format((float) $total, 4), number_format((float) $charge + $fees, 4)],
                    "signatureId" => $hd_wallet->private_key,
                    "tokenAddress" => ["TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t",  "TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t"],
                    "tokenId" => ['0', '0'],
                    "feeLimit" => $fee_limit,
                    "from" => $fee_wallet->address,
                ]
            ]);

            $send_res = json_decode($send->getBody());
            if (!isset($send_res->signatureId)) {
                //Cancel TXN
                $cancel = $client->request('delete', env('TATUM_URL') . '/offchain/withdrawal/' . $store_res->id, [
                    'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
                ]);
                return $send_res;
            }

        } catch (\Exception $e) {
            report($e);
            $cancel = $client->request('delete', env('TATUM_URL') . '/offchain/withdrawal/' . $store_res->id, [
                'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
            ]);

            return response()->json(['success' => false, 'msg' => 'An error occured, please try again']);
        }

        return response()->json(['success' => true, 'msg' => 'USDT sent successfully' ]);
    }
}
