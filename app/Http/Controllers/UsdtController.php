<?php

namespace App\Http\Controllers;

use App\Contract;
use App\CryptoRate;
use App\FeeWallet;
use App\FlaggedTransactions;
use App\HdWallet;
use App\Http\Controllers\Admin\FlaggedTransactionsController;
use App\Http\Controllers\Admin\SettingController;
use App\Mail\GeneralTemplateOne;
use App\NairaTransaction;
use App\NairaWallet;
use App\Setting;
use App\User;
use App\Wallet;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UsdtController extends Controller
{
    public static function create(Request $request)
    {
        if (Auth::user()->usdtWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'USDT wallet already exists for this account'
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
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'USDT wallet created successfully'
        ]);
    }

    public function wallet(Request $r)
    {

        if (!Auth::user()->usdtWallet) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please a USDT wallet to continue']);
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

    public function walletApi(Request $r)
    {

        if (!Auth::user()->usdtWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please create a USDT wallet to continue'
            ]);
        }


        $sell_rate =  LiveRateController::usdNgn();
        $buy_rate = LiveRateController::usdNgn(true, 'buy');
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
        $wallet->ngn = $wallet->usd * $sell_rate;

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

        // \Log::info([$buy_rate, $sell_rate, $wallet, $rate, $transactions]);

        return response()->json([
            'success' => true,
            'date' => [
                'buy_rate' => $buy_rate,
                'sell_rate' => $sell_rate,
                'wallet' => $wallet,
                'rate' => $rate,
                'transactions' => $transactions
            ]
        ]);
    }


    public function trade()
    {
        $sell_rate = LiveRateController::usdNgn();
        $buy_rate = LiveRateController::usdNgn(true, 'buy');
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

        return view('newpages.trade_usdt', compact('sell_rate', 'wallet', 'hd_wallet', 'amt_usd', 'charge', 'buy_rate'));
    }

    public function tradeApi()
    {
        $sell_rate = LiveRateController::usdNgn();
        $buy_rate = LiveRateController::usdNgn(true, 'buy');
        $tron_usd = LiveRateController::usdtRate();
        $wallet = Auth::user()->usdtWallet;
        $charge = Setting::where('name', 'usdt_sell_charge')->first()->value;

        $trading_per = Setting::where('name', 'trading_usdt_per')->first()->value;
        $tp = ($trading_per / 100) * $tron_usd;

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . $wallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
        ]);

        $accounts = json_decode($res->getBody());

        $wallet->balance = $accounts->balance->availableBalance;
        $wallet->usd = $wallet->balance  * $tron_usd;
        $wallet->ngn = $wallet->usd * $sell_rate;

        return response()->json([
            'success' => true,
            'data' => [
                'sell_rate' => $sell_rate,
                'buy_rate' => $buy_rate,
                'wallet' => $wallet,
                'tron_usd' => $tron_usd,
                'charge' => $charge
            ]
        ]);
    }

    public function fees($address, $amount)
    {
        // $fees = 1;

        $charge = Setting::where('name', 'usdt_send_charge')->first()->value;

        return response()->json([
            'success' => true,
            "fee" => $charge,
        ]);
    }


    public static function sell(Request $request)
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

        if ($user_wallet->status == 'pending') {
            $activate = UsdtController::activate($user_wallet)->getData();

            if ($activate->success == false) {
                return response()->json([
                    'success' => false,
                    'message' => $activate->message
                ]);
            }
        }

        $user_wallet->balance = $accounts->balance->availableBalance;


        $hd_wallet = HdWallet::where(['currency_id' => 7])->first();

        if ($request->amount > $user_wallet->balance) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient balance'
            ]);
        }

        if ($user_wallet->balance - $request->amount < 1) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient balance, a minimum of $1 must be reserved in the wallet'
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
        $service_fee = round(($trading_per / 100) * $request->amount, 3);



        //Get fees for the txn on the chain


        //percentage charge
        $charge = Setting::where('name', 'usdt_sell_charge')->first()->value;
        $charge = round(($charge / 100) * $request->amount, 3);

        //Current eth price
        $amt_usd = LiveRateController::usdtRate();
        $usd_ngn = LiveRateController::usdtNgn();

        $total = $request->amount - $charge - $service_fee;
        $usd = $total * $amt_usd;
        $ngn = $usd * $usd_ngn;

        //Commission
        $usd_ngn_old = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;
        $commission = SettingController::get('crypto_commission');
        $commission = ($commission / 100) * $usd_ngn_old;
        $commission = $commission * $total;

        if ($request->amount < 5) {
            return response()->json([
                'success' => false,
                'msg' => 'Minimum trade amount is $5'
            ]);
        }

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
                "amount" => (string)round($request->amount, 4),
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
                    "amount" => [$total,  $service_fee, $charge],
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

        $is_flagged = 0;
        if($ngn >= 1000000):
            $is_flagged = 1;
            $lastTranxAmount = FlaggedTransactionsController::getLastTransaction(Auth::user()->id);
        endif;

        $reference = \Str::random(5) . '-' . Auth::user()->id;

        $t = Auth::user()->transactions()->create([
            'card_id' => 143,
            'type' => 'sell',
            'amount' => $usd,
            'amount_paid' => $ngn,
            'quantity' => number_format((float) $request->amount, 8),
            'card_price' => $amt_usd,
            'status' => 'success',
            'uid' => $reference,
            'user_email' => Auth::user()->email,
            'card' => 'USDT',
            'agent_id' => 1,
            'ngn_rate' => $usd_ngn,
            'commission' => $commission,
            'is_flagged' => $is_flagged,
        ]);

        $systemBalance = NairaWallet::sum('amount');
        $user_naira_wallet = Auth::user()->nairaWallet;
        $user = Auth::user();
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
        $nt->transaction_type_id = 24;
        $nt->dr_wallet_id = $n->id;
        $nt->cr_wallet_id = $user_naira_wallet->id;
        $nt->dr_acct_name = 'Dantown';
        $nt->cr_acct_name = $user->first_name . ' ' . $user->last_name;
        $nt->narration = 'Credit for sell transaction with id ' . $t->uid;
        $nt->trans_msg = 'This transaction was handled automatically ';
        $nt->cr_user_id = $user->id;
        $nt->dr_user_id = 1;
        $nt->status = 'success';
        $nt->is_flagged = $t->is_flagged;
        $nt->save();

        if($t->is_flagged == 1){
            $narration = "USDT transaction for the day is greater than 1 million";
            $agent_id = FlaggedTransactionsController::getCurrentAccountant();
            $user = Auth::user();
            $type = 'Bulk Credit';
            $flaggedTranx =  new FlaggedTransactions();
            $flaggedTranx->type = $type;
            $flaggedTranx->user_id = Auth::user()->id;
            $flaggedTranx->transaction_id = $t->id;
            $flaggedTranx->reference_id = $nt->reference;
            $flaggedTranx->previousTransactionAmount = $lastTranxAmount;
            $flaggedTranx->accountant_id = $agent_id;
            $flaggedTranx->narration = $narration;
            $flaggedTranx->save();
        }

        // ///////////////////////////////////////////////////////////
        $finalamountcredited = Auth::user()->nairaWallet->amount;
        $title = 'Sell Order Successful';
        $body = 'Your order to sell ' . $t->card . ' has been filled and your Naira wallet has been credited with???' . number_format($t->amount_paid) . '<br>
         Your new  balance is ' . $finalamountcredited . '.<br>
         Date: ' . now() . '.<br><br>
         Thank you for Trading with Dantown.';

        $btn_text = '';
        $btn_url = '';

        $name = (Auth::user()->first_name == " ") ? Auth::user()->username : Auth::user()->first_name;
        $name = explode(' ', $name);
        $firstname = ucfirst($name[0]);
        Mail::to(Auth::user()->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));

        ////////////////////////////////////////////

        return response()->json([
            'success' => true,
            'msg' => 'USDT sold successfully'
        ]);
    }

    public static function buy(Request $request)
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

        $systemBalance = NairaWallet::sum('amount');
        $client = new Client();

        $usdt_wallet = Auth::user()->usdtWallet;
        $naira_wallet = Auth::user()->nairaWallet;

        $hd_wallet = HdWallet::where(['currency_id' => 7])->first();
        $url_hd = env('TATUM_URL') . '/ledger/account/' . $hd_wallet->account_id;
        $res_hd = $client->request('GET', $url_hd, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
        ]);
        $res_hd = json_decode($res_hd->getBody());
        $hd_wallet->balance = $res_hd->balance->availableBalance;

        if ($request->amount > $hd_wallet->balance) {
            return response()->json([
                'success' => false,
                'msg' => 'Service not available, please try again later'
            ]);
        }

        // percentage deduction in price
        $trading_per = Setting::where('name', 'trading_usdt_per')->first()->value;
        $service_fee = ($trading_per / 100) * $request->amount;

        //percentage charge
        $charge = Setting::where('name', 'usdt_sell_charge')->first()->value;
        $charge = ($charge / 100) * $request->amount;

        //Current price
        $amt_usd = LiveRateController::usdtRate();
        $usd_ngn = LiveRateController::usdNgn(true, 'buy');


        $usd = $request->amount * $amt_usd;
        $ngn = $usd * $usd_ngn;
        $total = $request->amount - $charge - $service_fee;

        if ($ngn > $naira_wallet->amount) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient balance'
            ]);
        }

        $ledger_balance = UserController::ledgerBalance()->getData()->balance;
        if ($ngn  > ($ledger_balance + 10)) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient ledger balance to initiate trade'
            ]);
        }

        $blockchain_fee = 200;
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

        // if ($usd < 10) {
        //     return response()->json([
        //         'success' => false,
        //         'msg' => 'Minimum trade amount is $10'
        //     ]);
        // }

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
                "senderAccountId" => $hd_wallet->account_id,
                "address" => $usdt_wallet->address,
                "amount" => (string)round($request->amount, 5),
                "compliant" => false,
                "fee" => "0",
                "paymentId" => uniqid(),
                "senderNote" => "buying Tether 1"
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
                    "custodialAddress" => $hd_wallet->address,
                    "contractType" => [0, 0, 0],
                    "recipient" => [Auth::user()->usdtWallet->address, $service_wallet->address, $charge_wallet->address],
                    "amount" => [round($total, 5),  round($service_fee, 5), round($charge, 5)],
                    "signatureId" => $hd_wallet->private_key,
                    "tokenId" => ["0", "0", "0"],
                    "tokenAddress" => ["TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t", "TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t", "TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t"],
                    "feeLimit" => $blockchain_fee,
                    "from" => $fees_wallet->address,
                ]
            ]);

            $send_res = json_decode($send->getBody());

            if (isset($send_res->signatureId)) {
                // deduct the ngn
                Auth::user()->nairaWallet->amount -= $ngn;
                Auth::user()->nairaWallet->save();
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
            'type' => 'buy',
            'amount' => $usd,
            'amount_paid' => $ngn,
            'quantity' => number_format((float) $total, 8),
            'card_price' => $amt_usd,
            'status' => 'success',
            'uid' => uniqid(),
            'user_email' => Auth::user()->email,
            'card' => 'USDT',
            'agent_id' => 1,
            'ngn_rate' => $usd_ngn
        ]);

        $user_naira_wallet = Auth::user()->nairaWallet;
        $user = Auth::user();
        $reference = \Str::random(2) . '-' . $t->id;
        $n = NairaWallet::find(1);

        $currentSystemBalance = NairaWallet::sum('amount');
        $nt = new NairaTransaction();
        $nt->reference = $reference;
        $nt->amount = $t->amount_paid;
        $nt->user_id = Auth::user()->id;
        $nt->type = 'naira wallet';
        $nt->current_balance = Auth::user()->nairaWallet->amount;
        $nt->previous_balance = Auth::user()->nairaWallet->amount + $t->amount_paid;
        $nt->system_previous_balance = $systemBalance;
        $nt->system_current_balance =  $currentSystemBalance;
        $nt->charge = 0;
        $nt->transaction_type_id = 5;
        $nt->cr_wallet_id = $n->id;
        $nt->dr_wallet_id = $user_naira_wallet->id;
        $nt->cr_acct_name = 'Dantown';
        $nt->dr_acct_name = $user->first_name . ' ' . $user->last_name;
        $nt->narration = 'Debit for sell transaction with id ' . $t->uid;
        $nt->trans_msg = 'This transaction was handled automatically ';
        $nt->dr_user_id = $user->id;
        $nt->cr_user_id = 1;
        $nt->status = 'success';
        $nt->save();


        // ///////////////////////////////////////////////////////////
        $finalamountcredited = Auth::user()->nairaWallet->amount + $t->amount_paid;
        $title = 'Buy Order Successful';
        $body = 'Your order to buy ' . $t->card . ' has been filled and your Naira wallet has been debited with???' . number_format($t->amount_paid) . '<br>
         Your new  balance is ' . $finalamountcredited . '.<br>
         Date: ' . now() . '.<br><br>
         Thank you for Trading with Dantown.';

        $btn_text = '';
        $btn_url = '';

        $name = (Auth::user()->first_name == " ") ? Auth::user()->username : Auth::user()->first_name;
        $name = explode(' ', $name);
        $firstname = ucfirst($name[0]);
        Mail::to(Auth::user()->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));

        // ////////////////////////////////////////////

        return response()->json([
            'success' => true,
            'msg' => 'USDT bought successfully'
        ]);
    }




    public static function send(Request $request)
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

        if ($user_wallet->status == 'pending') {
            $activate = UsdtController::activate($user_wallet)->getData();

            if ($activate->success == false) {
                return response()->json([
                    'success' => false,
                    'message' => $activate->message
                ]);
            }
        }

        $user_wallet->balance = $accounts->balance->availableBalance;


        $hd_wallet = HdWallet::where(['currency_id' => 7])->first();
        $charge = Setting::where('name', 'usdt_send_charge')->first()->value;
        $sub_total = round(($request->amount  + $charge), 3);

        if ($sub_total > $user_wallet->balance) {
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


        //Store Withdrawal
        $url = env('TATUM_URL') . '/offchain/withdrawal';
        $store = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
            'json' =>  [
                "senderAccountId" => Auth::user()->usdtWallet->account_id,
                "address" => $request->address,
                "amount" => (string)$sub_total,
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
                    "amount" => [round($total, 3),  round($charge, 3)],
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

        return response()->json(['success' => true, 'msg' => 'USDT sent successfully']);
    }

    public static function activate($wallet)
    {
        $index = Contract::where('hash', $wallet->address)->first()->index;
        $hd_wallet = HdWallet::where(['currency_id' => 7])->first();

        $fees_wallet = FeeWallet::where('name', 'usdt_fees')->first();
        $fees_wallet->balance = CryptoHelperController::feeWalletBalance(7);

        $fee_limit = 50;
        if ($fees_wallet->balance < $fee_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Service not available'
            ]);
        }

        $client = new Client();

        $url_contract = env('TATUM_URL') . '/gas-pump/activate';
        try {
            $res_contract = $client->request('POST', $url_contract, [
                'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
                'json' =>  [
                    "chain" => "TRON",
                    "owner" => $fees_wallet->address,
                    "from" => (int)$index,
                    "to" => (int)$index,
                    "feeLimit" => $fee_limit,
                    "signatureId" => $hd_wallet->private_key,
                ]
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'An error occured, please try again'
            ]);
        }

        $send_res = json_decode($res_contract->getBody());
        if (!isset($send_res->signatureId)) {
            return response()->json([
                'success' => false,
                'message' => 'A server error occured, please try again'
            ]);
        }

        $wallet->status = 'activated';
        $wallet->save();

        return response()->json(['success' => true]);
    }
}
