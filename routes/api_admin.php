<?php

use Illuminate\Http\Request;

// Route::GET('/',  'UtilityController@index');

Route::group(['middleware' => ['auth:api', 'verified', 'super']], function () {
    Route::GET('/utility-transaction/airtime',  'UtilityController@airtime');
    Route::GET('/utility-transaction/data',  'UtilityController@data');
    Route::GET('/utility-transaction/power',  'UtilityController@power');
    Route::GET('/utility-transaction/cable',  'UtilityController@cable');
    // Route::get('/utility/airtime', 'UserController@deleteBank');
    // Route::get('/read-not/{id}', 'UserController@readNot');
});

// Route::GET('/verification-limit', 'Admin\VerificationLimitController@get');
// Route::get('/banks', 'Api\AuthController@bankList' );
// Route::get('ref', 'Admin\ReferralSettingsController@referral_bonus');

// Route::group(['middleware' => 'auth:api'], function () {

//     Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'verified', 'admin', 'super' ]], function () {
//         Route::get('/utility/airtime', 'UserController@deleteBank');
//         Route::get('/read-not/{id}', 'UserController@readNot');
//     });

// });
