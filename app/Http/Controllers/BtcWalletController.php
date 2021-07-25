<?php

namespace App\Http\Controllers;

use App\Card;
use App\CardCurrency;
use App\HdWallet;
use App\NairaTransaction;
use App\NairaWallet;
use App\Notification;
use App\Setting;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RestApis\Blockchain\Constants;

class BtcWalletController extends Controller
{
    public function __construct()
    {
        $this->instance = $instance = new \RestApis\Factory(env('BITCOIN_WALLET_API_KEY'));
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

        $btc_wallet_bal  = Auth::user()->bitcoinWallet->balance ?? 0;
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


        try {
            $fees_req = $this->instance->transactionApiBtcNewTransactionFee()->get(Constants::$BTC_MAINNET);
        } catch (\Throwable $th) {
            return back()->with(['error' => 'Network busy']);
        }

        $fees = $fees_req->payload->recommended;

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
            "json" => [ "id" => Auth::user()->btcWallet->account_id ]
        ]);

        $transactions = json_decode($get_txns->getBody());
        foreach ($transactions as $t ) {
            $x = \Str::limit($t->created, 10, '') ;
            $time = \Carbon\Carbon::parse((int)$x);
            $t->created = $time->setTimezone('Africa/Lagos');
        }

        //dd(time(), $transactions[0]->created, $time->format('d M Y h:ia') );

        return view('newpages.bitcoin-wallet', compact('fees', 'btc_wallet', 'transactions', 'btc_rate', 'charge', 'total_fees'));
    }


    public function sell(Request $r)
    {
        $data = $r->validate([
            'card_id' => 'required|integer',
            'type' => 'required|string',
            'amount' => 'required',
            'amount_paid' => 'required',
            'quantity' => 'required',

        ]);


        /* if ($data['amount'] < 3) {
            return back()->with(['error' => 'Minimum trade amount is $3']);
        } */

        if (!Auth::user()->btcWallet) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please a bitcoin wallet to continue']);
        }

        if (!Auth::user()->nairaWallet) {
            return back()->with(['error' => 'Please create a Naira wallet to continue']);
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
            return back()->with(['success' => false, 'message' => 'An error occured, please try again']);
        }

        if ($r->quantity > $btc_wallet->balance) {
            return back()->with(['error' => 'Insufficient bitcoin wallet balance to initiate trade']);
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
            'quantity' => $r->quantity,
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
        $hd_wallet = HdWallet::where('currency', 'BTC')->first();

        try {
            $send = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
                'json' =>  [
                    "senderAccountId" => Auth::user()->btcWallet->account_id,
                    "recipientAccountId" => $hd_wallet->account_id,
                    "amount" => $r->quantity,
                    "anonymous" => false,
                    "compliant" => false,
                    "transactionCode" => $reference,
                    "paymentId" => $reference,
                    "baseRate" => 1,
                ]
            ]);
            $t->status = 'success';
            $t->save();
        } catch (\Exception $e) {
            //set transaction status to failed
            $t->status = 'failed';
            $t->save();
            report($e);
            return back()->with(['error' => 'An error occured, please try again']);
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

        return redirect()->route('user.transactions');
    }
}
