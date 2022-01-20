<?php

namespace App\Http\Controllers;

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

    public static function tronRate()
    {
        $client = new Client();
        $url = env('TATUM_URL') . '/tatum/rate/TRON?basePair=USD';
        $res = $client->request('GET', $url, ['headers' => ['x-api-key' => env('TATUM_KEY')]]);
        $res = json_decode($res->getBody());
        $eth_rate = $res->value;

        $trading_per = Setting::where('name', 'trading_tron_per')->first()->value ?? 0;
        $tp = ($trading_per / 100) * $eth_rate;
        $eth_rate -= $tp;

        return $eth_rate;
    }


    public static function usdtRate()
    {
        $client = new Client();
        $url = env('TATUM_URL') . '/tatum/rate/USDT_TRON?basePair=USD';
        $res = $client->request('GET', $url, ['headers' => ['x-api-key' => env('TATUM_KEY')]]);
        $res = json_decode($res->getBody());
        $rate = $res->value;

        $trading_per = Setting::where('name', 'trading_usdt_per')->first()->value ?? 0;
        $tp = ($trading_per / 100) * $rate;
        $rate -= $tp;

        return $rate;
    }
}
