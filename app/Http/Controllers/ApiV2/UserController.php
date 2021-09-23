<?php

namespace App\Http\Controllers\ApiV2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function dashboard()
    {
        $client = new Client();
        $url = env('TATUM_URL') . '/tatum/rate/BTC?basePair=USD';
        $res = $client->request('GET', $url, ['headers' => ['x-api-key' => env('TATUM_KEY')]]);
        $res = json_decode($res->getBody());
        $btc_real_time = $res->value;

        $url = env('TATUM_URL') . '/tatum/rate/USD?basePair=NGN';
        $res = $client->request('GET', $url, ['headers' => ['x-api-key' => env('TATUM_KEY')]]);
        $res = json_decode($res->getBody());
        $naira_usd_real_time = $res->value;

        $url = env('TATUM_URL') . '/ledger/account/customer/' . Auth::user()->btcWallet->account_id . '?pageSize=50';
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $accounts = json_decode($res->getBody());

        if (empty($accounts)) {
            return response()->json([
                'success' => false,
                'message' => 'btc wallet not found'
            ]);
        }

        $btc_balance = $accounts[0]->balance->availableBalance;

        $btc_wallet = Auth::user()->btcWallet;
        $btc_wallet->balance = $btc_balance;
        $btc_wallet->usd = $btc_wallet->balance * $btc_real_time;

        $naira_balance = $btc_wallet->usd * $naira_usd_real_time;

        $res = file_get_contents("https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,litecoin,ripple,tether&vs_currencies=ngn&include_24hr_change=true");

        $data = json_decode($res,true);

        $currencies = [
            [
                'name' => 'Tether',
                'short_name' => 'USDT',
                'rate' => $data['tether']['ngn'],
                '24h_change' => $data['tether']['ngn_24h_change'],
                'img' => url('/crypto/tether.png')
            ],
            [
                'name' => 'Bitcoin',
                'short_name' => 'BTC',
                'rate' => $data['bitcoin']['ngn'],
                '24h_change' => $data['bitcoin']['ngn_24h_change'],
                'img' => url('/crypto/bitcoin.png')
            ],
            [
                'name' => 'Ethereum',
                'short_name' => 'ETH',
                'rate' => $data['ethereum']['ngn'],
                '24h_change' => $data['ethereum']['ngn_24h_change'],
                'img' => url('/crypto/ethereum.png')
            ],
            [
                'name' => 'Litecoin',
                'short_name' => 'LTC',
                'rate' => $data['litecoin']['ngn'],
                '24h_change' => $data['litecoin']['ngn_24h_change'],
                'img' => url('/crypto/litecoin.png')
            ],
            [
                'name' => 'Ripple',
                'short_name' => 'XRP',
                'rate' => $data['ripple']['ngn'],
                '24h_change' => $data['ripple']['ngn_24h_change'],
                'img' => url('/crypto/xrp.png')
            ]
        ];

        return response()->json([
            'success' => true,
            'btc_balance' => $btc_balance,
            'btc_balance_in_naira' => $naira_balance,
            'btc_balnace_in_usd' => $btc_wallet->usd,
            'btc_rate' => $btc_real_time,
            'featured_coins' => $currencies
        ]);
    }
}
