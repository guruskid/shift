<?php

namespace App\Http\Controllers\Api;

use App\Card;
use App\CardCurrency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CardResource;

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
        return response()->json([
            'success' => true,
            'data' => $card_rates
        ]);
    }


    public function trade(Request $r)
    {
        if (Auth::user()->transactions()->where('status', 'waiting')->count() >= 3 || Auth::user()->transactions()->where('status', 'in progress')->count() >= 3) {
            return response()->json([
                'success' => false,
                'msg' => 'You cant initiate a new transaction with more than 3 waiting or processing transactions',
            ]);
        }

        if ($r->buy_sell == 'buy' && Auth::user()->nairaWallet->amount < $r->amount_paid) {
            return response()->json([
                'success' => false,
                'msg' => 'Insufficient wallet balance to complete this transaction, please topup and try again ',
            ]);
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
}
