<?php

Route::group(['prefix' =>'trade_naira_api','middleware' => 'auth:api'], function () {
    Route::get('/agents', 'TradeController@getAgent');
    Route::get('/transactions', 'TradeController@transactions');
    Route::post('/buy_naira', 'TradeController@buyNaira');
    Route::post('/upload-pop', 'TradeController@upload');

    Route::post('/confirm_transaction', 'TradeController@confirm');
    Route::post('/cancel_transaction', 'TradeController@cancel');

    Route::post('/sell_naira', 'TradeController@sellNaira');

    Route::post('/complete_withdrawal', 'TradeController@completeWihtdrawal');
    Route::post('/complete_deposit', 'TradeController@completeDeposit');
    Route::get('/get_stat', 'TradeController@getStat');

    Route::get('/get_transactions', 'TradeController@getTransactions');

});

Route::group(['prefix' => 'user', 'middleware' => ['auth']], function () {
    Route::get('/agents', 'TradeController@getAgent');
    Route::get('/transactions', 'TradeController@transactions');
    Route::post('/buy_naira', 'TradeController@buyNaira');
    Route::post('/upload-pop', 'TradeController@upload');

    Route::post('/confirm_transaction', 'TradeController@confirm');
    Route::post('/cancel_transaction', 'TradeController@cancel');

    Route::post('/sell_naira', 'TradeController@sellNaira');

    Route::post('/complete_withdrawal', 'TradeController@completeWihtdrawal');
    Route::post('/complete_deposit', 'TradeController@completeDeposit');
    Route::get('/get_stat', 'TradeController@getStat');

    Route::get('/get_transactions', 'TradeController@getTransactions');
    Route::get('/accounts', 'TradeController@accounts');

});
