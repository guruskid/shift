<?php

namespace App\Http\Controllers\ApiV2;

use App\CryptoRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CryptoHelperController;
use App\Http\Controllers\LiveRateController;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {

        $user = Auth::user();
        // $usdRate = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;
        $usdRate = LiveRateController::usdNgn();



        //  dd($usdRate);

        $nairaWalletNGN = 0;
        $nairaWalletUSD = 0;

        if($user->nairaWallet)
        {
           $nairaWalletNGN = $user->nairaWallet->amount;
           $nairaWalletUSD = $nairaWalletNGN/$usdRate;
        }

        $BTC_NGN = 0;
        $BTC_USD = 0;
        $BTC_VOLUME = 0;

        if($user->btcWallet)
        {
            $btcWallet = CryptoHelperController::balance(1);
            $BTC_NGN = $btcWallet->ngn;
            $BTC_USD = $btcWallet->usd;
            $BTC_VOLUME = $btcWallet->balance;
        }

        $USDT_NGN = 0;
        $USDT_USD = 0;
        $USDT_VOLUME = 0;

        if($user->usdtWallet)
        {
            $usdtWallet = CryptoHelperController::balance(7);
            $USDT_NGN = $usdtWallet->ngn;
            $USDT_USD = $usdtWallet->usd;
            $USDT_VOLUME = $usdtWallet->balance;
        }

        // -----------  Converting to user balance --------- //

        // add total user balance in dollars
        $total_user_balance_in_usd =  $nairaWalletUSD + $USDT_USD + $BTC_USD;

        // converts  $total_user_balance_in_usd  to bitcoin
        $btc_real_time = LiveRateController::btcRate();

        $total_user_balance_in_btc =   $total_user_balance_in_usd /  $btc_real_time;

















        $netWalletBalanceNGN = $nairaWalletNGN + $BTC_NGN + $USDT_NGN;
        $netWalletBalanceUSD = $nairaWalletUSD + $BTC_USD + $USDT_USD;

        $walletData = array();
        $walletData['netWalletBalanceNGN'] = $netWalletBalanceNGN;
        $walletData['netWalletBalanceUSD'] = $netWalletBalanceUSD;
        $walletData['BTC_balance'] =  $total_user_balance_in_btc;  //$BTC_VOLUME;

        $featuredCoinsBTC = array();
        $featuredCoinsBTC['name'] = 'BTC';
        $featuredCoinsBTC['image'] = env('APP_URL') . '/crypto/bitcoin.png';
        $featuredCoinsBTC['balance'] = $BTC_VOLUME;
        $featuredCoinsBTC['USD_value'] = $BTC_USD;
        $featuredCoinsBTC['NGN_value'] = $BTC_NGN;
        $featuredCoinsBTC['coin_to_usd'] = LiveRateController::btcRate();
        $featuredCoinsBTC['coin_to_ng'] = LiveRateController::btcRate() * $usdRate;

        $featuredCoinsUSDT = array();
        $featuredCoinsUSDT['name'] = 'USDT';
        $featuredCoinsUSDT['image'] = env('APP_URL') . '/crypto/tether.png';
        $featuredCoinsUSDT['balance'] = (string)$USDT_VOLUME;
        $featuredCoinsUSDT['USD_value'] = $USDT_USD;
        $featuredCoinsUSDT['NGN_value'] =  $USDT_NGN;
        $featuredCoinsUSDT['coin_to_usd'] = LiveRateController::usdtRate();
        $featuredCoinsUSDT['coin_to_ng'] = $usdRate;

        $featuredCoins = collect([$featuredCoinsBTC, $featuredCoinsUSDT]);
        return response()->json([
            'success' => true,
            'netWalletBalance' => $walletData,
            'featuredCoins' => $featuredCoins,

        ],200);
    }
}
