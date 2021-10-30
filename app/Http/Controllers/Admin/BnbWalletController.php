<?php

namespace App\Http\Controllers\Admin;

use App\FeeWallet;
use App\HdWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LiveRateController;
use GuzzleHttp\Client;

class BnbWalletController extends Controller
{
    public function index()
    {
        $client = new Client();

        $hd_wallet = HdWallet::where('currency_id', 4)->first();
        $url_hd = env('TATUM_URL') . '/ledger/account/' . $hd_wallet->account_id;
        $res_hd = $client->request('GET', $url_hd, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_hd = json_decode($res_hd->getBody());
        $hd_wallet->balance = $res_hd->balance->availableBalance;



        $charges_wallet = FeeWallet::where(['crypto_currency_id' => 4, 'name' => 'bnb_charge'])->first();
        $url_charges = env('TATUM_URL') . '/ledger/account/' . $charges_wallet->account_id;
        $res_charges = $client->request('GET', $url_charges, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_charges = json_decode($res_charges->getBody());
        $charges_wallet->balance = $res_charges->balance->availableBalance;



        $service_wallet = FeeWallet::where(['crypto_currency_id' => 4, 'name' => 'bnb_service'])->first();
        $url_service = env('TATUM_URL') . '/ledger/account/' . $service_wallet->account_id;
        $res_service = $client->request('GET', $url_service, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_service = json_decode($res_service->getBody());
        $service_wallet->balance = $res_service->balance->availableBalance;

        $url = env('TATUM_URL') . '/ledger/transaction/account?pageSize=50';
        $get_txns = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            "json" => ["id" => $hd_wallet->account_id]
        ]);

        $transactions = json_decode($get_txns->getBody());
        foreach ($transactions as $t) {
            $x = \Str::limit($t->created, 10, '');
            $time = \Carbon\Carbon::parse((int)$x);
            $t->created = $time->setTimezone('Africa/Lagos');
        }

        return view('admin.binance.index', compact('service_wallet', 'charges_wallet',  'hd_wallet', 'transactions'));
    }

    public function settings()
    {
        $sell_rate = LiveRateController::usdRate();
        return view('admin.binance.settings', compact('sell_rate'));
    }
}
