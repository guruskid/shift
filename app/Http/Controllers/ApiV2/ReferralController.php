<?php

namespace App\Http\Controllers\ApiV2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\LiveRateController;
use App\Setting;
use App\Card;
use App\CardCurrency;
use App\CryptoRate;
use App\HdWallet;
use App\Notification;
use GuzzleHttp\Client;
use App\Mail\GeneralTemplateOne;
use Illuminate\Support\Facades\Mail;
use App\Wallet;
use App\FeeWallet;
use App\NairaWallet;
use App\NairaTransaction;
use App\Http\Controllers\GeneralSettings;

class ReferralController extends Controller
{
    public function create() {
        if (isset(Auth::user()->referral_code)) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a referral link'
            ]);
        }
        $length = 6;
        $randomString = substr(str_shuffle(md5(time())),0,$length);
        $user = Auth::user();
        $user->referral_code = Str::upper($randomString);
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Referral link generated successfully'
        ]);
    }

    public function getBalance() {
        return response()->json([
            'success' => true,
            'balance' => number_format((float) Auth::user()->referral_wallet, 8)
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

        if (!Auth::user()->btcWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please a bitcoin wallet to continue'
            ]);
        }

        if (!Auth::user()->nairaWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please create a Naira wallet to continue'
            ]);
        }

        $client = new Client();

        $current_btc_rate =  LiveRateController::btcRate();
        $trading_per = Setting::where('name', 'trading_btc_per')->first()->value;
        $service_fee = ($trading_per / 100) * $r->quantity;


        $card = Card::find(102);
        $card_id = 102;

        $trade_rate = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;

        $referral_wallet = Auth::user()->referral_wallet;

        if ($r->quantity > $referral_wallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient referral balance to initiate trade'
            ]);
        }

        //Get the other currencies using currnt rate (-tp)
        $usd = $r->quantity * $current_btc_rate;
        $ngn = $usd * $trade_rate;


        //Convert the charge to naira and subtract it from the amount paid
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

        // ///////////////////////////////////////////////////////////
        $finalamountcredited = Auth::user()->nairaWallet->amount + $t->amount_paid;
        $title = 'Sell Order Successful';
        $body = 'Your order to sell 0.07 ' . $t->card . ' has been filled and your Naira wallet has been credited with₦' . number_format($t->amount_paid) . '<br>
        Your new  balance is ' . $finalamountcredited . '.<br>
        Date: ' . now() . '.<br><br>

        Thank you for Trading with Dantown.

        ';

        $btn_text = '';
        $btn_url = '';

        $name = (Auth::user()->first_name == " ") ? Auth::user()->username : Auth::user()->first_name;
        $name = explode(' ', $name);
        $firstname = ucfirst($name[0]);
        Mail::to(Auth::user()->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $firstname));

        // /////////////////////////////////////////////

        $reference = \Str::random(5) . Auth::user()->id;
        $url = env('TATUM_URL') . '/ledger/transaction';

        $hd_wallet = HdWallet::where('currency_id', 1)->first();
        $service_wallet = Wallet::where(['name' => 'service', 'user_id' => 1, 'currency_id' => 1])->first();
        $charges_wallet = Wallet::where(['name' => 'charges', 'user_id' => 1, 'currency_id' => 1])->first();

        $referral_wallet = FeeWallet::where('name','referral_pool')->first();

        try {
            $send = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
                'json' =>  [
                    "senderAccountId" => $referral_wallet->account_id,
                    "recipientAccountId" => $hd_wallet->account_id,
                    "amount" => number_format((float) $r->quantity, 8),
                    "anonymous" => false,
                    "compliant" => false,
                    "transactionCode" => $reference,
                    "paymentId" => $reference,
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
            $t->status = 'success';
            $t->save();
        } catch (\Exception $e) {
            //set transaction status to failed
            return $e->getResponse()->getBody();
            $t->status = 'failed';
            $t->save();
            \Log::info($e->getResponse()->getBody());
            //report($e);
            return response()->json([
                'success' => false,
                'msg' => 'An error occured, please try again'
            ]);
        }

        $user = Auth::user();
        $user->referral_wallet = $user->referral_wallet - $r->quantity;
        $user->save();

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
        $nt->transaction_type_id = 20;
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
            'message' => 'Bitcoin sold successfully'
        ]);
    }

    public function referralSystemStatus() {
        $status = GeneralSettings::getSetting('REFERRAL_ACTIVE')->settings_value;
        return response()->json([
            'success' => true,
            'status' => ($status == 1) ? 'active' : 'in-active'
        ]);
    }
}
