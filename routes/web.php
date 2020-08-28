<?php

use Illuminate\Routing\RouteGroup;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('mailable', function () {
    return new App\Mail\DantownNotification('yeeah', 'testing' );
});


Auth::routes();
Route::get('/', 'HomeController@index')->name('welcome');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/setup-bank-account', 'HomeController@setupBank')->name('user.setup-bank');
Route::post('/setup-bank-account', 'HomeController@addUserBank')->name('signup.add-bank');

Route::view('/test', 'user.test');

Route::get('/message/{id}', 'MessageController@index')->name('message');
Route::get('/read-messages/{id}', 'MessageController@read')->name('message');
/* Route::post('/message', 'MessageController@store')->name('message.store'); */
Route::post('/pop', 'MessageController@pop')->name('message.pop');
Route::get('/conversation-details/{id}', 'MessageController@convDetails');

Route::get('/inbox', 'ChatController@inbox')->name('admin.inbox');
Route::get('/agents', 'ChatController@agents');

/* Upload Transaction image */
Route::post('/transacion-image', 'PopController@add')->name('transaction.add-image');

/* Add Admin middleware */
Route::get('/user-details/{id}', 'ChatController@userDetails');
Route::get('/user-transactions/{id}', 'ChatController@userTransactions');
Route::post('/get-bank-details', 'NairaWalletController@acctDetails');

/* Callbacks */
Route::post('/naira/recieve-funds-dhfhshd', 'NairaWalletController@callback')->name('recieve-funds.callback');
Route::post('/naira/recharge/dhfhd-q23-nfnd-dnf', 'BillsPaymentController@rechargeCallback')->name('recharge-card.callback');
Route::post('/naira/electricity/dddsfhd-q23-nfnd-dnf', 'BillsPaymentController@electricityCallback')->name('electricity.callback');

Route::group(['prefix' => 'user', 'middleware' => ['auth', 'checkName'] ], function () {
    /* ajax calls */
    Route::get('/get-card/{card}', 'UserController@getCard');
    Route::get('/get-country/{card}', 'UserController@getCountry');
    Route::get('/get-type/{card}', 'UserController@getType');
    Route::get('/get-wallet-id/{card}', 'UserController@getWalletId');
    Route::POST('/get-rate', 'UserController@getRate')->name('rate');
    Route::POST('/add_transaction', 'UserController@addTransaction');
    /* Profile Ajax functions */
    Route::post('update-profile', "UserController@updateProfile");
    Route::post('update-bank', "UserController@updateBank"); /* its for new bank details */
    Route::get('/get-bank/{id}', 'UserController@getBank');
    Route::get('/delete-bank/{id}', 'UserController@deleteBank');
    Route::get('/read-not/{id}', 'UserController@readNot');
    /* ajax ends here */

    Route::get('/', 'UserController@dashboard')->name('user.dashboard');
    Route::get('/calculator', 'UserController@calculator')->name('user.calculator');
    Route::get('/calculator/crypto', 'UserController@calcCrypto')->name('user.calcCrypto');
    Route::get('/calculator/gift-card', 'UserController@calcCard')->name('user.calcCard');
    Route::get('/rates', 'UserController@rates')->name('user.rates');
    Route::view('account', 'user.profile')->name('user.profile');
    Route::POST('/account', 'UserController@updateProfile')->name('user.update_profile');
    Route::POST('/id_card', 'UserController@idcard')->name('user.idcard');
    Route::get('/transactions', 'UserController@transactions')->name('user.transactions');
    Route::view('change-password', 'user.password')->name('user.password');
    Route::POST('/change-password', 'UserController@password')->name('user.change_password');
    Route::POST('/profile-picture', 'UserController@profilePicture')->name('user.dp');
    Route::POST('/user-bank-details', 'UserController@updateBankDetails')->name('user.update_bank_details');
    Route::get('/view-transaction/{id}/{uid}', 'UserController@viewTransac')->name('user.view-transaction');
    Route::get('/notifications', 'UserController@notifications')->name('user.notifications');
    Route::POST('/notification-switch', 'UserController@notificationSetting');

   /*  Route::get('/chat', 'UserController@chat')->name('user.chat');
    Route::get('/chat/{id}', 'UserController@chat'); */

    Route::get('/portfolio', 'PortfolioController@view')->name('user.portfolio');
    Route::get('/wallet', 'PortfolioController@nairaWallet')->name('user.naira-wallet');
    Route::POST('/naira-wallet/create', 'NairaWalletController@create')->name('user.create-naira');
    Route::POST('/naira-wallet/password', 'NairaWalletController@changePassword')->name('user.update-naira-password');
    Route::POST('/transfer-funds', 'NairaWalletController@transfer')->name('user.transfer');

    Route::get('/pay-bills', 'BillsPaymentController@view')->name('user.bills');
    Route::post('/get-dec-user', 'BillsPaymentController@getUser');
    Route::post('/get-tv-packages', 'BillsPaymentController@getPackages');
    Route::get('/paytv', 'BillsPaymentController@paytvView')->name('user.paytv');
    Route::post('/paytv', 'BillsPaymentController@paytv')->name('user.paytv');
    Route::view('/recharge', 'user.recharge')->name('user.recharge');
    Route::post('/airtime', 'BillsPaymentController@airtime')->name('user.airtime');
    Route::view('/discounted-airtime', 'user.discount_airtime')->name('user.discount-airtime');
    Route::get('/electricity', 'BillsPaymentController@electricityView')->name('user.electricity');
    Route::post('/get-elect-user', 'BillsPaymentController@getElectUser');
    Route::post('/electricity', 'BillsPaymentController@payElectricity')->name('user.electricity');

    /* Route::get('/banklist', 'NairaWalletController@banklist')->name('user.banklist'); */
});


Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin'] ], function () {
     /* ajax calls */
     Route::GET('/get-user/{email}', 'AdminController@getUser');
     Route::GET('/get-rate/{id}', 'AdminController@getRate');
     Route::GET('/get-transac/{id}', 'AdminController@getTransac');
     Route::GET('/get-card/{id}', 'AdminController@getCard');
     Route::GET('/get-notification/{id}', 'AdminController@getNotification');
     Route::GET('/update-transaction/{id}/{status}', 'AdminController@updateTransaction');

    /* ajax ends here */
    Route::get('/', 'AdminController@dashboard')->name('admin.dashboard');
    Route::get('/rates', 'AdminController@rates')->name('admin.rates');

    Route::get('/transactions', 'AdminController@transactions')->name('admin.transactions');
    Route::get('/transactions/buy', 'AdminController@buyTransac')->name('admin.buy_transac');
    Route::get('/transactions/sell', 'AdminController@sellTransac')->name('admin.sell_transac');
    Route::get('/transactions/successful', 'AdminController@successTransac')->name('admin.success_transac');
    Route::get('/transactions/failed', 'AdminController@failedTransac')->name('admin.failed_transac');
    Route::get('/transactions/waiting', 'AdminController@waitingTransac')->name('admin.waiting_transac');
    Route::get('/transactions/declined', 'AdminController@declinedTransac')->name('admin.declined_transac');
    Route::get('/transactions/assigned', 'AdminController@assignedTransac')->name('admin.assigned-transactions');
    Route::post('/edit-transactions', 'AdminController@editTransaction' )->name('admin.edit_transaction');
    Route::get('/view-transaction/{id}/{uid}', 'AdminController@viewTransac')->name('admin.view-transaction');
    Route::get('/chat/{id}', 'ChatController@index')->name('admin.chat');

});

/* For Super Admins Only */
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin', 'super']  ], function () {
    Route::post('/edit-rate', 'AdminController@editRate' )->name('admin.edit_rate');
    Route::post('/cards', 'AdminController@addCard' )->name('admin.add_card');
    Route::post('/edit-card', 'AdminController@editCard' )->name('admin.edit_card');
    Route::post('/rates', 'AdminController@addRate' )->name('admin.add_rate');

    Route::get('/chat-agents', 'ChatAgentController@chatAgents')->name('admin.chat_agents');
    Route::post('/chat-agents', 'ChatAgentController@addChatAgent' )->name('admin.add_chat_agent');
    Route::get('/change-agent/{id}/{action}', 'ChatAgentController@changeStatus');
    Route::get('/remove-agent/{id}', 'ChatAgentController@removeAgent');

    Route::post('/transactions', 'AdminController@addTransaction' )->name('admin.add_transaction');
    Route::GET('/delete-transaction/{id}', 'AdminController@deleteTransac');
    Route::GET('/delete-rate/{id}', 'AdminController@deleteRate');
    Route::GET('/delete-card/{id}', 'AdminController@deleteCard');
    Route::get('/cards', 'AdminController@cards')->name('admin.cards');
    Route::post('/wallet_id', 'AdminController@walletId' )->name('admin.wallet');

    Route::get('/users', 'AdminController@users')->name('admin.users');
    Route::get('/user/{id}/{email}', 'AdminController@user')->name('admin.user');
    Route::get('/verify', 'AdminController@verify')->name('admin.verify');
    Route::post('/verify', 'AdminController@verifyUser' )->name('admin.verify_user');

    Route::get('/notifications', 'AdminController@notification')->name('admin.notification');
    Route::post('/notifications', 'AdminController@addNotification' )->name('admin.add_notification');
    Route::post('/edit-notifications', 'AdminController@editNotification' )->name('admin.edit_notification');
    Route::GET('/delete-notification/{id}', 'AdminController@deleteNotification');

    Route::post('/admin-transfer', 'NairaWalletController@adminTransfer' )->name('admin.transfer');
    Route::post('/admin-refund', 'NairaWalletController@adminRefund' )->name('admin.refund');
    Route::post('/search', 'AdminController@searchUser' )->name('admin.search');

    Route::get('/wallet-transactions/{id?}', 'AdminController@walletTransactions')->name('admin.wallet-transactions');
    Route::post('/wallet-transactions', 'AdminController@walletTransactionsSortByDate')->name('admin.wallet-transactions.sort.by.date');

});


Route::group(['prefix' => 'db'], function () {
    Route::GET('/function', 'DatabaseController@txns');
});

