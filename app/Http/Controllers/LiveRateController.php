<?php

namespace App\Http\Controllers;

use App\CryptoRate;
use App\Setting;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class LiveRateController extends Controller
{
    public static function ethRate()
    {
        $client = new Client();
        $url = env('TATUM_URL') . '/tatum/rate/ETH?basePair=USD';
        $res = $client->request('GET', $url, ['headers' => ['x-api-key' => env('TATUM_KEY')]]);
        $res = json_decode($res->getBody());
        $eth_rate = $res->value;

        $trading_per = Setting::where('name', 'trading_eth_per')->first()->value ?? 0;
        $tp = ($trading_per / 100) * $eth_rate;
        $eth_rate -= $tp;

        return $eth_rate;
    }

    public static function bnbRate()
    {
        $client = new Client();
        $url = env('TATUM_URL') . '/tatum/rate/BNB?basePair=USD';
        $res = $client->request('GET', $url, ['headers' => ['x-api-key' => env('TATUM_KEY')]]);
        $res = json_decode($res->getBody());
        $bnb_rate = $res->value;

        $trading_per = Setting::where('name', 'trading_bnb_per')->first()->value ?? 0;
        $tp = ($trading_per / 100) * $bnb_rate;
        $bnb_rate -= $tp;

        return $bnb_rate;
    }


    public static function usdRate(Type $var = null)
    {
        $rates = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;

        return $rates;
    }
}
