<?php

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


    // Verification
    Route::group(['prefix' => 'verification'], function () {
        Route::GET('/get-all-verifications',  'AdminController@allVerification');
        Route::put('/user-verification/{verification}',  'AdminController@verifyUser');
        Route::put('/cancel-verification/{verification}', 'AdminController@cancelVerification');
    });

});
