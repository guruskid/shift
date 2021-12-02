<?php

namespace App\Http\Controllers;

use App\Card;
use App\CardCurrency;
use App\Events\NewTransaction;
use App\Http\Resources\CardResource;
use App\Mail\DantownNotification;
use App\Notification;
use App\Pop;
use App\Setting;
use App\Transaction;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use \App\Http\Controllers\GeneralSettings;
use App\Mail\GeneralTemplateOne;

class TradeController extends Controller
{
    public function assets($asset_type = 'all')
    {
        if ($asset_type == 'gift cards') {
            $assets = Card::where('is_crypto', 0)->where(function ($query) {
                $query->where('buyable', 1)->orWhere('sellable', 1);
            })->get();
        } elseif ($asset_type == 'digital assets') {
            $assets = Card::where('is_crypto', 1)->where(function ($query) {
                $query->where('buyable', 1)->orWhere('sellable', 1);
            })->get();
        } else {
            $assets = Card::where('buyable', 1)->orWhere('sellable', 1)->get();
        }

        /* $balance = Auth::user()->email; */

        return view('user.assets', compact(['assets', 'asset_type']));
        /* return response()->json($assets); */
    }

    /*  */
    public function assetRates($buy_sell, $card_id, $card_name)
    {
        if (!Auth::user()->nairaWallet) {
            return redirect()->route('user.portfolio')->with(['error' => 'Please create a Naira Wallet to continue']);
        }
        if ($buy_sell == 'sell') {
            $buy_sell = 2;
        } else {
            $buy_sell = 1;
        }
        $card = Card::find($card_id);
        $card_rates =  new CardResource($card);
        $card_rates = json_encode($card_rates);
        if (\Str::lower($card->name) == 'bitcoins' || \Str::lower($card->name) == 'bitcoin') {
            return $this->bitcoin($card->id, $buy_sell);
        }
        if (\Str::lower($card->name) == 'ether' || \Str::lower($card->name) == 'ethereum') {
            return $this->ethereum($card->id);
        }

        $sell_gc_setting = GeneralSettings::getSetting('GIFTCARD_SELL');
        $buy_gc_setting = GeneralSettings::getSetting('GIFTCARD_BUY');

        return view('user.gift_card_calculator', compact(['card_rates', 'buy_sell', 'sell_gc_setting', 'buy_gc_setting']));
    }

    public function bitcoin($card_id, $buy_sell = 1)
    {
        $card = Card::find($card_id);
        $rates = $card->currency->first();
        $sell =  CardCurrency::where(['card_id' => $card_id, 'currency_id' => $rates->id, 'buy_sell' => 2])->first()->paymentMediums()->first();
        $rates->sell = json_decode($sell->pivot->payment_range_settings);

        $buy =  CardCurrency::where(['card_id' => $card_id, 'currency_id' => $rates->id, 'buy_sell' => 1])->first()->paymentMediums()->first();
        $rates->buy = json_decode($buy->pivot->payment_range_settings);

        $client = new Client();
        // $url = env('TATUM_URL') . '/tatum/rate/BTC?basePair=USD';
        // $res = $client->request('GET', $url, ['headers' => ['x-api-key' => env('TATUM_KEY')]]);
        // $res = json_decode($res->getBody());
        // $btc_real_time = $res->value;

        // $trading_per = Setting::where('name', 'trading_btc_per')->first()->value;
        // $tp = ($trading_per / 100) * $btc_real_time;
        $btc_real_time = LiveRateController::btcRate();


        $url = env('TATUM_URL') . '/ledger/account/' . Auth::user()->btcWallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);

        $accounts = json_decode($res->getBody());

        $btc_wallet = Auth::user()->btcWallet;
        $btc_wallet->balance = $accounts->balance->availableBalance;
        $btc_wallet->usd = $btc_wallet->balance  * $btc_real_time;

        $charge = Setting::where('name', 'bitcoin_sell_charge')->first()->value;

        $sell_btc_setting = GeneralSettings::getSetting('SELL_BTC');

        $buy_btc_setting = GeneralSettings::getSetting('BUY_BTC');


        return view('newpages.bitcoin', compact(['rates', 'card', 'btc_real_time', 'charge', 'buy_sell', 'sell_btc_setting', 'buy_btc_setting']));

    }

    public function ethereum($card_id)
    {
        $card = Card::find($card_id);
        $rates = $card->currency->first();
        $sell =  CardCurrency::where(['card_id' => $card_id, 'currency_id' => $rates->id, 'buy_sell' => 2])->first()->paymentMediums()->first();
        $rates->sell = json_decode($sell->pivot->payment_range_settings);

        $buy =  CardCurrency::where(['card_id' => $card_id, 'currency_id' => $rates->id, 'buy_sell' => 1])->first()->paymentMediums()->first();
        $rates->buy = json_decode($buy->pivot->payment_range_settings);

        return view('newpages.ethereum', compact(['rates']));
    }

    /* Trade GiftCards */
    public function trade(Request $r)
    {
        if (count($r->cards) != count($r->totals)) {
            return back()->with(['error' => 'Invalid trade details']);
        }

         if (Auth::user()->transactions()->where('status', 'waiting')->count() >= 3 || Auth::user()->transactions()->where('status', 'in progress')->count() >= 3) {
            return back()->with(['error' => 'You cant initiate a new transaction with more than 3 waiting or processing transactions']);
        }

        if ($r->buy_sell == 1 && Auth::user()->nairaWallet->amount < $r->amount_paid) {
            return back()->with(['error' => 'Insufficient wallet balance to complete this transaction ']);
        }

        $card = Card::where('name', $r->cards[0])->first();
        $batch_id = uniqid();
        $online_agent = User::where('role', 888)->where('status', 'active')->inRandomOrder()->first();
        $r->buy_sell == 1 ? $buy_sell = 'buy' : $buy_sell = 'sell';

        foreach ($r->cards as $i => $total) {
            $t = new Transaction();
            $t->uid = uniqid();
            $t->user_email = Auth::user()->email;
            $t->user_id = Auth::user()->id;
            $t->card = $card->name;
            $t->card_id = $card->id;
            $t->country = $r->currencies[$i];
            $t->type = $buy_sell;
            $t->amount = $r->values[$i];
            $t->amount_paid = $r->totals[$i];
            $t->agent_id = $online_agent->id;
            $t->status = 'waiting';
            $t->batch_id = $batch_id;
            $t->card_type = $r->card_types[$i];
            $t->quantity = $r->quantities[$i];
            $t->card_price = $r->prices[$i];
            $t->save();
        }
        /* dd($r->card_images); */
        if ($r->has('card_images')) {
            foreach ($r->card_images as $file) {
                /* dd($file); */
                $extension = $file->getClientOriginalExtension();
                $filenametostore = time() . uniqid() . '.' . $extension;
                Storage::put('public/pop/' . $filenametostore, fopen($file, 'r+'));
                $p = new Pop();
                $p->user_id = Auth::user()->id;
                $p->transaction_id = $batch_id;
                $p->path = $filenametostore;
                $p->save();
            }
        }

        broadcast(new NewTransaction($t))->toOthers();

        $title = ucwords($t->type) . ' ' . $t->card;
        $body = 'Your order to ' . $t->type . ' ' . $t->card . ' worth of ₦' . number_format($t->amount_paid) . ' has been initiated successfully';
        Notification::create([
            'user_id' => Auth::user()->id,
            'title' => $title,
            'body' => $body,
        ]);
        if (Auth::user()->notificationSetting->trade_email == 1) {
            Mail::to(Auth::user()->email)->send(new DantownNotification($title, $body, 'Transaction History', route('user.transactions')));

            $title = 'Transaction Successful';

            $btn_text = '';
            $btn_url = '';

            $name = Auth::user()->first_name;
            Mail::to(Auth::user()->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $name));

        }


        return redirect()->route('user.transactions')->with(['success' => 'Transaction initiated']);
    }

    /* Trade Crypto no longer in use for btc */
    public function tradeCrypto(Request $r)
    {

        $data = $r->validate([
            'card_id' => 'required|integer',
            'type' => 'required|string',
            'amount' => 'required',
            'amount_paid' => 'required',
            'quantity' => 'required',
        ]);

        /* if (Auth::user()->transactions()->where('status', 'waiting')->count() >= 3 || Auth::user()->transactions()->where('status', 'in progress')->count() >= 3) {
            return back()->with(['error' => 'You cant initiate a new transaction with more than 3 waiting or processing transactions']);
        } */

        if ($r->type == 'buy' && Auth::user()->nairaWallet->amount < $r->amount_paid) {
            return back()->with(['error' => 'Insufficient wallet balance to complete this transaction ']);
        }
        $online_agent = User::where('role', 888)->where('status', 'active')->inRandomOrder()->first();
        $data['status'] = 'waiting';
        $data['uid'] = uniqid();
        $data['user_email'] = Auth::user()->email;
        $data['user_id'] = Auth::user()->id;
        $data['card'] = Card::find($r->card_id)->name;
        $data['agent_id'] = $online_agent->id;

        $t = Transaction::create($data);
        if ($r->type == 'sell' && $r->has('card_images')) {
            foreach ($r->card_images as $file) {
                $extension = $file->getClientOriginalExtension();
                $filenametostore = time() . uniqid() . '.' . $extension;
                Storage::put('public/pop/' . $filenametostore, fopen($file, 'r+'));
                $p = new Pop();
                $p->user_id = Auth::user()->id;
                $p->transaction_id = $t->id;
                $p->path = $filenametostore;
                $p->save();
            }
            //$t->status = 'in progress';
            //$t->save();
        }
        try {
            broadcast(new NewTransaction($t))->toOthers();
        } catch (\Exception $e) {
            report($e);
        }
        $title = ucwords($t->type) . ' ' . $t->card;
        $body = 'Your order to ' . $t->type . ' ' . $t->card . ' worth of ₦' . number_format($t->amount_paid) . ' has been initiated successfully';
        Notification::create([
            'user_id' => Auth::user()->id,
            'title' => $title,
            'body' => $body,
        ]);
        if (Auth::user()->notificationSetting->trade_email == 1) {
            Mail::to(Auth::user()->email)->send(new DantownNotification($title, $body, 'Transaction History', route('user.transactions')));
            $btn_text = '';
            $btn_url = '';

            $name = Auth::user()->first_name;
            Mail::to(Auth::user()->email)->send(new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $name));

        }

        return redirect()->route('user.transactions');
    }
}
