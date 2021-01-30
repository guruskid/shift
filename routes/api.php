<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {
    Route::post('/register', 'Api\AuthController@register');
    Route::post('/login', 'Api\AuthController@login');
    Route::get('/banks', 'Api\AuthController@bankList' );
    Route::get('/countries', 'Api\AuthController@countries' );

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/bank-details', 'Api\AuthController@addBankDetails');
        Route::post('/get-bank-name', 'Api\AuthController@getBankName');
        Route::get('/logout', 'Api\AuthController@logout');

        Route::post('/send-otp', 'Api\AuthController@sendOtp');
        Route::post('/verify-phone', 'Api\AuthController@verifyPhone');

        Route::get('/send-bvn-otp', 'Api\AuthController@sendBvnOtp');

        Route::post('/update-password', 'Api\UserController@updatePassword');
        Route::post('/update-email', 'Api\UserController@updateEmail');
        Route::post('/update-dp', 'Api\UserController@updateDp');
        Route::post('/update-wallet-pin', 'Api\NairaWalletController@updateWalletPin');

        Route::GET('/dashboard', 'Api\UserController@dashboard');
        Route::get('/user-details', 'Api\UserController@details');

        Route::GET('/accounts', 'Api\BankAccountController@accounts');

        Route::GET('/notifications', 'Api\NotificationController@index');
        Route::GET('/notification/read/{id}', 'Api\NotificationController@read');
        Route::GET('/notification/settings', 'Api\NotificationController@settings');
        Route::POST('/notification/settings', 'Api\NotificationController@updateSettings');

        Route::get('/assets', 'Api\TradeController@assets');
        Route::get('/asset/{buy_sell}/{card_id}/{card_name}', 'Api\TradeController@assetRates');
        Route::post('/trade-gift-card', 'Api\TradeController@tradeGiftCard');

        Route::GET('/transactions', 'Api\TransactionController@allTransactions');

        Route::GET('/naira-wallet', 'Api\NairaWalletController@index' );
        Route::GET('/naira-transactions', 'Api\NairaWalletController@allTransactions');
        Route::POST('/transfer-cash', 'Api\NairaWalletController@transfer');
        Route::POST('/withdraw-cash', 'Api\NairaWalletController@transfer');

            //Pay electricity
        Route::post('/get-elect-user', 'BillsPaymentController@getElectUser');
        Route::post('/electricity', 'BillsPaymentController@payElectricity');
            //Pay Cable
        Route::post('/get-dec-user', 'BillsPaymentController@getUser');
        Route::post('/get-tv-packages', 'BillsPaymentController@getPackages');

        //BTC Wallet
        Route::group(['prefix' => 'bitcoin-wallet'], function () {
            Route::POST('/create', 'Api\BitcoinWalletController@create');
            Route::GET('/balance', 'Api\BitcoinWalletController@balance');
            Route::GET('/send-charges', 'Api\BitcoinWalletController@sendBtcCharges');
            Route::GET('/transactions', 'Api\BitcoinWalletController@transactions');
            Route::POST('/trade', 'Api\BitcoinWalletController@trade');
            Route::POST('/send', 'Api\BitcoinWalletController@send');
        });


    });
});
