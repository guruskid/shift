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
        $usdRate = CryptoRate::where(['type' => 'sell', 'crypto_currency_id' => 2])->first()->rate;

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
        
        $netWalletBalanceNGN = $nairaWalletNGN + $BTC_NGN + $USDT_NGN;
        $netWalletBalanceUSD = $nairaWalletUSD + $BTC_USD + $USDT_USD;

        $walletData = array();
        $walletData['netWalletBalanceNGN'] = $netWalletBalanceNGN;
        $walletData['netWalletBalanceUSD'] = $netWalletBalanceUSD;
        $walletData['BTC_balance'] = $BTC_VOLUME;

        $featuredCoinsBTC = array();
        $featuredCoinsBTC['name'] = 'BTC';
        $featuredCoinsBTC['image'] = env('APP_URL') . '/storage/crypto/bitcoin.png';
        $featuredCoinsBTC['balance'] = $BTC_VOLUME;
        $featuredCoinsBTC['USD_value'] = $BTC_USD;
        $featuredCoinsBTC['coin_to_usd'] = LiveRateController::btcRate();
        
        $featuredCoinsUSDT = array();
        $featuredCoinsUSDT['name'] = 'USDT';
        $featuredCoinsUSDT['image'] = env('APP_URL') . '/storage/crypto/tether.png';
        $featuredCoinsUSDT['balance'] = (string)$USDT_VOLUME;
        $featuredCoinsUSDT['USD_value'] = $USDT_USD;
        $featuredCoinsUSDT['coin_to_usd'] = LiveRateController::usdtRate();

        $featuredCoins = collect([$featuredCoinsBTC, $featuredCoinsUSDT]);
        return response()->json([
            'success' => true,
            'netWalletBalance' => $walletData,
            'featuredCoins' => $featuredCoins,
        ],200);
    }
}
