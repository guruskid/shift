<?php

use Illuminate\Http\Request;

Route::GET('/verification-limit', 'Admin\VerificationLimitController@get');
Route::get('/banks', 'Api\AuthController@bankList' );
// Route::get('ref', 'Admin\ReferralSettingsController@referral_bonus');

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

    //Pay Cable
    Route::get('/cable', 'Api\BillsPaymentController@cable');
    Route::post('/recharge-cable', 'Api\BillsPaymentController@rechargeCable');
    Route::post('/get-merchant/{serviveId}/{billercode}', 'BillsPaymentController@merchantVerify');

    //Power
    Route::get('/get-variations/{serviveId}', 'BillsPaymentController@getVariations');
    Route::get('/get-elect-boards/{category?}', 'BillsPaymentController@getProducts');

    Route::post('/get-dec-user', 'BillsPaymentController@getUser');
    Route::post('/get-tv-packages', 'BillsPaymentController@getPackages');

    Route::get('/power/{category?}', 'Api\BillsPaymentController@power');
    Route::get('/get-variations/{serviveId}', 'BillsPaymentController@getVariations');
    Route::post('/electricity', 'Api\BillsPaymentController@payElectricityVtpass')->name('user.pay-electricity');

    Route::get('referral-transactions', 'ApiV2\ReferralController@referralTransactions');
    Route::post('withdraw-referral-bonus', 'ApiV2\ReferralController@withdrawReferralBonus');
    Route::get('my-referrers', 'ApiV2\ReferralController@myReferrers');
    Route::get('get-referrers-link', 'ApiV2\ReferralController@getReferralLink');


    // Notifications 
    Route::GET('/notifications', 'Api\NotificationController@index');
    Route::GET('/notification/read/{id}', 'Api\NotificationController@read');
    Route::GET('/notification/settings', 'Api\NotificationController@settings');
    Route::POST('/notification/settings', 'Api\NotificationController@updateSettings');

});
