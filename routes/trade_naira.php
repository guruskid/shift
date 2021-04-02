<?php

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/agents', 'TradeController@agents');
    Route::get('/transactions', 'TradeController@transactions');
    Route::post('/buy_naira', 'TradeController@buyNaira');
    Route::post('/upload-pop', 'TradeController@upload');

    Route::post('/confirm_transaction', 'TradeController@confirm');
});
