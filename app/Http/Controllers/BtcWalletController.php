<?php

namespace App\Http\Controllers;

use App\BtcMigration;
use App\Card;
use App\CardCurrency;
use App\HdWallet;
use App\NairaTransaction;
use App\NairaWallet;
use App\Notification;
use App\Setting;
use App\Wallet;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use RestApis\Blockchain\Constants;

class BtcWalletController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'pin' => 'required|max:4'
        ]);

        if (Auth::user()->btcWallet) {
            return back()->with(['error' => 'New Bitcoin wallet already exists for this account']);
        }

        $user = Auth::user();
        $external_id = $user->username . '-' . uniqid();
        $btc_hd = HdWallet::where('currency_id', 1)->first();
        $btc_xpub = $btc_hd->xpub;

        $client = new Client();
        $url = env('TATUM_URL') . "/ledger/account/batch";

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
        $user->external_id = $external_id;
        $user->pin = Hash::make($request->pin);
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

        $btc_wallet = $user->btcWallet()->create([
            'account_id' => $btc_account_id,
            'name' => $user->email,
            'currency_id' => 1,
            'address' => $address_body[0]->address,
        ]);

        //Migrate old funds
        if (Auth::user()->bitcoinWallet && Auth::user()->bitcoinWallet->balance > 0) {
            $migration = BtcMigration::create([
                'user_id' => Auth::user()->id,
                'amount' => number_format((float) Auth::user()->bitcoinWallet->balance, 8),
            ]);

            $reference = \Str::random(5) . Auth::user()->id;
            $url = env('TATUM_URL') . '/ledger/transaction';
            $migration_wallet = Wallet::where('name', 'migration')->first();

            try {
                $send = $client->request('POST', $url, [
                    'headers' => ['x-api-key' => env('TATUM_KEY')],
                    'json' =>  [
                        "senderAccountId" => $migration_wallet->account_id,
                        "recipientAccountId" => $btc_wallet->account_id,
                        "amount" => $migration->amount,
                        "anonymous" => false,
                        "compliant" => false,
                        "transactionCode" => $reference,
                        "paymentId" => $reference,
                        "baseRate" => 1,
                    ]
                ]);
                $migration->status = 'completed';
                $migration->save();

                $user->bitcoinWallet->balance = 0;
                $user->bitcoinWallet->save();
            } catch (\Throwable $th) {
                //throw $th;
                return back()->with(['success' => 'Bitcoin wallet migrated successfully']);
            }
        }

        return back()->with(['success' => 'Bitcoin wallet migrated successfully']);
    }

    public function getBitcoinNgn()
    {
        $card = Card::find(102);
        $rates = $card->currency->first();

        $res = json_decode(file_get_contents("https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd"));
        $btc_rate = $res->bitcoin->usd;

        $trading_per = Setting::where('name', 'trading_btc_per')->first()->value;
        $tp = ($trading_per / 100) * $btc_rate;
        $btc_rate -= $tp;

        $btc_wallet_bal = Auth::user()->bitcoinWallet->balance ?? 0;
        $btc_usd = $btc_wallet_bal  * $btc_rate;

        $sell =  CardCurrency::where(['card_id' => 102, 'currency_id' => $rates->id, 'buy_sell' => 2])->first()->paymentMediums()->first();
        $rates->sell = json_decode($sell->pivot->payment_range_settings);

        $btc_ngn = $btc_usd * $rates->sell[0]->rate;

        return response()->json([
            'data' => (int)$btc_ngn
        ]);
    }

    public function wallet(Request $r)
    {

        if (!Auth::user()->btcWallet) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please a bitcoin wallet to continue']);
        }

        $fees = 0;

        $charge = Setting::where('name', 'bitcoin_charge')->first();
        if (!$charge) {
            $charge = 0;
        } else {
            $charge = Setting::where('name', 'bitcoin_charge')->first()->value;
        }
        $total_fees = $fees + $charge;


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

        $url = env('TATUM_URL') . '/ledger/transaction/account?pageSize=50';
        $get_txns = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            "json" => ["id" => Auth::user()->btcWallet->account_id]
        ]);

        $transactions = json_decode($get_txns->getBody());
        foreach ($transactions as $t) {
            $x = \Str::limit($t->created, 10, '');
            $time = \Carbon\Carbon::parse((int)$x);
            $t->created = $time->setTimezone('Africa/Lagos');

            if (!isset($t->senderNote)) {
                $t->senderNote = 'Sending BTC';
            }
        }

        //dd(time(), $transactions[0]->created, $time->format('d M Y h:ia') );

        return view('newpages.bitcoin-wallet', compact('fees', 'btc_wallet', 'transactions', 'btc_rate', 'charge', 'total_fees'));
    }

    public function fees($address, $amount)
    {
        $client = new Client();
        $hd_wallet = HdWallet::where(['currency_id' => 1])->first();
        $amount = number_format((float) $amount, 8);

        $url = env('TATUM_URL') . '/offchain/blockchain/estimate';
        /* try { */
        $get_fees = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' =>  [
                "senderAccountId" => Auth::user()->btcWallet->account_id,
                "address" => $address,
                "amount" => $amount,
                "xpub" => $hd_wallet->xpub
            ]
        ]);
        /*  } catch (\Exception $e) {
            //\Log::info($e->getResponse()->getBody());
        } */
        $charge = Setting::where('name', 'bitcoin_charge')->first()->value;
        $res = json_decode($get_fees->getBody());
        return response()->json([
            "fee" => $res,
            "charge" => $charge
        ]);
    }


    public function sell(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'quantity' => 'required|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }


        /* if ($data['amount'] < 3) {
            return back()->with(['error' => 'Minimum trade amount is $3']);
        } */

        if (!Auth::user()->btcWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please a bitcoin wallet to continue'
            ]);
        }

        if (!Auth::user()->nairaWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please a Naira wallet to continue'
            ]);
        }

        /* if (Auth::user()->transactions()->where('status', 'waiting')->count() >= 3 || Auth::user()->transactions()->where('status', 'in progress')->count() >= 1) {
            return back()->with(['error' => 'You cant initiate a new transaction with more than 1 waiting or processing transactions']);
        } */

        try {
            $res = json_decode(file_get_contents("https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd"));
            $current_btc_rate = $res->bitcoin->usd;

            $trading_per = Setting::where('name', 'trading_btc_per')->first()->value;
            $service_fee = ($trading_per / 100) * $r->quantity;
            $tp = ($trading_per / 100) * $current_btc_rate;

            $main_rate = $current_btc_rate;
            $current_btc_rate -= $tp;

            $card = Card::find(102);
            $card_id = 102;
            $rates = $card->currency->first();

            $sell =  CardCurrency::where(['card_id' => $card_id, 'currency_id' => $rates->id, 'buy_sell' => 2])->first()->paymentMediums()->first();
            $trade_rate = json_decode($sell->pivot->payment_range_settings);
            $trade_rate = $trade_rate[0]->rate;

            $client = new Client();
            $url = env('TATUM_URL') . '/ledger/account/customer/' . Auth::user()->customer_id . '?pageSize=50';
            $res = $client->request('GET', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')]
            ]);

            $accounts = json_decode($res->getBody());

            $btc_wallet = Auth::user()->btcWallet;
            $btc_wallet->balance = $accounts[0]->balance->availableBalance;
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'msg' => 'An error occured, please try again'
            ]);
        }

        if ($r->quantity > $btc_wallet->balance) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient bitcoin wallet balance to initiate trade'
            ]);
        }

        //Get the other currencies using currnt rate (-tp)
        $usd = $r->quantity * $current_btc_rate;
        $ngn = $usd * $trade_rate;



        //Convert the charge t0 naira and subtract it from the amount paid
        $charge = Setting::where('name', 'bitcoin_sell_charge')->first()->value ?? 0;
        $charge = ($charge / 100) * $r->quantity;
        $charge_ngn = $charge * $r->current_rate * $trade_rate;

        $ngn -= $charge_ngn;

        $t = Auth::user()->transactions()->create([
            'card_id' => 102,
            'type' => 'sell',
            'amount' => $usd,
            'amount_paid' => $ngn,
            'quantity' => number_format((float) $r->quantity, 8),
            'card_price' => $current_btc_rate,
            'status' => 'waiting',
            'uid' => uniqid(),
            'user_email' => Auth::user()->email,
            'card' => 'bitcoin',
            'agent_id' => 1
        ]);


        $title = ucwords($t->type) . ' ' . $t->card;
        $body = 'Your order to ' . $t->type . ' ' . $t->card . ' worth of ₦' . number_format($t->amount_paid) . ' has been initiated successfully';
        Notification::create([
            'user_id' => Auth::user()->id,
            'title' => $title,
            'body' => $body,
        ]);

        $reference = \Str::random(5) . Auth::user()->id;
        $url = env('TATUM_URL') . '/ledger/transaction';

        $hd_wallet = HdWallet::where('currency_id', 1)->first();
        $service_wallet = Wallet::where(['name' => 'service', 'user_id' => 1, 'currency_id' => 1])->first();
        $charges_wallet = Wallet::where(['name' => 'charges', 'user_id' => 1, 'currency_id' => 1])->first();

        try {
            $send = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
                'json' =>  [
                    "senderAccountId" => Auth::user()->btcWallet->account_id,
                    "recipientAccountId" => $hd_wallet->account_id,
                    "amount" => number_format((float) $r->quantity, 8),
                    "anonymous" => false,
                    "compliant" => false,
                    "transactionCode" => $reference,
                    "paymentId" => $reference,
                    "baseRate" => 1,
                ]
            ]);

            $send_charge = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
                'json' =>  [
                    "senderAccountId" => $hd_wallet->account_id,
                    "recipientAccountId" => $service_wallet->account_id,
                    "amount" => number_format((float) $charge, 9),
                    "anonymous" => false,
                    "compliant" => false,
                    "transactionCode" => uniqid(),
                    "paymentId" => uniqid(),
                    "baseRate" => 1,
                    "senderNote" => 'hidden'
                ]
            ]);

            $send_service = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
                'json' =>  [
                    "senderAccountId" => $hd_wallet->account_id,
                    "recipientAccountId" => $charges_wallet->account_id,
                    "amount" => number_format((float) $service_fee, 9),
                    "anonymous" => false,
                    "compliant" => false,
                    "transactionCode" => uniqid(),
                    "paymentId" => uniqid(),
                    "baseRate" => 1,
                    "senderNote" => 'hidden'
                ]
            ]);
            $t->status = 'success';
            $t->save();
        } catch (\Exception $e) {
            //set transaction status to failed
            $t->status = 'failed';
            $t->save();
            \Log::info($e->getResponse()->getBody());
            //report($e);
            return response()->json([
                'success' => false,
                'msg' => 'An error occured, please try again'
            ]);

        }

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
        $nt->transaction_type_id = 4;
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
            'msg' => 'Bitcoin sold successfully'
        ]);

    }

    public function send(Request $r)
    {
        $data = $r->validate([
            'amount' => 'required|min:0',
            'address' => 'required|string',
            'pin' => 'required',
            'fees' => 'required',
        ]);


        if (!Auth::user()->btcWallet) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please create a bitcoin wallet to continue']);
        }

        //Check password
        if (!Hash::check($data['pin'], Auth::user()->pin)) {
            return back()->with(['error' => 'Incorrect bitcoin wallet pin']);
        }

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/customer/' . Auth::user()->customer_id . '?pageSize=50';
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);

        $accounts = json_decode($res->getBody());

        $user_wallet = Auth::user()->btcWallet;
        $user_wallet->balance = $accounts[0]->balance->availableBalance;

        $hd_wallet = HdWallet::where(['currency_id' => 1])->first();

        if ($data['amount'] > $user_wallet->balance) {
            return back()->with(['error' => 'Insufficient balance']);
        }

        $charge_wallet = Wallet::where(['name' => 'charges', 'user_id' => 1, 'currency_id' => 1])->first();

        $fees = $r->fees; // get fees
        $charge = Setting::where('name', 'bitcoin_charge')->first()->value;
        $total = $data['amount'] - $fees - $charge;

        $send_total = number_format((float)$total, 8);

        if ($send_total < 0) {
            return back()->with(['error' => 'Insufficient amount']);
        }
        //dd($send_total, $fees, $data['address']);
        $fees = number_format((float) $fees, 6);
        try {
            $url = env('TATUM_URL') . '/offchain/bitcoin/transfer';
            $send_btc = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
                'json' =>  [
                    "senderAccountId" => Auth::user()->btcWallet->account_id,
                    "address" => $data['address'],
                    "amount" => $send_total,
                    "compliant" => false,
                    "fee" => $fees,
                    "signatureId" => $hd_wallet->signature_id,
                    "xpub" => $hd_wallet->xpub,
                    "senderNote" => "Send BTC"
                ]
            ]);

            if ($charge > 0) {
                $url = env('TATUM_URL') . '/ledger/transaction';
                $send_charges = $client->request('POST', $url, [
                    'headers' => ['x-api-key' => env('TATUM_KEY')],
                    'json' =>  [
                        "senderAccountId" => Auth::user()->btcWallet->account_id,
                        "recipientAccountId" => $charge_wallet->account_id,
                        "amount" => $charge,
                        "anonymous" => false,
                        "compliant" => false,
                        "transactionCode" => uniqid(),
                        "paymentId" => uniqid(),
                        "baseRate" => 1,
                        "senderNote" => 'hidden'
                    ]
                ]);
            }

            $res = json_decode($send_btc->getBody());


            if (Arr::exists($res, 'signatureId')) {
                return back()->with(['success' => 'Bitcoin sent successfully']);
            } else {
                return back()->with(['error' => 'An error occured, please try again']);
            }
        } catch (\Exception $e) {
            //report($e);
            \Log::info($e->getResponse()->getBody());

            return back()->with(['error' => 'An error occured while processing the transaction please confirm the details and try again']);
        }
    }
}
