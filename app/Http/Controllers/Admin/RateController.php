<?php

namespace App\Http\Controllers\Admin;

use App\Card;
use App\CardCurrency;
use App\CardCurrencyPaymentMedium;
use App\CryptoRate;
use App\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LiveRateController;
use App\Http\Resources\CardResource;
use App\PaymentMedium;
use Illuminate\Support\Facades\Auth;

class RateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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
        $usd_ngn = LiveRateController::usdNgn(false);

        return view('admin.rates.index', compact(['cards', 'currencies', 'card_types', 'usd_ngn', 'rates']));
    }

    public function filter(Request $request)
    {
        if($request->card_id == null AND $request->currency_id == null AND $request->payment_medium_id == null){
            return back()->with(['error' => 'Nothing Selected']);
        }

        $selected_card = null;
        $selected_currency = null;
        $selected_Payment_medium = null;
        $cards = Card::orderBy('name', 'asc')->get();
        $currencies = Currency::orderBy('name', 'asc')->get();
        $card_types = PaymentMedium::orderBy('name', 'asc')->get();
        $rates = CardCurrencyPaymentMedium::orderBy('card_currency_id', 'desc');

        if($request->card_id != null){
            $selected_card = Card::find($request->card_id) ?? null;
            $rates = $rates->whereHas('cardCurrency',function ($query) use ($request) {
                $query->whereHas('card',function ($query) use ($request){
                    $query->where('id',$request->card_id);
                });
            });
        }

        if($request->currency_id != null){
            $selected_currency = Currency::find($request->currency_id) ?? null;
            $rates = $rates->whereHas('cardCurrency',function ($query) use ($request) {
                $query->whereHas('currency',function ($query) use ($request){
                    $query->where('id',$request->currency_id);
                });
            });
        }

        if($request->payment_medium_id != null){
            $selected_Payment_medium = PaymentMedium::find($request->payment_medium_id) ?? null;
            $rates = $rates->whereHas('paymentMedium',function ($query) use ($request) {
                    $query->where('id',$request->payment_medium_id);
            });
        }


        $rates = $rates->get()->each(function ($rate) {
            $rate->card_name = $rate->cardCurrency->card->name;
            $rate->currency_name = $rate->cardCurrency->currency->name;
            $rate->rates = \json_decode($rate->payment_range_settings);

            return true;
        });
        $rates = $rates->sortBy('card_name');
        $usd_ngn = LiveRateController::usdNgn();

        return view('admin.rates.index', compact(['cards', 'currencies', 'card_types',
         'usd_ngn', 'rates','selected_card','selected_currency','selected_Payment_medium']));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role == 888) {
            return redirect()->route('admin.dashboard');
        }

        $request->validate([
            'card_id' => 'required|integer',
            'currency_id' => 'required|integer',
            'payment_medium_id' => 'required|integer',
            'buy_sell' => 'required|integer',
        ]);

        $data = $request->only(['card_id', 'currency_id', 'buy_sell']);

        $cardCurrency = CardCurrency::firstOrCreate($data);

        $rates = [
            ['value' => 1,
            'rate' => 1]
        ];

        $rate = CardCurrencyPaymentMedium::updateOrCreate(
            ['card_currency_id' => $cardCurrency->id, 'payment_medium_id' => $request->payment_medium_id],
            ['payment_range_settings' => json_encode($rates)]
        );

        return back()->with(['success' => 'Rate added']);
    }


    public function update(Request $request)
    {

        $card_currency = CardCurrency::findOrFail($request->cc_id);
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

        if (Auth::user()->role == '999') {
            $rate = CardCurrencyPaymentMedium::updateOrCreate(
                ['card_currency_id' => $card_currency->id, 'payment_medium_id' => $request->payment_medium_id],
                ['payment_range_settings' => json_encode($rates), 'percentage_deduction' => $request['percentage_deduction']]
            );
        }else {
            $rate = CardCurrencyPaymentMedium::updateOrCreate(
                ['card_currency_id' => $card_currency->id, 'payment_medium_id' => $request->payment_medium_id],
                ['payment_range_settings' => json_encode($rates)]
            );
        }

        return redirect()->route('admin.rates')->with(['success' => 'Rates Updated']);
    }

    public function updateUsd(Request $request)
    {
        CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->update([
            'rate' => $request->rate
        ]);

        return back()->with(['success' => 'Usd rate updated']);
    }


    public function deleteRate($id)
    {
        $rate = CardCurrencyPaymentMedium::find($id)->delete();
        return response()->json(true);
    }
}
