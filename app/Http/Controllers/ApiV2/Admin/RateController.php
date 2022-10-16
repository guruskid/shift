<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\Card;
use App\CardCurrencyPaymentMedium;
use App\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LiveRateController;
use App\PaymentMedium;

class RateController extends Controller
{
    public function index()
    {
        $cards = Card::orderBy('name', 'asc')->get();
        $currencies = Currency::orderBy('name', 'asc')->get();
        $card_types = PaymentMedium::orderBy('name', 'asc')->get();
        $rates = CardCurrencyPaymentMedium::orderBy('card_currency_id', 'desc')->get()->each(function ($rate) {
            $rate->card_name = $rate->cardCurrency->card->name;
            $rate->currency_name = $rate->cardCurrency->currency->name;
            $rate->rates = \json_decode($rate->payment_range_settings);

            return true;
        });
        $rates = $rates->sortBy('card_name');
        $usd_ngn = LiveRateController::usdNgn();

        return response()->json([
            'success' => true,
            'cards' => $cards,
            'currencies' => $currencies,
            'card_types' => $card_types,
            'usd_ngn' => $usd_ngn,
            'rates' => $rates
        ]);

    }

    public function deleteRate($id)
    {

        $rate = CardCurrencyPaymentMedium::find($id);
        if (is_null($rate)) {
            return response()->json(["success" => false, 'message' => "rate does not exist"], 404);
        }

        $rate->delete();
        return response()->json([
            'success' => true,
            'message' => 'Rate has been successfully deleted!'
        ], 200);




    }


    public function updateRate(Request $request)
    {

        $request->validate([
            'card_currency_id' => 'required|integer',
            'values' => 'required',
            'rates' => 'required'
        ]);


        $card_currency = CardCurrency::findOrFail($request->card_currency_id);
        $value = $request->values;
        $rate = $request->rates;

        $rates = [];
        foreach ($value as $key => $value) {
            if ($value != null && $rate[$key] != null) {
                $s = [
                    'value' => $value,
                    'rate' => $rate[$key]
                ];
                array_push($rates, $s);
            }
        }


            $rate = CardCurrencyPaymentMedium::updateOrCreate(
                ['card_currency_id' => $card_currency->id, 'payment_medium_id' => $request->payment_medium_id],
                ['payment_range_settings' => json_encode($rates)]
            );


        return response()->json([
            'success' => true,
            'message' => 'Rate has been successfully updated'
        ]);
    }
}
