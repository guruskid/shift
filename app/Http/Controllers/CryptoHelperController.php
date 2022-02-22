<?php

namespace App\Http\Controllers;

use App\FeeWallet;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class CryptoHelperController extends Controller
{
    public static function feeWalletBalance($currency_id)
    {
        if ($currency_id == 5) {
            $address = FeeWallet::where('name', 'tron_fees')->first()->address;

            $client = new Client();
            $url = env('TATUM_URL') . '/tron/account/' . $address;
            $res = $client->request('GET', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')]
            ]);

            $res = json_decode($res->getBody());
            $balance = ($res->balance) / 1000000;

            return $balance;
        }
    }
}
