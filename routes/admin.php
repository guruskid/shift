<?php

Route::group(['middleware'=>'manager'], function(){
    Route::get('/rates', 'RateController@index')->name('admin.rates');
    Route::post('/card-combination/create', 'RateController@store')->name('admin.rate.add');

    Route::post('/card/create', 'CardController@store')->name('admin.card.create');
    Route::post('/edit-card', 'CardController@editCard' )->name('admin.card.edit');

    Route::post('/currency/store', 'CurrencyController@store')->name('admin.currency.store');

    Route::post('/card-type/store', 'PaymentMediumController@store')->name('admin.card-type.store');

    Route::post('/rate/update', 'RateController@update')->name('admin.rate.update');
    Route::get('/rate/delete/{id}', 'RateController@deleteRate');
    Route::get('/index', 'CurrencyController@index')->name('admin.currency.index');
});

Route::group(['middleware' => 'seniorAccountant'], function () {

    Route::get('/get-wallet-details/{account_number}', 'NairaTransactionController@getWalletDetails');

    Route::get('/naira-transaction/create', 'NairaTransactionController@create')->name('admin.naira-transaction.create');
    Route::post('/naira-transaction/store', 'NairaTransactionController@store')->name('admin.naira-transaction.store');

    Route::get('/profit-manager', 'NairaTransactionController@profits')->name('admin.profits');
    Route::POST('/send-charges', 'NairaTransactionController@sendCharges')->name('admin.send-charges');

    Route::get('/bitcoin-wallets', 'BitcoinWalletController@wallets')->name('admin.bitcoin-wallets');
    Route::POST('/bitcoin-wallet', 'BitcoinWalletController@createHdWallet')->name('admin.bitcoin-wallet.create');

    Route::POST('/set-bitcoin-charge', 'BitcoinWalletController@setCharge')->name('admin.set-bitcoin-charge');

});


Route::group(['middleware' => ['accountant'] ], function () {
    Route::post('/admin-transfer', 'AssetTransactionController@payTransaction' )->name('admin.transfer');
    Route::post('/admin-btc-transfer', 'AssetTransactionController@payBtcTransaction' )->name('admin.btc-transfer');

    Route::post('/admin-hd-wallet-recieve-hghdhfh-ehe7sjdhsjaqwe', 'BitcoinWalletController@webhook' )->name('admin.hdwallet-recieve');

    Route::get('/setup-webhooks', 'BitcoinWalletController@webhooks' );

});
