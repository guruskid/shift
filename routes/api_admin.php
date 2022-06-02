<?php

use App\ReferralSettings;
use Illuminate\Http\Request;

Route::group(['middleware' => ['auth:api', 'verified', 'super']], function () {

    //UTILITIES TRANSACTIONS
    Route::group(['prefix' => 'utility-transaction'], function () {
        Route::GET('/airtime',  'UtilityController@airtime');
        Route::POST('/utilities-by-date-search',  'UtilityController@utilitiesSearch');
        Route::GET('/data',  'UtilityController@data');
        Route::GET('/power',  'UtilityController@power');
        Route::GET('/cable',  'UtilityController@cable');
    });

    //CRYPTO TRANSACTIONS
    Route::group(['prefix' => 'transaction'], function () {
        Route::GET('/btc',  'TransactionController@btc');
        Route::GET('/p2p',  'TransactionController@p2p');

    });

    Route::POST('/admin/add-admin',  'AdminController@addAdmin');
    Route::POST('/admin/action',  'AdminController@action');

    Route::get('/customer', 'AdminController@customerHappiness');
    Route::get("/all-accountant", 'AdminController@accountant');


    // Announcement
    Route::group(['prefix' => 'announcement'], function () {
        Route::GET('/all',  'AnnoucementController@allAnnouncement');
        Route::POST('/add',  'AnnoucementController@addAnnouncement');
        Route::POST('/edit',  'AnnoucementController@editAnnoucement');
        Route::POST('/delete',  'AnnoucementController@deleteAnnouncement');
    });

    //?settings
    Route::group(['prefix' => 'setting'], function () {
        Route::GET('/showUser', 'SettingController@showUser');
        Route::POST('/editUser', 'SettingController@editUser');

        Route::GET('/staffList', 'SettingController@MembersOfStaff');
        Route::GET('/showStaff/{id}', 'SettingController@showStaff');
        Route::POST('/editStaff', 'SettingController@editStaff');
        Route::GET('/removeStaff/{id}', 'SettingController@removeUser');

        Route::GET('/roleSelection', 'SettingController@roleSelection');
        Route::POST('/addStaff', 'SettingController@addStaff');

        Route::GET('/settings', 'SettingController@settings');
        Route::POST('/updateSetting', 'SettingController@updateSettings');

        Route::POST('/setTarget', 'SettingController@assignSalesTarget');
    });

    //?summary

    Route::group(['prefix' => 'summary'], function () {

        Route::GET('/timeGraph/{date?}', 'SummaryController@timeGraph');
        Route::GET('/crypto_transaction', 'SummaryController@cryptoTransaction');
        Route::POST('/sortCrypto', 'SummaryController@sortCryptoTransaction');
        Route::GET('/giftCard_transaction', 'SummaryController@giftCardTransactions');
        Route::POST('/sortGiftCards', 'SummaryController@sortGiftCardTransactions');

        Route::GET('transaction_detail','SummaryController@transactionsDetails');
        Route::POST('sort_transaction_detail','SummaryController@sortTransaction');


   });

    // Verification
    Route::group(['prefix' => 'verification'], function () {
        Route::GET('/get-all-verifications',  'AdminController@allVerification');
        Route::put('/user-verification/{verification}',  'AdminController@verifyUser');
        Route::put('/cancel-verification/{verification}', 'AdminController@cancelVerification');
    });


    // ReferralSettings
    Route::group(['prefix' => 'referral'], function () {
        Route::GET('/', 'ReferralSettingController@index');
        Route::GET('/settings', 'ReferralSettingController@settings');
    });

});


Route::group(['middleware' => ['auth:api', 'verified', 'coo']], function () {
    Route::get('/test', function(){
        return response()->json(['message' => 'test']);
    });
    //?Customer Happiness
    Route::group(['prefix' => 'customerHappiness'], function () {

        Route::GET('/Overview', 'CustomerHappinessController@overview');
        Route::POST('/addStaff', 'CustomerHappinessController@addStaff');
        Route::GET('/showStaff/{id}', 'CustomerHappinessController@showStaff');
        Route::POST('/editStaff', 'CustomerHappinessController@editStaff');
        Route::GET('/removeStaff/{id}', 'CustomerHappinessController@removeUser');

        Route::GET('/activateUser/{id}/{status}', 'CustomerHappinessController@activateUser');
   });

   //? Nexus
   Route::group(['prefix' => 'nexus'], function () {
    Route::GET('/nexusOverview/{date?}', 'NexusController@verificationData');
    Route::GET('/nexusCrypto/{date?}', 'NexusController@NexusCrypto');
    Route::GET('/nexusGiftCard/{date?}', 'NexusController@NexusGiftCard');

    });

    //? pulseTransactionsAnalytics
    Route::group(['prefix' => 'pulse'], function () {
        Route::GET('/Analytics/{startDate?}/{endDate?}/{transaction_type?}/{transaction_duration?}', 'pulseAnalyticsController@pulseTransactionAnalytics');
    });



    Route::get('/customer-life', 'CustomerLifeController@index');



});
