<?php

Route::group(['middleware' => 'auth:api'], function () {
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

});
