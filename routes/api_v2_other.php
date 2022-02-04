<?php

use Illuminate\Http\Request;

Route::GET('/verification-limit', 'Admin\VerificationLimitController@get');
Route::get('/banks', 'Api\AuthController@bankList' );

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
    Route::post('/update-wallet-pin', 'Api\NairaWalletController@updateWalletPin');
    Route::post('/bank-details', 'Api\AuthController@addBankDetails');
    Route::GET('/naira-wallet', 'Api\NairaWalletController@index');
    // Airtime
    Route::get('/airtime', 'Api\BillsPaymentController@airtime');
    Route::post('/buy-airtime', 'Api\BillsPaymentController@buyAirtime');

    // Data
    Route::get('/data', 'Api\BillsPaymentController@data');
    Route::post('/buy-data', 'Api\BillsPaymentController@buyData');

    // Data
    Route::get('/cable', 'Api\BillsPaymentController@cable');
    Route::post('/recharge-cable', 'Api\BillsPaymentController@rechargeCable');
    Route::post('/get-merchant/{serviveId}/{billercode}', 'BillsPaymentController@merchantVerify');

    Route::get('/get-variations/{serviveId}', 'BillsPaymentController@getVariations');

    //Pay electricity
        // Route::post('/get-elect-user', 'BillsPaymentController@getElectUser');
        // Route::post('/electricity', 'BillsPaymentController@payElectricity');

        Route::get('/get-elect-boards/{category?}', 'BillsPaymentController@getProducts');
        Route::get('/get-variations/{serviveId}', 'BillsPaymentController@getVariations');
        Route::post('/electricity', 'Api\BillsPaymentController@payElectricityVtpass')->name('user.pay-electricity');

        Route::get('/power/{category?}', 'Api\BillsPaymentController@power');
        Route::get('/get-variations/{serviveId}', 'BillsPaymentController@getVariations');
        Route::post('/electricity', 'Api\BillsPaymentController@payElectricityVtpass')->name('user.pay-electricity');

            //Pay Cable
        Route::post('/get-dec-user', 'BillsPaymentController@getUser');
        Route::post('/get-tv-packages', 'BillsPaymentController@getPackages');

});
