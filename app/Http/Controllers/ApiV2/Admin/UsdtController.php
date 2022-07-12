<?php

namespace App\Http\Controllers\ApiV2\Admin;

use App\AdminAddresses;
use App\Contract;
use App\CryptoCurrency;
use App\CryptoRate;
use App\FeeWallet;
use App\HdWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CryptoHelperController;
use App\Http\Controllers\LiveRateController;
use App\Transaction;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;

class UsdtController extends Controller
{
    //
    public function index()
    {

        $client = new Client();

        $hd_wallet = HdWallet::where('currency_id', 7)->first();
        $url_hd = env('TATUM_URL') . '/ledger/account/' . $hd_wallet->account_id;
        $res_hd = $client->request('GET', $url_hd, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
        ]);
        $res_hd = json_decode($res_hd->getBody());
        $hd_wallet->balance = $res_hd->balance->availableBalance;
        // dd($res_hd);



        $charges_wallet = FeeWallet::where(['crypto_currency_id' => 7, 'name' => 'usdt_charge'])->first();
        $url_charges = env('TATUM_URL') . '/ledger/account/' . $charges_wallet->account_id;
        $res_charges = $client->request('GET', $url_charges, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
        ]);
        $res_charges = json_decode($res_charges->getBody());
        $charges_wallet->balance = $res_charges->balance->availableBalance;



        $service_wallet = FeeWallet::where(['crypto_currency_id' => 7, 'name' => 'usdt_service'])->first();
        $url_service = env('TATUM_URL') . '/ledger/account/' . $service_wallet->account_id;
        $res_service = $client->request('GET', $url_service, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
        ]);
        $res_service = json_decode($res_service->getBody());
        $service_wallet->balance = $res_service->balance->availableBalance;

        $blockchain_fee_wallet = FeeWallet::where('name', 'usdt_fees')->first();
        $blockchain_fee_wallet->balance = CryptoHelperController::feeWalletBalance(7);


        $addresses = Contract::where(['currency_id' => 7, 'status' => 'pending', 'type' => 'address'])->count();


        $url = env('TATUM_URL') . '/ledger/transaction/account?pageSize=50';
        $get_txns = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
            "json" => ["id" => $hd_wallet->account_id]
        ]);

        $transactions = json_decode($get_txns->getBody());
        foreach ($transactions as $t) {
            $x = \Str::limit($t->created, 10, '');
            $time = \Carbon\Carbon::parse((int)$x);
            $t->created = $time->setTimezone('Africa/Lagos');
        }
        $address = AdminAddresses::where('crypto_currency_id', 7)->get();
        $crypto_currencies = CryptoCurrency::all();
        return response()->json([
            'service_wallet' => $service_wallet,
            'addresses' => $addresses,
            'blockchain_fee_wallet' => $blockchain_fee_wallet,
            'charges_wallet' => $charges_wallet,
            'crypto_currencies' => $crypto_currencies,
            'address' => $address,
            'hd_wallet' => $hd_wallet,
            'transactions' => $transactions,
        ]);
    }


    public function settings(Request $request)
    {
        $sell_rate = CryptoRate::where(['crypto_currency_id' => 2, 'type' => 'sell'])->first()->rate ?? 0;
        $buy_rate = LiveRateController::usdtBuy();

        if ($request->start and $request->end) {
            $sell_transactions = Transaction::where('card_id', 143)->where('created_at', '>=', $request->start)
                ->where('created_at', '<=', $request->end)->where('status', 'success')->get();

            $sell_btc = $sell_transactions->sum('quantity');
            $sell_usd = $sell_transactions->sum('amount');

            $ngn_sell_average = 0;
            $cumulative = 0;
            foreach ($sell_transactions as $t) {
                $cumulative += ($t->quantity * $t->ngn_rate * $t->card_price);
            }
            $ngn_sell_average = ($cumulative == 0 ? 1 : $cumulative) / ($sell_usd == 0 ? 1 : $sell_usd);

            return response()->json([
                'sell_rate' => $sell_rate,
                'buy_rate' => $buy_rate,
                'ngn_sell_average' => $ngn_sell_average,
            ]);
        }

        return response()->json([
            'sell_rate' => $sell_rate,
            'buy_rate' => $buy_rate,
        ]);
    }

    public function updateRate(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'rate' => 'required',
            'buy_rate' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'error' => $validate->errors()
            ]);
        }
        CryptoRate::updateOrCreate(
            ['crypto_currency_id' => 2, 'type' => 'sell'],
            ['rate' => $request->rate]
        );

        CryptoRate::updateOrCreate(
            ['crypto_currency_id' => 7, 'type' => 'buy'],
            ['rate' => $request->buy_rate]
        );

        return response()->json(['success' => 'Rate updated']);
    }


    public function deployContract(Request $request)
    {
        $fees_wallet = FeeWallet::where('name', 'usdt_fees')->first();
        $fees_wallet->balance = CryptoHelperController::feeWalletBalance(7);
        $key = env('USDT_KEY');
        $fee_limit = 0;

        switch ($request->count) {
            case 2:
                $fee_limit = 60;
                break;
            case 5:
                $fee_limit = 200;
                break;
            case 10:
                $fee_limit = 300;
                break;
            case 100:
                $fee_limit = 3000;
                break;
            case 270:
                $fee_limit = 8000;
                break;
            default:
                $fee_limit = 2000;
                break;
        }

        if ($fees_wallet->balance < $fee_limit) {
            return response()->json([
                'success' => false,
                'error' => 'Insufficient fee wallet balance']);
        }
        $client = new Client();

        $url_contract = env('TATUM_URL') . '/blockchain/sc/custodial/batch';
        try {
            $res_contract = $client->request('POST', $url_contract, [
                'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
                'json' =>  [
                    "chain" => "TRON",
                    "fromPrivateKey" => $key,
                    "batchCount" => (int)$request->count,
                    "owner" => $fees_wallet->address,
                    "feeLimit"  => $fee_limit,
                ]
            ]);
        } catch (\Exception $e) {
            report($e);
            return response([
                'success' => false,
                'error' => 'An error occured while deploying the contract']);
        }

        $res = json_decode($res_contract->getBody());
        Contract::create([
            'hash' => $res->txId,
            'type' => 'transaction',
            'currency_id' => 7
        ]);

        return response()->josn([
            'success' => true,
            'message' => 'Contract deployed successfully']);
    }


    public function contracts()
    {
        $fees_wallet = FeeWallet::where('name', 'usdt_fees')->first();
        $fees_wallet->balance = CryptoHelperController::feeWalletBalance(7);

        $addresses = Contract::where(['currency_id' => 7, 'status' => 'pending', 'type' => 'address'])->count();

        $pending_transactions = Contract::where(['currency_id' => 7, 'status' => 'pending', 'type' => 'transaction'])->get();

        return response()->json([
            'success' => true,
            'fees_wallet' => $fees_wallet,
            'addresses' => $addresses,
            'pending_transactions' => $pending_transactions,
        ]);
    }

    public function activate($id)
    {
        $contract = Contract::find($id);

        $client = new Client();

        try {

            $url_contract = env('TATUM_URL') . '/blockchain/sc/custodial/TRON/' . $contract->hash;
            $res_contract = $client->request('GET', $url_contract, [
                'headers' => ['x-api-key' => env('TATUM_KEY_USDT')]
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'error' => 'An error occured while activating the contract. Please confirm if the transaction has been signed']);
        }

        $res = json_decode($res_contract->getBody());
        $count = 0;
        foreach ($res as $r) {
            // check if address already exists
            if (!Contract::where('hash', $r)->exists()) {
                Contract::create([
                    'hash' => $r,
                    'type' => 'address',
                    'currency_id' => 7
                ]);
                $count++;
            }
        }

        $contract->status = 'completed';
        $contract->save();

        return response()->json([
            'success' => true,
            'message' => $count . ' addresses created successfully']);
    }
}
