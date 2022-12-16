<?php

use App\Http\Controllers\ApiV2\Admin\AccountantController;
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
        Route::GET('/transaction-trial-1',  'TransactionController@trialTransactions1');
        Route::GET('/transaction-trial-2',  'TransactionController@trialTransactions2');
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
        Route::GET('/get-currently-active-accountant', 'AccountantController@getCurrentActiveAccountant');
        Route::GET('/get-last-active-accountant', 'AccountantController@getLastActiveAccountant');
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
        // Route::get('/users-verification',  'DashboardOverviewController@getCryptoTransactionHistoryByDate');
        Route::get('/users-verification/{type}', 'DashboardOverviewController@usersVerification');
        Route::get('/monthly-analytics', 'SpotLightController@monthlyAnalytics');
        Route::get('/monthly-earnings', 'DashboardOverviewController@monthlyEarnings');
        Route::get('/summary', 'DashboardOverviewController@summary');

        Route::GET('/graph-analytics',  'SpotLightController@graphAnalytics');
        Route::GET('/turnover-graph-analytics',  'SpotLightController@turnOverGraphAnalytics');
    });

    Route::prefix('user-rating')->group(function () {
        Route::get('/', 'UserRateManager@index');
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


    Route::GET('/global-search',  'SpotLightController@globalSearch');

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
        Route::GET('/stats', 'SpotLightController@newStats');
        // Route::GET('/new-stats', 'SpotLightController@newStats');
        // Route::GET('/recent-transactions', 'SpotLightController@recentTransactions');
        Route::GET('/recent-transactions', 'SpotLightController@newRecentTransactions');
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

Route::group(['middleware' => ['auth:api', 'seniorAccountant', 'cors']], function () {
    //payBridge
    Route::group(['prefix' => 'payBridge'], function () {
        Route::group(['prefix' => 'bank'], function () {
            Route::GET('/list', 'PayBridgeController@index');
            Route::POST('/add', 'PayBridgeController@addBank');
            Route::GET('/show/{id}', 'PayBridgeController@showBank');
            Route::GET('activate/{id}', 'PayBridgeController@activateBank');
            Route::GET('deactivate/{id}', 'PayBridgeController@deactivateBank');
        });
        Route::group(['prefix' => 'transactions'], function () {
            Route::GET('/list', 'PayBridgeController@p2p');
            Route::POST('/sort', 'PayBridgeController@p2pSorting');
            Route::GET('/filter', 'PayBridgeController@loadFilter');
        });

    });

    Route::group(['prefix' => 'accountant'], function () {
        Route::GET("/overview",'SeniorAccountant\AccountantController@AccountantOverview');
        Route::GET('/get-active-accountant', 'SeniorAccountant\AccountantController@GetActiveAccountant');
        Route::GET('/get-activation-history', 'SeniorAccountant\AccountantController@getActivationHistory');
        Route::GET('/activate/{id}', 'SeniorAccountant\AccountantController@activateAccountant');
        Route::GET('/deactivate/{id}', 'SeniorAccountant\AccountantController@deactivateAccountant');

    });

    Route::group(['prefix' => 'wallet'], function () {
        Route::GET("/overview",'SeniorAccountant\AccountantController@WalletOverview');
        Route::GET("/monthly-withdrawal-charges/{month?}/{year?}",'SeniorAccountant\AccountantController@MonthlyWithdrawalCharges');
        Route::GET("/freeze-user-wallet/{userID}",'SeniorAccountant\AccountantController@freezeUserWallet');
        Route::post("/deposit-withdrawal",'SeniorAccountant\AccountantController@depositWithdrawal');




    });

    //Overview

    Route::group(['prefix' => 'overview'], function () {
        Route::GET("/",'SeniorAccountant\DashboardController@overView');
        Route::GET('/analytics', 'SeniorAccountant\DashboardController@analytics');
        Route::GET('/recenttrans', 'SeniorAccountant\DashboardController@recentTransactions');
        Route::GET('/statement/{id}', 'SeniorAccountant\DashboardController@transPerUser');

    });

      //Rate

    Route::group(['prefix' => 'rate'], function () {
        Route::GET("/updateusd",'SeniorAccountant\RateController@updateUsd');
        Route::GET('/updaterate', 'SeniorAccountant\RateController@updateRate');
        Route::GET('/delete/{id}', 'SeniorAccountant\RateController@deleteRate');

    });



    Route::group(['prefix' => 'complianceAndFraud'], function () {
        Route::GET('/users/{type?}', 'ComplianceFraudController@index');
        Route::POST('/sort', 'ComplianceFraudController@sorting');
        Route::GET('/user/{id}', 'ComplianceFraudController@getUser');

        Route::group(['prefix' => 'flagged'], function () {
            Route::GET('/', 'ComplianceFraudController@flaggedTransactions');
            Route::POST('/sort', 'ComplianceFraudController@sortFlaggedTransaction');
        });
    });

    Route::group(['prefix' => 'users'], function () {
        Route::GET('/', 'UserController@allUsers');
        Route::POST('/sort', 'UserController@UserSort');
        Route::GET('/show/{id}', 'UserController@showUser');
        Route::POST('/withholdFunds', 'UserController@withholdFunds');
        Route::POST('/clearFunds', 'UserController@clearAmount');
        Route::GET('/freeze/{id}', 'UserController@freezeWallet');
        Route::GET('/activate/{id}', 'UserController@activateWallet');
    });
    Route::group(['prefix' => 'analytics'], function () {
        Route::GET('/', 'AnalyticsController@Analytics');
        Route::POST('/sort', 'AnalyticsController@sortAnalytics');
    });

    Route::group(['prefix' => 'transactionAnalytics'], function () {
        Route::GET('/', 'pulseAnalyticsController@pulseTransactionAnalytics');
        Route::POST('/sort', 'pulseAnalyticsController@sortTransactionAnalytics');
    });

});



Route::group(['middleware' =>['auth:api', 'accountant', 'cors']], function () {
    Route::prefix('junior-accountant')->group(function () {

        Route::prefix('overview')->group(function () {
            Route::get('/paybridge-transactions', 'DashboardOverviewController@paybridgeTransactions');
            Route::get('/recent-transactions', 'DashboardOverviewController@recentTransactions');
            Route::get('/compliance-fraud', 'DashboardOverviewController@complianceFraud');
            Route::get('/summary', 'DashboardOverviewController@juniorAccountantSummary');

        });

       // paybridge

        Route::prefix('payBridge')->group(function () {
            Route::group(['prefix' => 'bank'], function () {
                Route::GET('/list', 'PayBridgeController@index');
                Route::POST('/add', 'PayBridgeController@addBank');
                Route::GET('/show/{id}', 'PayBridgeController@showBank');
                Route::GET('activate/{id}', 'PayBridgeController@activateBank');
                Route::GET('deactivate/{id}', 'PayBridgeController@deactivateBank');
            });
        });

        Route::prefix('rate')->group(function () {
            Route::get('/overview', 'RateController@overview');
            Route::get('/delete/{id}', 'RateController@deleteRate');
            Route::get('/update', 'RateController@updateRate');
        });


        // transactions
        Route::prefix('transactions')->group(function () {
            Route::get('/overview', 'TransactionController@overview');

        });

        // Notification
        Route::prefix('notifications')->group(function () {
            Route::get('/', 'NotificationsController@index');
            Route::get('/show/{id}', 'NotificationsController@show');
            Route::get('/read-all-clear-all', 'NotificationsController@mark_clear_all');

        });

        Route::prefix('verifications')->group(function () {
            Route::get('/overview', 'VerificationsController@overview');
            Route::get('/decline', 'VerificationsController@approveVerification');
            Route::get('/approve', 'VerificationsController@approveVerification');


        });


        Route::prefix('accountants')->group(function () {
            Route::get('/list', 'AccountantController@listOfAccountantOfficers');


        });









    });


    Route::prefix('accounting-officer')->group(function () {

        Route::prefix('overview')->group(function () {

            Route::get('/analytics', 'DashboardOverviewController@accountingOfficerOverview');
            Route::get('/current-rate', 'DashboardOverviewController@currentRate');



        });

        Route::prefix('session-summary')->group(function () {
            Route::get('/', 'SummarySessionController@index');

        });

        Route::prefix('transactions')->group(function () {
            Route::get('/summary', 'TransactionController@overview');



            Route::prefix('p2p')->group(function () {
                Route::get('/overview', 'PayBridgeController@p2pTransactionsSUmmary');
                Route::get('/transaction-overview', 'PayBridgeController@p2pTransactions');
                Route::get('/sort-by-accountant/{accountant_id}', 'PayBridgeController@sortP2PByAccountant');
                Route::get('/analytics', 'PayBridgeController@p2pAnalytics');

            });

        });


        // transactions
        Route::prefix('transactions')->group(function () {
            Route::prefix('overview')->group(function () {
                Route::get('/', 'TransactionController@overview');
            });

        });

        // Notification
        Route::prefix('notifications')->group(function () {
            Route::get('/', 'NotificationsController@index');
            Route::get('/show/{id}', 'NotificationsController@show');
            Route::get('/read-all-clear-all', 'NotificationsController@mark_clear_all');
        });

        Route::prefix('verifications')->group(function () {
            Route::get('/overview', 'VerificationsController@overview');
            Route::get('/decline', 'VerificationsController@approveVerification');
            Route::get('/approve', 'VerificationsController@approveVerification');
        });

        Route::prefix('accountants')->group(function () {
            Route::get('/list', 'AccountantController@listOfAccountantOfficers');

        });
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


Route::group(['middleware' => ['auth:api', 'contentCurator',  'cors']], function(){
    Route::prefix('content')->group(function () {
        // Blog Category
        Route::prefix('category')->group(function () {
            Route::get('/', 'ContentController@FetchCategories');
            Route::post('/', 'ContentController@addBlogCategory');
            Route::put('/{id}', 'ContentController@updateBlogCategory');
            Route::delete('/{id}', 'ContentController@deleteBlogCategory');
        });
        // Blog Heading
        Route::prefix('heading')->group(function () {
            Route::post('/', 'ContentController@addBlogHeading');
            Route::get('/', 'ContentController@fetchBlogHeadings');
            Route::put('/{id}', 'ContentController@updateBlogHeading');
            Route::delete('/{id}', 'ContentController@deleteBlogHeading');
        });
        // Blog posts
        Route::prefix('blog')->group(function () {
            Route::post('/', 'ContentController@storeBlog');
            Route::get('/', 'ContentController@fetchBlogPosts');
            Route::get('/{id}', 'ContentController@showPost');
            Route::delete('/{id}', 'ContentController@destroyBlog');
            Route::post('/{id}', 'ContentController@updateBlog');

        });


    });
});

