<?php
Route::get('/rates', 'RateController@index')->name('admin.rates');
Route::post('/card-combination/create', 'RateController@store')->name('admin.rate.add');

Route::post('/card/create', 'CardController@store')->name('admin.card.create');
Route::post('/edit-card', 'CardController@editCard' )->name('admin.card.edit');

Route::post('/currency/store', 'CurrencyController@store')->name('admin.currency.store');

Route::post('/card-type/store', 'PaymentMediumController@store')->name('admin.card-type.store');

Route::post('/rate/update', 'RateController@update')->name('admin.rate.update');
Route::get('/rate/delete/{id}', 'RateController@deleteRate');
Route::get('/index', 'CurrencyController@index')->name('admin.currency.index');

Route::group(['middleware'=>'manager'], function(){
<<<<<<< HEAD

=======
    Route::get('/user-verification', 'UserController@verifications')->name('admin.user-verifications');
    Route::put('/user-verification/{verification}', 'UserController@verify')->name('admin.verify');
    Route::put('/cancel-verification/{verification}', 'UserController@cancelVerification')->name('admin.cancel-verification');
>>>>>>> 2c50de2c84ed8827a9934b04519e76f327ca79a0
});

Route::group(['middleware' => 'seniorAccountant'], function () {

    Route::get('/get-wallet-details/{account_number}', 'NairaTransactionController@getWalletDetails');

    Route::get('/naira-transaction/create', 'NairaTransactionController@create')->name('admin.naira-transaction.create');
    Route::post('/naira-transaction/store', 'NairaTransactionController@store')->name('admin.naira-transaction.store');

    Route::get('/profit-manager', 'NairaTransactionController@profits')->name('admin.profits');
    Route::POST('/send-charges', 'NairaTransactionController@sendCharges')->name('admin.send-charges');


    Route::post('/freeze-account', 'UserController@freezeAccount')->name('admin.freeze-account');
    Route::post('/activate-account', 'UserController@activateAccount')->name('admin.activate-account');


    Route::POST('/bitcoin-wallet', 'BitcoinWalletController@createHdWallet')->name('admin.bitcoin-wallet.create');

    Route::POST('/set-bitcoin-charge', 'BitcoinWalletController@setCharge')->name('admin.set-bitcoin-charge');
    Route::POST('/send-from-hd-wallet', 'BitcoinWalletController@sendFromHd')->name('admin.btc-hd-wallet.send');

    Route::get('/bitcoin-summary', 'SummaryController@index')->name('admin.bitcoin-summary');
    Route::get('/bitcoin-summary-txns/{summary}', 'SummaryController@transactions')->name('admin.bitcoin-summary-txns');
    Route::get('/bitcoin-users-balance', 'SummaryController@ledgerBalance');

});


Route::group(['middleware' => ['accountant'] ], function () {
    Route::post('/admin-transfer', 'AssetTransactionController@payTransaction' )->name('admin.transfer');
    Route::post('/admin-btc-transfer', 'BitcoinWalletController@payBtcTransaction' )->name('admin.btc-transfer');

    Route::post('/admin-hd-wallet-recieve-hghdhfh-ehe7sjdhsjaqwe', 'BitcoinWalletController@webhook' )->name('admin.hdwallet-recieve');

    Route::get('/setup-webhooks', 'BitcoinWalletController@webhooks' );

    Route::get('/bitcoin', 'BitcoinWalletController@index')->name('admin.bitcoin');
    Route::get('/bitcoin-wallets', 'BitcoinWalletController@wallets')->name('admin.bitcoin-wallets');
    Route::get('/bitcoin-hd-wallets', 'BitcoinWalletController@hdWallets')->name('admin.bitcoin.hd-wallets');
    Route::get('/bitcoin-charges', 'BitcoinWalletController@charges')->name('admin.bitcoin.charges');
    Route::post('/transfer-bitcoin-charges', 'BitcoinWalletController@transferCharges' )->name('admin.bitcoin.transfer-charges');
    Route::get('/bitcoin-wallet-transactions', 'BitcoinWalletController@transactions')->name('admin.bitcoin-wallets-transactions');
<<<<<<< HEAD

    //Trade Naira
    Route::prefix('trade-naira')->group(function () {
        Route::get('/', 'TradeNairaController@index')->name('admin.trade-naira.index');
        Route::post('/top-up', 'TradeNairaController@topup')->name('admin.trade-naira.topup');
        Route::post('/deduct', 'TradeNairaController@deduct')->name('admin.trade-naira.deduct');

        Route::get('/transactions', 'TradeNairaController@transactions')->name('admin.naira-p2p');
        Route::post('/set-limits', 'TradeNairaController@setLimits')->name('admin.naira-p2p.set-limits');
        Route::put('/confirm/{transaction}', 'TradeNairaController@confirm')->name('admin.naira-p2p.confirm');
    });
=======
>>>>>>> 2c50de2c84ed8827a9934b04519e76f327ca79a0
});
