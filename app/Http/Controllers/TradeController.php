<?php

namespace App\Http\Controllers;

use App\Card;
use App\CardCurrency;
use App\Events\NewTransaction;
use App\Http\Resources\CardResource;
use App\Mail\DantownNotification;
use App\Notification;
use App\Pop;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TradeController extends Controller
{
    public function assets()
    {
        $assets = Card::where('buyable', 1)->orWhere('sellable', 1)->get();


        return view('user.assets', compact(['assets']));
        /* return response()->json($assets); */
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
        $card_rates = json_encode($card_rates);
        if (\Str::lower($card->name) == 'bitcoins' || \Str::lower($card->name) == 'bitcoin') {
            return $this->bitcoin($card->id);
        }
        if (\Str::lower($card->name) == 'ether' || \Str::lower($card->name) == 'ethereum') {
            return $this->ethereum($card->id);
        }
        return view('user.gift_card_calculator', compact(['card_rates', 'buy_sell']));
    }

    public function bitcoin($card_id)
    {
        $card = Card::find($card_id);
        $rates = $card->currency->first();
        $sell =  CardCurrency::where(['card_id' => $card_id, 'currency_id' => $rates->id, 'buy_sell' => 2])->first()->paymentMediums()->first();
        $rates->sell = json_decode($sell->pivot->payment_range_settings);

        $buy =  CardCurrency::where(['card_id' => $card_id, 'currency_id' => $rates->id, 'buy_sell' => 1])->first()->paymentMediums()->first();
        $rates->buy = json_decode($buy->pivot->payment_range_settings);

        return view('newpages.bitcoin', compact(['rates', 'card']));
    }

    public function ethereum($card_id)
    {
        $card = Card::find($card_id);
        $rates = $card->currency->first();
        $sell =  CardCurrency::where(['card_id' => $card_id, 'currency_id' => $rates->id, 'buy_sell' => 2])->first()->paymentMediums()->first();
        $rates->sell = json_decode($sell->pivot->payment_range_settings);

        $buy =  CardCurrency::where(['card_id' => $card_id, 'currency_id' => $rates->id, 'buy_sell' => 1])->first()->paymentMediums()->first();
        $rates->buy = json_decode($buy->pivot->payment_range_settings);

        return view('newpages.ethereum', compact(['rates', 'card']));

    }

    /* Trade GiftCards */
    public function trade(Request $r)
    {
        if (count($r->cards) != count($r->totals)) {
            return back()->with(['error' => 'Invalid trade details']);
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

        if ($r->has('card_images')) {
            foreach ($r->card_images as $file) {
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
        }

        return redirect()->route('user.transactions')->with(['success' => 'Transaction initiated']);
    }

    /* Trade Crypto */
    public function tradeCrypto(Request $r)
    {

        /* dd($r->all()); */
        $data = $r->validate([
            'card_id' => 'required|integer',
            'type' => 'required|string',
            'amount' => 'required',
            'amount_paid' => 'required',
            'quantity' => 'required',
            'wallet_id' => 'nullable|string'
        ]);
        $online_agent = User::where('role', 888)->where('status', 'active')->inRandomOrder()->first();
        $data['status'] = 'waiting';
        $data['uid'] = uniqid();
        $data['user_email'] = Auth::user()->email;
        $data['user_id'] = Auth::user()->id;
        $data['card'] = Card::find($r->card_id)->name;
        $data['agent_id'] = $online_agent->id;


        $t = Transaction::create($data);
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
        }

        return redirect()->route('user.transactions');
    }
}
