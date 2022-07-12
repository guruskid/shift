<?php

namespace App\Http\Controllers\Api;

use App\Card;
use App\CardCurrency;
use App\CardCurrencyPaymentMedia;
use App\Currency;
use App\Events\NewTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CardResource;
use App\Mail\DantownNotification;
use App\Notification;
use App\PaymentMedia;
use App\Pop;
use App\Transaction;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TradeController extends Controller
{
    public function assets()
    {
        $assets = Card::where('buyable', 1)->orWhere('sellable', 1)->get();

        return response()->json([
            'success' => true,
            'data' => $assets
        ]);
    }

    public function assetRates($buy_sell, $card_id, $card_name)
    {
        if ($buy_sell == 'sell') {
            $buy_sell = 2;
        } else {
            $buy_sell = 1;
        }
        $card = Card::find($card_id);
        $card_rates =  new CardResource($card);

        $client = new Client();
        $url = env('TATUM_URL') . '/tatum/rate/BTC?basePair=USD';
        $res = $client->request('GET', $url, ['headers' => ['x-api-key' => env('TATUM_KEY')]]);
        $res = json_decode($res->getBody());
        $btc_real_time = (int)$res->value;

        return response()->json([
            'success' => true,
            'data' => $card_rates,
            'btc_current_rate' => $btc_real_time
        ]);
    }


    public function tradeGiftCard(Request $r)
    {
        /* if (Auth::user()->transactions()->where('status', 'waiting')->count() >= 3 || Auth::user()->transactions()->where('status', 'in progress')->count() >= 3) {
            return response()->json([
                'success' => false,
                'msg' => 'You cant initiate a new transaction with more than 3 waiting or processing transactions',
            ]);
        }*/

        if ($r->buy_sell == 'buy') {
            return response()->json([
                'success' => false,
                'msg' => 'Service not currently available ',
            ]);
        }

        if ($r->buy_sell == 'buy' && Auth::user()->nairaWallet->amount < $r->amount_paid) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient wallet balance to complete this transaction, please topup and try again ',
            ]);
        }

        /* $image = base64_encode(Storage::get('public/assets/AMAZON.png'));
        return response()->json($image); */

        $batch_id = uniqid();
        $online_agent = User::where('role', 888)->where('status', 'active')->inRandomOrder()->first();

         foreach ($r->trades as $key => $i) {
            $card = Card::where('name', $i['cardName'])->first();

            // $rate = json_decode($card->currency->where('name',$i['currency'])->first()->cardCurrency->where('card_id',$card->id)->first()->cardPaymentMedia->first()->payment_range_settings);

            $cardType = $i['cardType'];
            $payment_medium_id = PaymentMedia::where('name',$cardType)->first()->id;
            $currency_id = Currency::where('name',$i['currency'])->first()->id;
            $card_currency_id = CardCurrency::where(['card_id' => $card->id, 'currency_id' => $currency_id])->first()->id;
            $rates = CardCurrencyPaymentMedia::where(['payment_medium_id' => $payment_medium_id, 'card_currency_id' => $card_currency_id])->first();
            $rates = json_decode($rates->payment_range_settings);

            $t_amount = 0;
            foreach ($rates as $value) {
                if ($value->value == $i['cardValue']) {
                    $t_amount = $i['cardQuantity'] * $value->rate;
                    break;
                }
            }
            $commission = $t_amount - $i['cardTotal'];

            $t = new Transaction();
            $t->uid = uniqid();
            $t->user_email = Auth::user()->email;
            $t->user_id = Auth::user()->id;
            $t->card = $i['cardName'];
            $t->card_id = $card->id;
            $t->country = $i['currency'];
            $t->type = $r->buy_sell;
            $t->amount = $i['cardValue'];
            $t->amount_paid = $i['cardTotal'];
            $t->agent_id = $online_agent->id;
            $t->status = 'waiting';
            $t->batch_id = $batch_id;
            $t->card_type = $i['cardType'];
            $t->quantity = $i['cardQuantity'];
            $t->card_price = $i['cardPrice'];
            $t->commission = $commission;
            $t->save();
        }

        if ($r->has('images')) {
            foreach ($r->images as $file) {
                $file = $file['image'];
                $folderPath = public_path('storage/pop/');
                $image_base64 = base64_decode($file);

                $imageName = time() . uniqid() . '.png';
                $imageFullPath = $folderPath . $imageName;

                file_put_contents($imageFullPath, $image_base64);

                $p = new Pop();
                $p->user_id = Auth::user()->id;
                $p->transaction_id = $batch_id;
                $p->path = $imageName;
                $p->save();
            }
        }

        // broadcast(new NewTransaction($t))->toOthers();

        $title = ucwords($t->type) . ' ' . $t->card;
        $body = 'Your order to ' . $t->type . ' ' . $t->card . ' worth of ₦' . number_format($t->amount_paid) . ' has been initiated successfully';

         // Firebase Push Notification
         $fcm_id = Auth::user()->fcm_id;
         if (isset($fcm_id)) {
             try {
                 FirebasePushNotificationController::sendPush($fcm_id,$title,$body);
             } catch (\Throwable $th) {
                 //throw $th;
             }
         }

        Notification::create([
            'user_id' => Auth::user()->id,
            'title' => $title,
            'body' => $body,
        ]);
        if (Auth::user()->notificationSetting->trade_email == 1) {
            Mail::to(Auth::user()->email)->send(new DantownNotification($title, $body, 'Transaction History', route('user.transactions')));
        }

        return response()->json(['success'=> true]);
    }
}
