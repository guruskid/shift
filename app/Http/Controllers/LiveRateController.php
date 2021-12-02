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

    public static function btcRate()
    {
        $client = new Client();
        $url = "https://api.coinbase.com/v2/prices/spot?currency=USD";
        $res = $client->request('GET', $url);
        $res = json_decode($res->getBody());
        $btc_rate = $res->data->amount;

        $trading_per = Setting::where('name', 'trading_btc_per')->first()->value ?? 0;
        $tp = ($trading_per / 100) * $btc_rate;
        $btc_rate -= $tp;

        return $btc_rate;

    }
}
