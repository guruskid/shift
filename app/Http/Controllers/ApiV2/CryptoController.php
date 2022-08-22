<?php

namespace App\Http\Controllers\ApiV2;

use App\CryptoCurrency;
use App\Http\Controllers\Api\BtcWalletController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CryptoHelperController;
use App\Http\Controllers\LiveRateController;
use App\Setting;

class CryptoController extends Controller
{
    public function index()
    {
        $bitcoin = CryptoCurrency::find(1);
        $usdt = CryptoCurrency::find(7);

        $bitcoin->wallet = CryptoHelperController::balance(1);
        $btc_rates = BtcWalletController::fees()->getData();
        $bitcoin->rates = [
            'send_charge' => $btc_rates->send_fee,
            'coin_to_usd' => $btc_rates->btc_to_usd,
            'usd_to_ngn' => $btc_rates->usd_to_ngn,
            'sell_charge' => Setting::where('name', 'bitcoin_sell_charge')->first()->value
        ];

        $usdt->wallet = CryptoHelperController::balance(7);
        $usdt->rates = [
            'send_charge' => Setting::where('name', 'usdt_send_charge')->first()->value,
            'coin_to_usd' => LiveRateController::usdtRate(),
            'usd_to_ngn' => LiveRateController::usdNgn(),
            'sell_charge' => Setting::where('name', 'bitcoin_sell_charge')->first()->value
        ];

        return response()->json([
            'success' => true,
            'bitcoin' => $bitcoin,
            'usdt' => $usdt
        ]);

    }
}
