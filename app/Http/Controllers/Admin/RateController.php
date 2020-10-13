<?php

namespace App\Http\Controllers\Admin;

use App\Card;
use App\CardCurrency;
use App\CardCurrencyPaymentMedium;
use App\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CardResource;
use App\PaymentMedium;

class RateController extends Controller
{
    public function index()
    {


        $cards = Card::orderBy('name', 'asc')->get();
        $currencies = Currency::orderBy('name', 'asc')->get();
        $card_types = PaymentMedium::orderBy('name', 'asc')->get();
        $rates = CardCurrencyPaymentMedium::orderBy('card_currency_id', 'desc')->get()->each(function ($rate){
            $rate->card_name = $rate->cardCurrency->card->name;
            $rate->currency_name = $rate->cardCurrency->currency->name;
            $rate->rates = \json_decode($rate->payment_range_settings);

            return true;
        });

        /* return new CardResource(Card::find(3)); */


        /* dd($rates); */
        return view('admin.rates.index', compact(['cards', 'currencies', 'card_types', 'rates']));
    }

    public function store(Request $request)
    {
        $request->validate([
            'card_id' => 'required|integer',
            'currency_id' => 'required|integer',
            'payment_medium_id' => 'required|integer',
            'buy_sell' => 'required|integer',
        ]);

        $data = $request->only(['card_id', 'currency_id', 'buy_sell']);

        $cardCurrency = CardCurrency::firstOrCreate($data);

        $value = ['1', '2', '3'];
        $rate = ['1000', '2000', '3000' ];

        $rates = [];
        foreach ($value as $key => $value ) {
            $s = [
                'value' => $value,
                'rate' => $rate[$key]
            ];
            array_push($rates, $s);
        }

        $rate = CardCurrencyPaymentMedium::updateOrCreate(
            [ 'card_currency_id' =>$cardCurrency->id, 'payment_medium_id' => $request->payment_medium_id ],
            [ 'payment_range_settings' => json_encode($rates)]
        );

        return back()->with(['success' => 'Rate added']);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
