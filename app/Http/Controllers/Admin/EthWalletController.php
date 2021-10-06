<?php

namespace App\Http\Controllers\Admin;

use App\CryptoRate;
use App\FeeWallet;
use App\HdWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rate;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EthWalletController extends Controller
{
    public function index()
    {
        $client = new Client();

        $hd_wallet = HdWallet::where('currency_id', 2)->first();
        $url_hd = env('TATUM_URL') . '/ledger/account/' . $hd_wallet->account_id;
        $res_hd = $client->request('GET', $url_hd, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_hd = json_decode($res_hd->getBody());
        $hd_wallet->balance = $res_hd->balance->availableBalance;



        $charges_wallet = FeeWallet::where(['crypto_currency_id' => 2, 'name' => 'eth_charge'])->first();
        $url_charges = env('TATUM_URL') . '/ledger/account/' . $charges_wallet->account_id;
        $res_charges = $client->request('GET', $url_charges, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_charges = json_decode($res_charges->getBody());
        $charges_wallet->balance = $res_charges->balance->availableBalance;



        $service_wallet = FeeWallet::where(['crypto_currency_id' => 2, 'name' => 'eth_service'])->first();
        $url_service = env('TATUM_URL') . '/ledger/account/' . $service_wallet->account_id;
        $res_service = $client->request('GET', $url_service, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_service = json_decode($res_service->getBody());
        $service_wallet->balance = $res_service->balance->availableBalance;



        // $fees_wallet = FeeWallet::where(['crypto_currency_id' => 2, 'name' => 'eth_fees'])->first();
        // $fees_url = env('TATUM_URL') . '/ethereum/account/balance/' . $fees_wallet->address;
        // $res_fees = $client->request('GET', $fees_url, [
        //     'headers' => ['x-api-key' => env('TATUM_KEY')]
        // ]);
        // $res_fees = json_decode($res_fees->getBody());
        // $fees_wallet->balance = $res_fees->balance;




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



        return view('admin.ethereum.index', compact('service_wallet', 'charges_wallet',  'hd_wallet', 'transactions'));
    }

    public function settings()
    {
        $sell_rate = CryptoRate::where(['crypto_currency_id' => 2, 'type' => 'sell'])->first()->rate;
        return view('admin.ethereum.settings', compact('sell_rate'));
    }

    public function updateRate(Request $request)
    {
        $sell_rate = CryptoRate::where(['crypto_currency_id' => 2, 'type' => 'sell'])->first();

        $sell_rate->rate = $request->sell_rate;
        $sell_rate->save();

        return back()->with(['success' => 'Rate updated']);
    }


    public function send(Request $request)
    {

        $data = $request->validate([
            'amount' => 'required|min:0',
            'wallet' => 'required',
            'address' => 'required|string',
            'pin' => 'required',
            'password' => 'required'
        ]);


        //Check pin
        if (!Hash::check($request->pin, Auth::user()->pin)) {
            return back()->with(['error' => 'Incorrect wallet pin']);
        }

        if (!Hash::check($data['password'], Auth::user()->password)) {
            return back()->with(['error' => 'Incorrect password']);
        }

        if ($request->wallet == 'hd') {
            $wallet = HdWallet::where('currency_id', 2)->first();
        }else {
            $wallet = FeeWallet::find($request->wallet);
        }

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/account/' . $wallet->account_id;

        $res = $client->request('GET', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);

        $accounts = json_decode($res->getBody());


        $wallet->balance = $accounts->balance->availableBalance;

        $hd_wallet = HdWallet::where(['currency_id' => 2])->first();

        $url = env('TATUM_URL') . '/ethereum/gas';
        $get_fees = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' =>  [
                "from" => Auth::user()->ethWallet->address,
                "to" => $request->address,
                "amount" => number_format((float)$request->amount, 8),
            ]
        ]);

        $res = json_decode($get_fees->getBody());

        $fees = ($res->gasPrice * 100000) / 1e18;

        if ($request->amount + $fees > $wallet->balance) {
           // return back()->with(['error' => 'Insufficient balance']);
        }


        try {
            $url = env('TATUM_URL') . '/offchain/ethereum/transfer';
            $send_eth = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
                'json' =>  [
                    "senderAccountId" => $wallet->account_id,
                    "address" => $request->address,
                    "amount" => number_format((float) $request->amount, 8),
                    "compliant" => false,
                    "signatureId" => $hd_wallet->signature_id,
                    "index" => $wallet->pin,
                    "senderNote" => "Send ETH"
                ]
            ]);

            $res = json_decode($send_eth->getBody());


            if (Arr::exists($res, 'signatureId')) {
                return back()->with(['success' => 'Ethereum sent successfully']);

            } else {
                return back()->with(['error' => 'AN error occured, try again']);
            }
        } catch (\Exception $e) {
            //report($e);
            \Log::info($e->getResponse()->getBody());
            return back()->with(['error' => 'AN error occured, try again']);
        }
    }
}
