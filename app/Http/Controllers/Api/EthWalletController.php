<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LiveRateController;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class EthWalletController extends Controller
{
    public function wallet(Request $r)
    {

        if (!Auth::user()->ethWallet) {
            return response()->json([
                'success' => false,
                'msg' => 'Please an Ethereum wallet to continue'
            ]);
        }

        $eth_rate = LiveRateController::ethRate();
        $eth_wallet = Auth::user()->ethWallet;

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . $eth_wallet->account_id;
        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);

        $accounts = json_decode($res->getBody());

        $eth_wallet->balance = $accounts->balance->availableBalance;
        $eth_wallet->usd = $eth_wallet->balance  * $eth_rate;

        $url = env('TATUM_URL') . '/ledger/transaction/account?pageSize=50';
        $get_txns = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            "json" => ["id" => Auth::user()->ethWallet->account_id]
        ]);

        $transactions = json_decode($get_txns->getBody());
        foreach ($transactions as $t) {
            $x = \Str::limit($t->created, 10, '');
            $time = \Carbon\Carbon::parse((int)$x);
            $t->created = $time->setTimezone('Africa/Lagos');

            if (!isset($t->senderNote)) {
                $t->senderNote = 'Sending ETH';
            }
        }
        return response()->json([
            'success' => true,
            'wallet' => $eth_wallet,
            'transactions' => $transactions,
            'rate' => $eth_rate
        ]);

    }
}
