<?php

namespace App\Http\Controllers\Admin;

use App\FeeWallet;
use App\HdWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CryptoHelperController;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TronController extends Controller
{
    public function index()
    {
        $client = new Client();

        $hd_wallet = HdWallet::where('currency_id', 5)->first();
        $url_hd = env('TATUM_URL') . '/ledger/account/' . $hd_wallet->account_id;
        $res_hd = $client->request('GET', $url_hd, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_hd = json_decode($res_hd->getBody());
        $hd_wallet->balance = $res_hd->balance->availableBalance;



        $charges_wallet = FeeWallet::where(['crypto_currency_id' => 5, 'name' => 'tron_charge'])->first();
        $url_charges = env('TATUM_URL') . '/ledger/account/' . $charges_wallet->account_id;
        $res_charges = $client->request('GET', $url_charges, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_charges = json_decode($res_charges->getBody());
        $charges_wallet->balance = $res_charges->balance->availableBalance;



        $service_wallet = FeeWallet::where(['crypto_currency_id' => 5, 'name' => 'tron_service'])->first();
        $url_service = env('TATUM_URL') . '/ledger/account/' . $service_wallet->account_id;
        $res_service = $client->request('GET', $url_service, [
            'headers' => ['x-api-key' => env('TATUM_KEY')]
        ]);
        $res_service = json_decode($res_service->getBody());
        $service_wallet->balance = $res_service->balance->availableBalance;

        $blockchain_fee_wallet = FeeWallet::where('name', 'tron_fees')->first();
        $blockchain_fee_wallet->balance = CryptoHelperController::feeWalletBalance(5);





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



        return view('admin.tron.index', compact('service_wallet', 'blockchain_fee_wallet', 'charges_wallet',  'hd_wallet', 'transactions'));
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
            $wallet = HdWallet::where('currency_id', 5)->first();
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

        $hd_wallet = HdWallet::where(['currency_id' => 5])->first();
        $fees_wallet = FeeWallet::where(['crypto_currency_id' => 5, 'name' => 'tron_fees'])->first();

        if ($request->amount  > $wallet->balance) {
           return back()->with(['error' => 'Insufficient balance']);
        }

        //Store Withdrawal
        $url = env('TATUM_URL') . '/offchain/withdrawal';
        $store = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY')],
            'json' =>  [
                "senderAccountId" => $wallet->account_id,
                "address" => $request->address,
                "amount" => number_format((float) $request->amount, 8),
                "compliant" => false,
                "fee" => "0",
                "paymentId" => uniqid(),
                "senderNote" => "Sending Tron"
            ]
        ]);

        $store_res = json_decode($store->getBody());
        if ($store->getStatusCode() != 200) {
            return back()->with(['error' => 'An error occured while withdrawing']);
        }

        try {
            $url = env('TATUM_URL') . '/blockchain/sc/custodial/transfer';
            $send = $client->request('POST', $url, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
                'json' =>  [
                    "chain" => "TRON",
                    "custodialAddress" => $wallet->address,
                    "contractType" => 3,
                    "recipient" => $request->address,
                    "amount" => number_format($request->amount, 8),
                    "signatureId" => $hd_wallet->private_key,
                    "from" => $fees_wallet->address,
                    "feeLimit" => 5,
                ]
            ]);

            $send_res = json_decode($send->getBody());

            if (Arr::exists($send_res, 'signatureId')) {
                return back()->with(['success' => 'Tron sent successfully']);
            } else {
                //Cancel TXN
                $client->request('delete', env('TATUM_URL') . '/offchain/withdrawal/' . $store_res->id, [
                    'headers' => ['x-api-key' => env('TATUM_KEY')],
                ]);
                return back()->with(['error' => 'A blockchain error occured, please try again']);
            }
        } catch (\Exception $e) {
            report($e);
            $client->request('delete', env('TATUM_URL') . '/offchain/withdrawal/' . $store_res->id, [
                'headers' => ['x-api-key' => env('TATUM_KEY')],
            ]);

            return back()->with(['error' => 'An error occured, please try again']);
        }
    }
}
