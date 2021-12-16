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
Route::post('/usd-rate/update', 'RateController@updateUsd')->name('admin.usd.update');

Route::group(['middleware'=>'manager'], function(){

    Route::get('/user-verification', 'UserController@verifications')->name('admin.user-verifications');
    Route::put('/user-verification/{verification}', 'UserController@verify')->name('admin.verify');
    Route::put('/cancel-verification/{verification}', 'UserController@cancelVerification')->name('admin.cancel-verification');

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


    Route::POST('/send-from-admin-wallet', 'BtcWalletController@send')->name('admin.btc.send');
    Route::GET('/btc-migration-wallet', 'BtcWalletController@migrationWallet')->name('admin.btc.migration');
    Route::put('/confirm-migration/{migration}', 'BtcWalletController@confirmMigration')->name('admin.migration.confirm');


    Route::get('/summary/{id}', 'SummaryController@index')->name('admin.crypto-summary');
    Route::get('/summary-txns/{summary}/{card_id}', 'SummaryController@transactions')->name('admin.crypto-summary-txns');
    Route::post('/summary-txns/sort/{card_id}', 'SummaryController@sortTransactions')->name('admin.crypto-summary-txns.sort');


    Route::get('/bitcoin-users-balance', 'SummaryController@ledgerBalance');
    Route::view('/bitcoin-new-txn', 'admin.bitcoin_wallet.new_txn');
    Route::POST('/bitcoin-new-txn', 'BitcoinWalletController@addTxn')->name('admin.bitcoin.add-txn');

    Route::get('/service-fee', 'BitcoinWalletController@serviceFee')->name('admin.service-fee');
    Route::post('/service-fee', 'BitcoinWalletController@setFee')->name('admin.set-service-fee');


    Route::get('/service-fee', 'BitcoinWalletController@serviceFee')->name('admin.service-fee');
    Route::post('/service-fee', 'BitcoinWalletController@setFee')->name('admin.set-service-fee');


    Route::prefix('trade-naira')->group(function () {
        Route::get('/', 'TradeNairaController@index')->name('admin.trade-naira.index');
        Route::get('/agent-transactions/{user}', 'TradeNairaController@agentTransactions')->name('p2p.agent-transactions');
        Route::get('/accounts', 'TradeNairaController@accounts')->name('p2p.accounts');

        Route::post('/add-account', 'TradeNairaController@addAccount')->name('agent.add-account');
        Route::post('/update-account', 'TradeNairaController@updateAccount')->name('agent.update-account');
    });

    //Eth
    Route::POST('/ethereum/send', 'EthWalletController@send')->name('admin.eth.send');
});


Route::group(['middleware' => ['accountant'] ], function () {
    Route::post('/admin-transfer', 'AssetTransactionController@payTransaction' )->name('admin.transfer');
    Route::post('/admin-btc-transfer', 'BitcoinWalletController@payBtcTransaction' )->name('admin.btc-transfer');

    Route::post('/admin-hd-wallet-recieve-hghdhfh-ehe7sjdhsjaqwe', 'BitcoinWalletController@webhook' )->name('admin.hdwallet-recieve');

    Route::get('/setup-webhooks', 'BitcoinWalletController@webhooks' );

    Route::get('/bitcoin', 'BtcWalletController@index')->name('admin.bitcoin');
    Route::get('/bitcoin-wallets', 'BitcoinWalletController@wallets')->name('admin.bitcoin-wallets');
    Route::get('/bitcoin-hd-wallets', 'BitcoinWalletController@hdWallets')->name('admin.bitcoin.hd-wallets');
    Route::get('/bitcoin-charges', 'BitcoinWalletController@charges')->name('admin.bitcoin.charges');
    Route::post('/transfer-bitcoin-charges', 'BitcoinWalletController@transferCharges' )->name('admin.bitcoin.transfer-charges');
    Route::get('/bitcoin-wallet-transactions', 'BitcoinWalletController@transactions')->name('admin.bitcoin-wallets-transactions');

    Route::get('/bitcoin-live-balance-transactions', 'BitcoinWalletController@liveBalanceTransactions')->name('live-balance.transactions');

    Route::post('/update-settings', 'SettingController@set' )->name('admin.settings.update');

    Route::prefix('ethereum')->group(function () {
        Route::get('/', 'EthWalletController@index')->name('admin.ethereum');
        Route::get('/settings', 'EthWalletController@settings')->name('admin.ethereum.settings');
        Route::post('/update-rate', 'EthWalletController@updateRate')->name('admin.eth.update-rate');
    });

    //Trade Naira
    Route::prefix('trade-naira')->group(function () {

        Route::post('/top-up', 'TradeNairaController@topup')->name('admin.trade-naira.topup');
        Route::post('/deduct', 'TradeNairaController@deduct')->name('admin.trade-naira.deduct');

        Route::get('/transactions', 'TradeNairaController@transactions')->name('admin.naira-p2p');
        Route::post('/set-limits', 'TradeNairaController@setLimits')->name('admin.naira-p2p.set-limits');
        Route::put('/confirm/{transaction}', 'TradeNairaController@confirm')->name('admin.naira-p2p.confirm');
        Route::put('/confirm-sell/{transaction}', 'TradeNairaController@confirmSell')->name('admin.naira-p2p.confirm-sell');
        Route::put('/cancel-trade/{transaction}', 'TradeNairaController@declineTrade')->name('admin.naira-p2p.cancel-trade');
        Route::put('/refund-trade/{transaction}', 'TradeNairaController@refundTrade')->name('admin.naira-p2p.refund-trade');
        Route::post('/update-bank-details', 'TradeNairaController@updateBankdetails')->name('agent.update-bank');

    });

});
