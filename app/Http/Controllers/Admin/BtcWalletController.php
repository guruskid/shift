<?php

namespace App\Http\Controllers\Admin;

use App\HdWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Wallet;
use GuzzleHttp\Client;

class BtcWalletController extends Controller
{
    public function index()
    {
        $hd_wallet = HdWallet::where('currency_id', 1)->first();
        $service_wallet = Wallet::where(['name' => 'service', 'user_id' => 1, 'currency_id' => 1])->first();
        $charges_wallet = Wallet::where(['name' => 'charges', 'user_id' => 1, 'currency_id' => 1])->first();

        $client = new Client();
        $url_hd = env('TATUM_URL') . '/ledger/account/' . $hd_wallet->account_id;
        $res_hd = $client->request('GET', $url_hd, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_hd = json_decode($res_hd->getBody());
        $hd_wallet->balance = $res_hd->balance->availableBalance;

        $url_service = env('TATUM_URL') . '/ledger/account/' . $service_wallet->account_id;
        $res_service = $client->request('GET', $url_service, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_service = json_decode($res_service->getBody());
        $service_wallet->balance = $res_service->balance->availableBalance;

        $url_charges = env('TATUM_URL') . '/ledger/account/' . $charges_wallet->account_id;
        $res_charges = $client->request('GET', $url_charges, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_charges = json_decode($res_charges->getBody());
        $charges_wallet->balance = $res_charges->balance->availableBalance;



        $transactions = [];



        return view('admin.bitcoin_wallet.index', compact('service_wallet', 'charges_wallet', 'hd_wallet', 'transactions'));
    }
}
