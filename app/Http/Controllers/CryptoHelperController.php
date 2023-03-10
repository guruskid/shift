<?php

namespace App\Http\Controllers;

use App\CryptoRate;
use App\FeeWallet;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        } elseif ($currency_id == 7) {
            $address = FeeWallet::where('name', 'usdt_fees')->first()->address;

            $client = new Client();
            $url = env('TATUM_URL') . '/tron/account/' . $address;
            $res = $client->request('GET', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
            ]);

            $res = json_decode($res->getBody());
            $balance = ($res->balance) / 1000000;

            return $balance;
        }
    }


    public static function balance($currency_id) //User Balance
    {
        $user = Auth::user();

        $wallet = '';
        $usd = 0;
        $ngn = 0;
        $key = env('TATUM_KEY');
        switch ($currency_id) {
            case 0:
                $wallet = $user->nairaWallet;
                return $wallet;
                break;
            case 1:
                $wallet = $user->btcWallet;
                $usd = LiveRateController::btcRate();
                $ngn = LiveRateController::usdNgn();
                break;
            case 5:
                $wallet = $user->tronWallet;
                $usd = LiveRateController::tronRate();
                $ngn = LiveRateController::usdNgn();
                break;
            case 7:
                $wallet = $user->usdtWallet;
                $key = env('TATUM_KEY_USDT');
                $usd = LiveRateController::usdtRate();
                $ngn = LiveRateController::usdNgn();
                break;

            default:
                # code...
                break;
        }


        if ($wallet) {
            $client = new Client();
            $url = env('TATUM_URL') . '/ledger/account/' . $wallet->account_id;
            $res = $client->request('GET', $url, [
                'headers' => ['x-api-key' => $key]
            ]);

            $accounts = json_decode($res->getBody());

            $wallet->balance = $accounts->balance->availableBalance;
            $wallet->usd = $wallet->balance * (float)$usd;
            $wallet->ngn = $wallet->usd * $ngn;
            $wallet->usd = (float)$wallet->usd;
        }

        return $wallet;
    }

    public static function accountBalance($account_id)
    {
        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . $account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res = json_decode($res->getBody());
        return $res->balance->availableBalance;
    }
}
