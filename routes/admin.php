<?php
Route::group(['middleware' => 'seniorAccountant'], function () {

    Route::get('/get-wallet-details/{account_number}', 'NairaTransactionController@getWalletDetails');

    Route::get('/naira-transaction/create', 'NairaTransactionController@create')->name('admin.naira-transaction.create');
    Route::post('/naira-transaction/store', 'NairaTransactionController@store')->name('admin.naira-transaction.store');

    Route::get('/profit-manager', 'NairaTransactionController@profits')->name('admin.profits');
    Route::POST('/send-charges', 'NairaTransactionController@sendCharges')->name('admin.send-charges');

    Route::get('/bitcoin-wallets', 'BitcoinWalletController@wallets')->name('admin.wallets');
    Route::POST('/bitcoin-wallet', 'BitcoinWalletController@createHdWallet')->name('admin.bitcoin-wallet.create');
});
