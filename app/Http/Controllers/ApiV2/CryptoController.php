<?php

namespace App\Http\Controllers\ApiV2;

use App\CryptoCurrency;
use App\Http\Controllers\Api\BtcWalletController;
use App\Http\Controllers\BtcWalletController as ControllersBtcWalletController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CryptoHelperController;
use App\Http\Controllers\LiveRateController;
use App\Http\Controllers\UsdtController;
use App\Setting;
use App\Transaction;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CryptoController extends Controller
{
    public function index()
    {

        $bitcoin = CryptoCurrency::find(1);
        $usdt = CryptoCurrency::find(7);
        $data['usdt_rate'] = LiveRateController::usdtRate();
        $data["btc_rate"] = LiveRateController::btcRate();
        // $data['usdt_rate_naira'] = LiveRateController::usdtNgn();
        // $data['btc_rate_naira'] = LiveRateController::btcNgn();



        $bitcoin->wallet = CryptoHelperController::balance(1);
        $bitcoin->network = "BRP-20";
        $bitcoin->image = env('APP_URL') . '/storage/assets/BITCOINS.png';
        $btc_rates = BtcWalletController::fees()->getData();
       $bitcoin->rates = [
            'send_charge' => $btc_rates->send_fee,
            'coin_to_usd' => $btc_rates->btc_to_usd,
            'coin_to_ngn' => LiveRateController::btcNgn(),
            'usd_to_ngn' => LiveRateController::usdNgn(), // Similar to sell rate
            'buy_rate' => LiveRateController::usdNgn(true, 'buy'),
            'sell_rate' => LiveRateController::usdNgn(),
            'sell_charge' => Setting::where('name', 'bitcoin_sell_charge')->first()->value
        ];

        $usdt->wallet = CryptoHelperController::balance(7);
        $usdt->network = "TRC-20";
        $usdt->image = env('APP_URL') . '/storage/assets/tether.png';
        $usdt->rates = [
            'send_charge' => Setting::where('name', 'usdt_send_charge')->first()->value,
            'coin_to_usd' => LiveRateController::usdtRate(),
            'coin_to_ngn' => LiveRateController::usdtNgn(),
            'usd_to_ngn' => LiveRateController::usdNgn(),
            'buy_rate' => LiveRateController::usdNgn(true, 'buy'),
            'sell_rate' => LiveRateController::usdNgn(),
            'sell_charge' => Setting::where('name', 'bitcoin_sell_charge')->first()->value
        ];

        $wallets = [$bitcoin, $usdt];
        $total_balances = [
            'ngn' => 0,
            'usd' => 0,
            'btc_balance'=> $bitcoin->wallet->balance
        ];

        foreach ($wallets as $w ) {
            if ($w->wallet) {
                $total_balances['ngn'] += $w->wallet->ngn;
                $total_balances['usd'] += $w->wallet->usd;
            }
        }

        return response()->json([
            'success' => true,
            'rates' => $data,
            'currencies' => $wallets,
            'total_balance' => $total_balances,
            'UserPin' => isset(Auth::user()->pin)
        ]);
    }

    public function create(Request $request)
    {
        if (!Hash::check($request->pin, Auth::user()->pin)) {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid Pin'
            ],401);
        }

        if ($request->currency_id == 1) {
            return BtcWalletController::create($request);
        } else if ($request->currency_id == 7) {
            return UsdtController::create($request);
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid currency selected'
            ]);
        }
    }

    public function sell(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|min:0',
            'currency_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        if ($request->currency_id == 1) {
            $request->merge([
                'quantity' => $request->amount
            ]);
            return ControllersBtcWalletController::sell($request);
        } else if ($request->currency_id == 7) {
            return UsdtController::sell($request);
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid currency selected'
            ]);
        }
    }


    public function buy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|min:0',
            'currency_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        if ($request->currency_id == 1) {
            $request->merge([
                'quantity' => $request->amount
            ]);
            return ControllersBtcWalletController::buy($request);
        } else if ($request->currency_id == 7) {
            return UsdtController::buy($request);
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid currency selected'
            ]);
        }
    }

    public function send(Request $request)
    {
        $request->validate([
            'currency_id' => 'required',
            'amount' => 'required|min:0',
            'address' => 'required|string',
            'pin' => 'required',
            'fees' => 'required'
        ]);

        if ($request->currency_id == 1) {
            return ControllersBtcWalletController::send($request);
        } else if ($request->currency_id == 7) {
            return UsdtController::send($request);
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid currency selected'
            ]);
        }
    }

    public function transactions($currency_id)
    {
        if ($currency_id == 1) {
            $account_id = Auth::user()->btcWallet->account_id;
        } else if ($currency_id == 7) {
            $account_id = Auth::user()->usdtWallet->account_id;
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid currency selected'
            ]);
        }

        $client = new Client();
        $url = env('TATUM_URL') . '/ledger/transaction/account?pageSize=50';
        $get_txns = $client->request('POST', $url, [
            'headers' => ['x-api-key' => env('TATUM_KEY_USDT')],
            "json" => ["id" => $account_id]
        ]);

        $transactions = json_decode($get_txns->getBody());
        foreach ($transactions as $t) {
            $x = \Str::limit($t->created, 10, '');
            $time = \Carbon\Carbon::parse((int)$x);
            $t->created = $time->setTimezone('Africa/Lagos');

            if (!isset($t->senderNote)) {
                $t->senderNote = 'Sending Tron';
            }
        }

        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);
    }

    public function cryptoTransactionByType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 401);
        }

        $type = $request->type;

        if(!in_array($type,['buy','sell'])):
            return response()->json([
                'success' => false,
                'message' => $type." not a valid type"
            ], 401);
        endif;

        $transactions = Transaction::whereHas('asset', function ($query) {
            $query->where('is_crypto', 1);
        })->where('type', $type)
        ->where('user_id',Auth::user()->id)->orderBy('created_at', 'DESC')->get();

        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);
    }
}
