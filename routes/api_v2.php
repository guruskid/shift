<?php

use Illuminate\Http\Request;


//Register and login here and other routes that dont require authentication

// Route::get('test-route', 'testController@index');
Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::get('mail-check', 'AuthController@verificationCodeEmail');


Route::group(['middleware' => 'auth:api'], function () {

    //Authenticated routes here
    // Email Verifiction code
    Route::post('/email-verification', 'AuthController@emailVerification');
    Route::post('/resend-code', 'AuthController@resendCode');

    // Level 1 OTP Phone Verification
    Route::post('/send-otp', 'AuthController@sendOtp');
    Route::post('/resend-otp', 'AuthController@resendOtp');
    Route::post('/verify-phone', 'AuthController@verifyPhone');


    Route::get('/dashboard', 'UserController@dashboard');
    Route::post('/upload-idcard', 'UserController@uploadId');
    Route::post('/upload-address', 'UserController@uploadAddress');
    Route::post('/update-dp', 'ProfileController@updateDp');

    // profile
    Route::get('/profile', 'ProfileController@index');

});

