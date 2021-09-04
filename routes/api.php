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

Route::get('email/verify/{id}', 'VerificationController@verify')->name('verification.verify'); // Make sure to keep this as your route name


Route::group(['prefix' => 'v1'], function () {

    Route::post('/general-settings/{name}', 'Api\GeneralSettings@getSetting');


    Route::post('/register', 'Api\AuthController@register');
    Route::post('/login', 'Api\AuthController@login');
    Route::get('/banks', 'Api\AuthController@bankList' );
    Route::get('/countries', 'Api\AuthController@countries' );

    Route::get('/check-phone/{phone}', 'Api\UserController@checkPhone');
    Route::GET('/bitcoin-wallet/price', 'Api\BtcWalletController@btcPrice');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('email/resend', 'Api\VerificationController@resend')->name('verification.resend');

        Route::post('/bank-details', 'Api\AuthController@addBankDetails');
        Route::post('/get-bank-name', 'Api\AuthController@getBankName');
        Route::get('/logout', 'Api\AuthController@logout');

        Route::GET('/airtime', 'Api\BillsPaymentController@nairaRate');
        Route::post('/airtime', 'Api\BillsPaymentController@buyAirtime');
        Route::post('/bitcoin-airtime', 'Api\BillsPaymentController@bitcoinAirtime');

        Route::post('/send-otp', 'Api\AuthController@sendOtp');
        Route::post('/resend-otp', 'Api\AuthController@resendOtp');
        Route::post('/verify-phone', 'Api\AuthController@verifyPhone');

        //BVN verification
        Route::get('/send-bvn-otp/{bvn}', 'Api\AuthController@sendBvnOtp');
        Route::post('/verify-bvn', 'Api\AuthController@verifyBvnOtp');

        Route::post('/update-password', 'Api\UserController@updatePassword');
        Route::post('/update-email', 'Api\UserController@updateEmail');
        Route::post('/update-dp', 'Api\UserController@updateDp');
        Route::post('/update-wallet-pin', 'Api\NairaWalletController@updateWalletPin');
        Route::post('/upload-idcard', 'Api\UserController@uploadId');
        Route::post('/upload-address', 'Api\UserController@uploadAddress');

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

            Route::POST('/create', 'Api\BtcWalletController@create');
            Route::GET('/balance', 'Api\BtcWalletController@balance');
            Route::GET('/transactions', 'Api\BtcWalletController@transactions');
            Route::GET('/send-charges', 'Api\BtcWalletController@fees');

            Route::POST('/trade', 'BtcWalletController@sell');
            Route::POST('/sell', 'BtcWalletController@sell'); //Future change in url
            Route::POST('/send', 'BtcWalletController@send');

            
        });


    });
});
