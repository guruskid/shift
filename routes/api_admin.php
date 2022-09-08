<?php

use App\ReferralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::group(['middleware' => ['auth:api', 'verified', 'super', 'cors']], function () {

    //UTILITIES TRANSACTIONS
    Route::group(['prefix' => 'utility-transaction'], function () {
        Route::get('/', 'UtilityController@allUtility');
        Route::GET('/airtime',  'UtilityController@airtime');
        Route::POST('/utilities-by-date-search',  'UtilityController@utilitiesSearch');
        Route::GET('/data',  'UtilityController@data');
        Route::GET('/power',  'UtilityController@power');
        Route::GET('/cable',  'UtilityController@cable');
    });

    Route::prefix('rate')->group(function () {
        Route::get('/', 'RateController@index');
    });

    Route::prefix('currency')->group(function () {
        Route::POST('/store', 'CurrencyController@store');
    });

    Route::prefix('card')->group(function () {
        Route::post('/create', 'CardsController@store');
        Route::post('/edit', 'CardsController@editCard');
        Route::GET('/delete-card/{id}', 'CardsController@deleteCard');
    });


    Route::GET('/total-user-balance', 'AdminController@totalUserBalance');
    Route::POST('/promote-to-admin', 'AdminController@promoteToAdmin');
    Route::GET('/user/{email}',  'AdminController@userSearch');


    Route::get('/accountant/active', 'AdminController@activeAccountant');

    // TRANSACTIONS
    Route::group(['prefix' => 'transaction'], function () {
        Route::GET('/btc',  'TransactionController@btc');
        Route::GET('/p2p',  'TransactionController@p2p');
        Route::GET('/transactions-per-day',  'TransactionController@transactionsPerDay');
        Route::GET('/transactions-by-date',  'TransactionController@transactionsByDate');
    });

    // Transaction Count
    Route::group(['prefix' => 'transaction-count'], function () {
        Route::GET('/{waiting}',  'TransactionController@TransactionCounts');
        Route::GET('/{pending}',  'TransactionController@TransactionCounts');
        Route::GET('/withdrawals',  'TransactionController@TransactionCounts');
    });


    Route::group(['prefix' => 'users'], function () {
        Route::GET('/',  'UserController@index');
        Route::GET('/new-users',  'UserController@newUsers');
        Route::GET('/{id}',  'UserController@user');
    });


    Route::POST('/admin/add-admin',  'AdminController@addAdmin');
    Route::POST('/admin/action',  'AdminController@action');

    Route::get('/customer', 'AdminController@customerHappiness');
    Route::get("/all-accountant", 'AdminController@accountant');


    // // Announcement
    // Route::group(['prefix' => 'announcement'], function () {
    //     Route::GET('/all',  'AnnoucementController@allAnnouncement');
    //     Route::POST('/add',  'AnnoucementController@addAnnouncement');
    //     Route::POST('/edit',  'AnnoucementController@editAnnoucement');
    //     Route::POST('/delete',  'AnnoucementController@deleteAnnouncement');
    // });

    // Announcement
    Route::group(['prefix' => 'announcement'], function () {
        Route::post('/create',  'AnnouncementController@create');
        Route::get('/all',  'AnnouncementController@getAnnouncements');
        Route::post('/update/{id}',  'AnnouncementController@update');
        Route::post('/update-status/{id}/{status}',  'AnnouncementController@updateStatus');
        Route::post('/delete/{id}',  'AnnouncementController@delete');
    });

    //settings
    Route::group(['prefix' => 'setting'], function () {
        Route::GET('/showUser', 'SettingController@showUser');
        Route::POST('/editUser', 'SettingController@editUser');

        Route::GET('/staffList', 'SettingController@MembersOfStaff');
        Route::GET('/showStaff/{id}', 'SettingController@showStaff');
        Route::POST('/editStaff', 'SettingController@editStaff');
        Route::GET('/removeStaff/{id}', 'SettingController@removeUser');

        Route::GET('/roleSelection', 'SettingController@roleSelection');
        Route::POST('/getUserByEmail', 'SettingController@getUserByEmail');
        Route::POST('/addStaff', 'SettingController@addStaff');

        Route::GET('/settings', 'SettingController@settings');
        Route::POST('/updateSetting', 'SettingController@updateSettings');

        Route::POST('/setTarget', 'SettingController@assignSalesTarget');

        Route::POST('/activateStaff', 'SettingController@activateStaff');
        Route::POST('/deactivateStaff', 'SettingController@deactivateStaff');
    });

    //Accountants
    Route::group(['prefix' => 'accountant'], function () {
        Route::GET('/listOfAccountants', 'AccountantController@listOfAccountants');
        Route::GET('/ChartAndTransaction', 'AccountantController@ChartAndTransactions');
        Route::GET('/summary', 'AccountantController@summary');
        Route::POST('/activateAccountant',  'AccountantController@activateAccountant');
        Route::POST('/deactivateAccountant',  'AccountantController@deactivateAccountant');
    });

    //summary
    Route::group(['prefix' => 'summary'], function () {

        Route::GET('/timeGraph/{date?}', 'SummaryController@timeGraph');
        Route::GET('/crypto_transaction', 'SummaryController@cryptoTransaction');
        Route::POST('/sortCrypto', 'SummaryController@sortCryptoTransaction');
        Route::GET('/giftCard_transaction', 'SummaryController@giftCardTransactions');
        Route::POST('/sortGiftCards', 'SummaryController@sortGiftCardTransactions');

        Route::GET('/transaction_detail','SummaryController@transactionsDetails');
        Route::POST('/sort_transaction_detail','SummaryController@sortTransaction');


   });

    // Verification
    Route::group(['prefix' => 'verification'], function () {
        Route::GET('/get-all-verifications',  'AdminController@allVerification');
        Route::put('/user-verification/{verification}',  'AdminController@verifyUser');
        Route::put('/cancel-verification/{verification}', 'AdminController@cancelVerification');
        Route::GET('/get-verification-percentages',  'AdminController@verificationByPercentage');

    });


    // ReferralSettings
    Route::group(['prefix' => 'referral'], function () {
        Route::GET('/', 'ReferralSettingController@index');
        Route::GET('/settings', 'ReferralSettingController@settings');
        Route::GET('/switch/{id}/{status}', 'ReferralSettingController@switch');
        Route::POST('/switch/percentage', 'ReferralSettingController@percentage');

    });

    Route::group(['prefix', 'charts'], function () {
        Route::GET('/monthly-transaction-analytics', 'ChartController@monthlyTransactionAnalytics');
        Route::GET('/monthly-new-user-analytics', 'ChartController@monthlyUserAnalytics');
    });


    Route::prefix('wallet')->group( function () {
        Route::prefix('bitcoin')->group(function(){

            Route::GET('/', 'BitcoinWalletController@index');
            Route::get('/bitcoin-wallets', 'BitcoinWalletController@wallets');
            Route::get('/bitcoin-hd-wallets', 'BitcoinWalletController@hdWallets');
            Route::get('/bitcoin-charges', 'BitcoinWalletController@charges');
            Route::post('/transfer-bitcoin-charges', 'BitcoinWalletController@transferCharges');
            Route::get('/bitcoin-wallet-transactions', 'BitcoinWalletController@transactions');


            Route::POST('/create', 'BitcoinWalletController@createHdWallet');
            Route::POST('/set-charge', 'BitcoinWalletController@setCharge');
            Route::POST('/send-from-hd-wallet', 'BitcoinWalletController@sendFromHd');
            Route::post('/add-address','BitcoinWalletController@addAddress');
            Route::POST('/send-from-admin-wallet', 'BitcoinWalletController@send');
            Route::GET('/btc-migration-wallet', 'BitcoinWalletController@migrationWallet');


            Route::put('/confirm-migration/{migration}', 'BitcoinWalletController@confirmMigration');

            Route::get('/bitcoin-users-balance', 'SummaryController@ledgerBalance');
            Route::POST('/bitcoin-new-txn', 'BitcoinWalletController@addTxn');

            Route::post('/service-fee', 'BitcoinWalletController@setFee');

        });


        Route::prefix('usdt')->group(function(){
            Route::GET('/', 'UsdtController@index');
            Route::get('/settings', 'UsdtController@settings');
            Route::post('/filter-sell-price', 'UsdtController@settings');
            Route::post('/update-rate', 'UsdtController@updateRate');

            Route::get('/smart-contracts', 'UsdtController@contracts');
            Route::post('/deploy-contract', 'UsdtController@deployContract');
            Route::get('/activate-contract/{id}', 'UsdtController@activate');
        });
    });

    // Dashboard Overview
    Route::prefix('dashboard')->group(function () {
        Route::get('/overview', 'DashboardOverviewController@overview');
        Route::get('/number-of-new-users',  'SpotLightController@numberOfNewUsers');
        Route::get('/get-users-by-date',  'SpotLightController@getNewUsersByDate');
        Route::post('/acquisition-cost',  'SpotLightController@getCustomerAcquisitionCost');
        Route::get('/transaction-history/{type}',  'DashboardOverviewController@transactionHistory');
        Route::get('/p2p-transaction-history',  'DashboardOverviewController@p2pTransactionHistory');
        Route::get('/p2p-transactions-by-date',  'DashboardOverviewController@getP2pTransactionHistoryByDate');
        Route::get('/crypto-transactions-by-date',  'DashboardOverviewController@getCryptoTransactionHistoryByDate');
        Route::get('/users-verification',  'DashboardOverviewController@getCryptoTransactionHistoryByDate');
        Route::get('/users-verification', 'DashboardOverviewController@usersVerification');
        Route::get('/monthly-analytics', 'SpotLightController@monthlyAnalytics');
        Route::get('/monthly-earnings', 'DashboardOverviewController@monthlyEarnings');
        Route::get('/summary', 'DashboardOverviewController@summary');

        Route::GET('/graph-analytics',  'SpotLightController@graphAnalytics');
        Route::GET('/turnover-graph-analytics',  'SpotLightController@turnOverGraphAnalytics');
    });


    //Customer Happiness
    Route::group(['prefix' => 'customerHappiness'], function () {

        Route::GET('/', 'CustomerHappinessController@overview');
        Route::GET('/user', 'CustomerHappinessController@CustomerHappinessData');

        Route::POST('/activateCustomerHappiness',  'CustomerHappinessController@activateCustomerHappiness');
        Route::POST('/deactivateCustomerHappiness',  'CustomerHappinessController@deactivateCustomerHappiness');
   });


});


Route::group(['middleware' => ['auth:api', 'coo', 'cors']], function () {
    Route::get('/test', function(){
        return response()->json(['message' => 'test']);
    });

   //Nexus
   Route::group(['prefix' => 'nexus'], function () {
    Route::GET('/', 'NexusController@verificationData');
    Route::GET('/nexusCrypto', 'NexusController@NexusCrypto');
    Route::GET('/nexusGiftCard', 'NexusController@NexusGiftCard');
    Route::GET('/timeGraph','NexusController@timeGraph');

    });

    //pulseTransactionsAnalytics
    Route::group(['prefix' => 'pulse'], function () {
        Route::GET('/Analytics', 'pulseAnalyticsController@pulseTransactionAnalytics');
        Route::POST('/SortAnalytics', 'pulseAnalyticsController@sortTransactionAnalytics');

        Route::GET('/', 'PulseController@index');
        Route::POST('/modal','PulseController@ModalData');
        Route::GET('/chart', 'PulseController@chart');
        Route::POST('/sortChart', 'PulseController@sortChart');

    });

    //csLifetime
    Route::group(['prefix' => 'csLifetime'], function () {
        Route::get('/', 'CustomerLifeController@index');
        Route::post('/sort', 'CustomerLifeController@sorting');
        Route::get('/chart', 'CustomerLifeController@ChartData');
        Route::post('/chartSort', 'CustomerLifeController@sortChartData');
    });

    //Sales Analytics
    Route::group(['prefix' => 'salesAnalytics'], function () {
        Route::GET('/newUsers/{category?}', 'SalesNewUsersController@loadNewUsers');
        Route::POST('/newUsersSort', 'SalesNewUsersController@sortNewUsers');

         Route::GET('/oldUsers/{category?}', 'SalesOldUsersController@loadOldUsers');
         Route::POST('/oldUsersSort', 'SalesOldUsersController@sortOldUsers');
    });

    Route::group(['prefix' => 'spotlight'], function () {
        Route::GET('/stats', 'SpotLightController@stats');
        Route::GET('/recent-transactions', 'SpotLightController@recentTransactions');
        Route::GET('/staff-on-role', 'SpotLightController@staffOnRole');
        Route::GET('/monthly-analytics',  'SpotLightController@monthlyAnalytics');
        Route::GET('/other-graph',  'SpotLightController@otherGraph');
        Route::POST('/acquisition-cost',  'SpotLightController@getCustomerAcquisitionCost');

        Route::GET('/number-of-users',  'SpotLightController@numberOfNewUsers');
        Route::GET('/get-users-by-date',  'SpotLightController@getNewUsersByDate');

        Route::GET('/graph-analytics',  'SpotLightController@graphAnalytics');
        Route::GET('/turnover-graph-analytics',  'SpotLightController@turnOverGraphAnalytics');

    });
});

Route::group(['middleware' => ['auth:api', 'customerHappiness', 'cors']], function(){
    Route::prefix('customer-happiness')->group(function () {
        Route::GET('/', 'CustomerHappinessController@overview');
        Route::GET('/p2p',  'CustomerHappinessController@p2p');
        Route::GET('/users',  'CustomerHappinessController@userProfile');
        Route::GET('/user/{email}',  'CustomerHappinessController@userSearch');
        Route::GET('/transactions',  'CustomerHappinessController@customerHappinessTransactions');
    });
});

