<?php

use Illuminate\Http\Request;

Route::group(['middleware' => 'auth:api'], function () {

    //BTC Wallet
    Route::group(['prefix' => 'bitcoin-wallet'], function () {

        Route::POST('/create', 'Api\BtcWalletController@create');
        Route::GET('/balance', 'Api\BtcWalletController@balance');
        Route::GET('/send-charges', 'Api\BtcWalletController@fees');
        Route::get('/send-charges/{address}/{amount}', 'BtcWalletController@fees');
        Route::GET('/transactions', 'Api\BitcoinWalletController@transactions');
        Route::GET('/all-transactions', 'Api\BtcWalletController@transactions');
        Route::POST('/trade', 'BtcWalletController@sell');
        Route::POST('/sell', 'BtcWalletController@sell'); //Future change in url

        Route::POST('/send', 'BtcWalletController@send');

    });

});
