<?php
Route::get('/rates', 'RateController@index')->name('admin.rates');
Route::post('/card-combination/create', 'RateController@store')->name('admin.rate.add');
Route::post('/card-combination/filter', 'RateController@filter')->name('admin.rate.filter');

Route::post('/card/create', 'CardController@store')->name('admin.card.create');
Route::post('/edit-card', 'CardController@editCard' )->name('admin.card.edit');

Route::post('/currency/store', 'CurrencyController@store')->name('admin.currency.store');

Route::post('/card-type/store', 'PaymentMediumController@store')->name('admin.card-type.store');

Route::post('/rate/update', 'RateController@update')->name('admin.rate.update');
Route::get('/rate/delete/{id}', 'RateController@deleteRate');
Route::get('/index', 'CurrencyController@index')->name('admin.currency.index');
Route::post('/usd-rate/update', 'RateController@updateUsd')->name('admin.usd.update');

Route::group(['middleware'=>'managerAndAccountant'], function(){
    // verify user
    Route::get('/user-verification', 'UserController@verifications')->name('admin.user-verifications');
    Route::put('/user-verification/{verification}', 'UserController@verify')->name('admin.verify');
    Route::put('/cancel-verification/{verification}', 'UserController@cancelVerification')->name('admin.cancel-verification');
    Route::get('/user-verification-history', 'UserController@verificationHistory')->name('admin.verification-history');

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

    Route::post('/add-address','BtcWalletController@addAddress')->name('admin.address');

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

    Route::prefix('trade-naira')->group(function () {
        Route::get('/', 'TradeNairaController@index')->name('admin.trade-naira.index');
        Route::get('/agent-transactions/{user}', 'TradeNairaController@agentTransactions')->name('p2p.agent-transactions');
        Route::post('/add-account', 'TradeNairaController@addAccount')->name('agent.add-account');
    });

    Route::get('/service-fee', 'BitcoinWalletController@serviceFee')->name('admin.service-fee');
    Route::post('/service-fee', 'BitcoinWalletController@setFee')->name('admin.set-service-fee');

    //sending
    Route::POST('/ethereum/send', 'EthWalletController@send')->name('admin.eth.send');
    Route::POST('/tron/send', 'TronController@send')->name('admin.tron.send');
    Route::POST('/usdt/send', 'UsdtController@send')->name('admin.usdt.send');

    //BLOCKFILL ORDERS
    Route::get('/blockfill-orders', 'BlockfillOrderController@index')->name('admin.blockfill.orders');

    //Ledger system
    Route::get('/ledger', 'LedgerController@index')->name('admin.ledger');
    Route::get('/negative-ledger', 'LedgerController@negative')->name('admin.negative-ledger');
    Route::get('/resolve-ledger', 'LedgerController@resolve');
    Route::get('/resolve-ledger-transactions', 'LedgerController@resolveTransactions')->name('admin.resolve-transactions');

});

Route::group(['middleware' => 'accountant'], function () {

    Route::prefix('trade-naira')->group(function () {
        Route::get('/accounts', 'TradeNairaController@accounts')->name('p2p.accounts');

        Route::post('/add-account', 'TradeNairaController@addAccount')->name('agent.add-account');
        Route::post('/update-account', 'TradeNairaController@updateAccount')->name('agent.update-account');
        Route::post('/delete-paybridge-account', 'TradeNairaController@deleteAccount')->name('agent.delete-paybridge-account');
    });

    Route::prefix('flagged')->group(function () {
        Route::get('/{type?}', 'FlaggedTransactionsController@index')->name('admin.flagged.home');
        Route::get('/clear/{flaggedTransaction}', 'FlaggedTransactionsController@clear')->name('admin.flagged.clear');
    });

});


Route::group(['middleware' => ['AccountOfficer'] ], function () {
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

    Route::prefix('tron')->group(function () {
        Route::get('/', 'TronController@index')->name('admin.tron');
        Route::get('/settings', 'TronController@settings')->name('admin.tron.settings');
        // Route::post('/update-rate', 'TronController@updateRate')->name('admin.eth.update-rate');

        Route::get('/smart-contracts', 'TronController@contracts')->name('admin.tron.contracts');
        Route::post('/deploy-contract', 'TronController@deployContract')->name('admin.tron.deploy-contract');
        Route::get('/activate-contract/{id}', 'TronController@activate')->name('admin.tron.activate-contract');
    });

    Route::prefix('tether')->group(function () {
        Route::get('/', 'UsdtController@index')->name('admin.tether');
        Route::get('/settings', 'UsdtController@settings')->name('admin.tether.settings');
        Route::post('/filter-sell-price', 'UsdtController@settings')->name('admin.tether.filter-sell-ngn');
        Route::post('/update-rate', 'UsdtController@updateRate')->name('admin.tether.update-rate');

        Route::get('/smart-contracts', 'UsdtController@contracts')->name('admin.tether.contracts');
        Route::post('/deploy-contract', 'UsdtController@deployContract')->name('admin.tether.deploy-contract');
        Route::get('/activate-contract/{id}', 'UsdtController@activate')->name('admin.tether.activate-contract');
    });

    //Trade Naira
    Route::prefix('trade-naira')->group(function () {

        Route::post('/top-up', 'TradeNairaController@topup')->name('admin.trade-naira.topup');
        Route::post('/deduct', 'TradeNairaController@deduct')->name('admin.trade-naira.deduct');

        Route::get('/transactions', 'TradeNairaController@transactions')->name('admin.naira-p2p');
        Route::get('/transactions/{type}/{status?}', 'TradeNairaController@transaction_type')->name('admin.naira-p2p.type');
        Route::any('/transactions/sortbydate','TradeNairaController@sort_transaction_type')->name('admin.naira-p2p.sort');
        Route::post('/transactions/search','TradeNairaController@search_transaction')->name('admin.naira-p2p.search');

        Route::post('/set-limits', 'TradeNairaController@setLimits')->name('admin.naira-p2p.set-limits');
        Route::put('/confirm/{transaction}', 'TradeNairaController@confirm')->name('admin.naira-p2p.confirm');
        Route::put('/confirm-sell/{transaction}', 'TradeNairaController@confirmSell')->name('admin.naira-p2p.confirm-sell');

        Route::put('/update-trade/{transaction}', 'TradeNairaController@assignStatusAction')->name('admin.naira-p2p.update');
        Route::get('/view-trade/{transaction}', 'TradeNairaController@viewTransactions')->name('admin.naira-p2p.view');


        Route::put('/cancel-trade/{transaction}', 'TradeNairaController@declineTrade')->name('admin.naira-p2p.cancel-trade');
        Route::put('/refund-trade/{transaction}', 'TradeNairaController@refundTrade')->name('admin.naira-p2p.refund-trade');
        Route::post('/update-bank-details', 'TradeNairaController@updateBankdetails')->name('agent.update-bank');

        Route::get('/withdrawal-queue', 'TradeNairaController@withdrawal_queue')->name('admin.naira-p2p.withdrawal-queue');
        Route::post('/add-withdrawal-queue', 'TradeNairaController@add_withdrawal_queue')->name('admin.naira-p2p.add-withdrawal-queue');
        Route::post('/update-withdrawal-queue', 'TradeNairaController@update_withdrawal_queue')->name('admin.naira-p2p.update-withdrawal-queue');

    });

});
