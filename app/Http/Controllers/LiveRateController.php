<?php

namespace App\Http\Controllers;

use App\CryptoRate;
use App\Http\Controllers\Admin\SettingController;
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

    public static function btcRate($type = 'sell')
    {
        $client = new Client();
        $url = "https://api.coinbase.com/v2/prices/spot?currency=USD";
        $res = $client->request('GET', $url);
        $res = json_decode($res->getBody());
        $btc_rate = $res->data->amount;

        $trading_per = Setting::where('name', 'trading_btc_per')->first()->value ?? 0;
        $tp = ($trading_per / 100) * $btc_rate;
        if ($type == 'sell') {
            $btc_rate -= $tp;
        } else {
            $btc_rate += $tp;
        }


        return $btc_rate;
    }


    public static function usdtRate()
    {
        return 1;
    }

    public static function usdtNgn()
    {
        $rate = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;
        $commission = SettingController::get('crypto_commission');

        $rate = $rate - (($commission / 100) * $rate);

        return $rate;
    }


    public static function usdNgn($add_com = true, $type = 'sell')
    {
        $rate = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;

        if ($add_com == true) {
            if ($type == 'sell') {
                $commission = SettingController::get('crypto_commission');
                $rate = $rate - (($commission / 100) * $rate);
            } else {
                $commission = SettingController::get('crypto_buy_commission');
                $rate = $rate + (($commission / 100) * $rate);
            }
        }
        return $rate;
    }

    public static function usdtBuy()
    {
        return CryptoRate::where(['type' => 'buy', 'crypto_currency_id' => 7])->first()->rate ?? 0;
    }
    public static function btcNgn(){
        $btc_dollar = self::btcRate();
        return $btc_dollar *  self::usdNgn();
     }
}
