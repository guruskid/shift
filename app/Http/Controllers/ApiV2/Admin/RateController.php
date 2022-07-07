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
}
