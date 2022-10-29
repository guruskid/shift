<?php

namespace App\Http\Controllers\ApiV2\Admin\SeniorAccountant;

use App\CardCurrency;
use App\CardCurrencyPaymentMedium;
use App\CryptoRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RateController extends Controller
{

    public function deleteRate($id)
    {
        if(!CardCurrencyPaymentMedium::find($id)){
            return response()->json([
                'success' => false,
                'message' => 'Rate does not exist!'
            ]);

        }
        $rate = CardCurrencyPaymentMedium::find($id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Rate has been successfully deleted!'
        ]);
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

    public function updateUsd(Request $request)
    {
        CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->update([
            'rate' => $request->rate
        ]);

        return back()->with(['success' => 'Usd rate updated']);
    }
}
