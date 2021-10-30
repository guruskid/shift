<?php

namespace App\Http\Controllers;

use App\CryptoRate;
use App\FeeWallet;
use App\HdWallet;
use App\NairaTransaction;
use App\NairaWallet;
use App\Setting;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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


        Auth::user()->bnbWallet()->create([
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
            return redirect()->route('user.portfolio')->with(['error' => 'Please a Binance coin wallet to continue']);
        }

        $bnb_rate = LiveRateController::bnbRate();
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
                $t->senderNote = 'Sending BNB';
            }
        }


        return view('newpages.binance-wallet', compact('bnb_wallet', 'transactions', 'bnb_rate'));
    }

    public function trade()
    {
        $sell_rate = LiveRateController::usdRate();
        $bnb_usd = LiveRateController::bnbRate();
        $bnb_wallet = Auth::user()->bnbWallet;
        $charge = Setting::where('name', 'binance_sell_charge')->first()->value;



        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . $bnb_wallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);

        $accounts = json_decode($res->getBody());

        $bnb_wallet->balance = $accounts->balance->availableBalance;
        $bnb_wallet->usd = $bnb_wallet->balance  * $bnb_usd;

        $hd_wallet = HdWallet::where('currency_id', 4)->first()->address;

        return view('newpages.trade_binance', compact('sell_rate', 'bnb_usd', 'hd_wallet', 'charge'));
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

        if (!Auth::user()->bnbWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please create a Binance coin wallet to continue'
            ]);
        }

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . Auth::user()->bnbWallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);

        $accounts = json_decode($res->getBody());

        $user_wallet = Auth::user()->bnbWallet;
        $user_wallet->balance = $accounts->balance->availableBalance;

        $hd_wallet = HdWallet::where(['currency_id' => 4])->first();

        if ($request->amount > $user_wallet->balance) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient balance'
            ]);
        }

        $charges_wallet = FeeWallet::where(['crypto_currency_id' => 4, 'name' => 'bnb_charge'])->first();
        $service_wallet = FeeWallet::where(['crypto_currency_id' => 4, 'name' => 'bnb_service'])->first();

        // percentage deduction in price
        $trading_per = Setting::where('name', 'trading_bnb_per')->first()->value;
        $service_fee = ($trading_per / 100) * $request->amount;


        //percentage charge
        $charge = Setting::where('name', 'binance_sell_charge')->first()->value;
        $charge = ($charge / 100) * $request->amount;


        //Current bnb price
        $bnb_usd = LiveRateController::bnbRate();
        $usd_ngn = LiveRateController::usdRate();

        $total = $request->amount - $charge  - $service_fee;
        $usd = $request->amount * $bnb_usd;
        $ngn = $usd * $usd_ngn;

        $charge_ngn = $charge * $bnb_usd * $usd_ngn;
        $ngn -= $charge_ngn;

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
                    "senderAccountId" => Auth::user()->bnbWallet->account_id,
                    "recipientAccountId" => $hd_wallet->account_id,
                    "amount" => number_format((float) $request->amount, 9),
                    "anonymous" => false,
                    "compliant" => false,
                    "transactionCode" => uniqid(),
                    "paymentId" => uniqid(),
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
            'card_id' => 140,
            'type' => 'sell',
            'amount' => $usd,
            'amount_paid' => $ngn,
            'quantity' => number_format((float) $request->amount, 8),
            'card_price' => $bnb_usd,
            'status' => 'success',
            'uid' => uniqid(),
            'user_email' => Auth::user()->email,
            'card' => 'binance coin',
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
            'msg' => 'BNB sold successfully'
        ]);
    }

}
