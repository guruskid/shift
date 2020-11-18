<?php

use App\Mail\UserRegistered;
use App\NairaTransaction;
use Illuminate\Support\Facades\Mail;

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


// Route::get('/showcards', function () {
//     return view('newpages.cards');
// });

Route::get('/cardcalculator', function () {
    return view('newpages.cardcalculator');
});
Route::get('/transaction', function () {
    return view('newpages.Transactionscreen');
});
Route::get('/bitcoin', function () {
    return view('newpages.bitcoin');
});
Route::get('/ethereum', function () {
    return view('newpages.ethereumscreen');
});
Route::get('/airtocash', function () {
    return view('newpages.airtimetocash');
});
Route::get('/newprofile', function () {
    return view('newpages.profile');
});
/* Route::get('/newdashboard', function () {
    return view('newpages.dashboard');
});
Route::get('/creditsuccess', function () {
    return view('newpages.creditsuccess');
});
Route::get('/creditfailure', function () {
    return view('newpages.creditfailure');
}); */
Route::get('/smartbudget', function () {
    return view('newpages.smartbudget');
});


Route::get('/choosewallet', function () {
    return view('newpages.choosewallet');
});

//Mobile and tab screen done
Route::get('/walletpage', function () {
    return view('newpages.walletpage');
});

Route::get('/nairawalletmain', function () {
    return view('newpages.nairawalletmain');
});
Route::get('/nairawalletdtodconfirm', function () {
    return view('newpages.nairawalletdtodconfirm');
});

//Mobile and tab screen done
Route::get('/recharge', function () {
    return view('newpages.rechargemenu');
});

//Mobile and Tab done
Route::get('/buyairtime', function () {
    return view('newpages.buyairtime');
});

//Mobile and tab done
Route::get('/buydata', function () {
    return view('newpages.buydata');
});

Route::get('/newlogin', function () {
    return view('newpages.newlogin');
});
Route::get('/newregister', function () {
    return view('newpages.newregister');
});












Route::get('mailable', function () {
    Mail::to('sheanwinston@gmail.com')->send(new UserRegistered() );
    /* $txn = NairaTransaction::where('reference', 'Ln1599637572')->first();
    return new App\Mail\WalletAlert($txn, 'Debit'); */
});


Auth::routes(['verify' => true]);
Route::get('/', 'HomeController@index')->name('welcome');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/setup-bank-account', 'HomeController@setupBank')->name('user.setup-bank');
Route::post('/setup-bank-account', 'HomeController@addUserBank')->name('signup.add-bank');

Route::view('/disabled', 'disabled')->name('disabled');

/* Upload Transaction image */
Route::post('/transacion-image', 'PopController@add')->name('transaction.add-image');

/* Add Admin middleware */
Route::post('/get-bank-details', 'NairaWalletController@acctDetails');

/* Callbacks */
Route::post('/naira/recieve-funds-dhfhshd', 'NairaWalletController@callback')->name('recieve-funds.callback');
Route::post('/naira/recharge/dhfhd-q23-nfnd-dnf', 'BillsPaymentController@rechargeCallback')->name('recharge-card.callback');
Route::post('/naira/electricity/dddsfhd-q23-nfnd-dnf', 'BillsPaymentController@electricityCallback')->name('electricity.callback');

Route::group(['prefix' => 'user', 'middleware' => ['auth', 'checkName'] ], function () {
    /* ajax calls */
    Route::POST('/add_transaction', 'UserController@addTransaction');
    /* Profile Ajax functions */
    Route::post('update-profile', "UserController@updateProfile");
    Route::post('update-bank', "UserController@updateBank"); /* its for new bank details */
    Route::get('/get-bank/{id}', 'UserController@getBank');
    Route::get('/delete-bank/{id}', 'UserController@deleteBank');
    Route::get('/read-not/{id}', 'UserController@readNot');
    /* ajax ends here */

    Route::get('/', 'UserController@dashboard')->name('user.dashboard');
    Route::get('/rates', 'UserController@rates')->name('user.rates');
    Route::view('account', 'user.profile')->name('user.profile');
    Route::POST('/account', 'UserController@updateProfile')->name('user.update_profile');
    Route::POST('/id_card', 'UserController@idcard')->name('user.idcard');
    Route::get('/transactions', 'UserController@transactions')->name('user.transactions');
    Route::view('change-password', 'user.password')->name('user.password');
    Route::POST('/change-password', 'UserController@password')->name('user.change_password');
    Route::POST('/reset-email', 'UserController@resetEmail')->name('user.reset-email');
    Route::POST('/profile-picture', 'UserController@profilePicture')->name('user.dp');
    Route::POST('/user-bank-details', 'UserController@updateBankDetails')->name('user.update_bank_details');
    Route::get('/view-transaction/{id}/{uid}', 'UserController@viewTransac')->name('user.view-transaction');
    Route::get('/notifications', 'UserController@notifications')->name('user.notifications');
    Route::POST('/notification-switch', 'UserController@notificationSetting');

    Route::get('/portfolio', 'PortfolioController@view')->name('user.portfolio');
    Route::get('/wallet', 'PortfolioController@nairaWallet')->name('user.naira-wallet')->middleware('verified');
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

    /* Routes for the new calculator */
    Route::get('/assets/{asset_type?}', 'TradeController@assets')->name('user.assets');
    Route::get('/trade/{trade_type}/{card_id}/{card_name}', 'TradeController@assetRates')->name('user.asset.rate');
    Route::view('/gift-card-calculator', 'user.gift_card_calculator');
    Route::post('/trade', 'TradeController@trade')->name('user.trade-gift-card');
    Route::post('/trade-crypto', 'TradeController@tradeCrypto')->name('user.trade-crypto');

    /* Bitcooin Wallet */
    Route::post('/bitcoin-wallet-create', 'BitcoinWalletController@create')->name('user.bitcoin-wallet.create');
    Route::get('/bitcoin-wallet', 'BitcoinWalletController@wallet')->name('user.bitcoin-wallet');

});


Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin'] ], function () {
     /* ajax calls */
     Route::GET('/get-user/{email}', 'AdminController@getUser');
     Route::GET('/get-rate/{id}', 'AdminController@getRate');
     Route::GET('/get-transac/{id}', 'AdminController@getTransac');
     Route::GET('/get-card/{id}', 'AdminController@getCard');
     Route::GET('/get-notification/{id}', 'AdminController@getNotification');
     Route::GET('/update-transaction/{id}/{status}', 'AdminController@updateTransaction');  //to accept or decline a new transaction

    /* ajax ends here */
    Route::get('/', 'AdminController@dashboard')->name('admin.dashboard');


    Route::get('/transactions', 'AdminController@transactions')->name('admin.transactions');
    Route::get('/transactions/buy', 'AdminController@buyTransac')->name('admin.buy_transac');
    Route::get('/transactions/sell', 'AdminController@sellTransac')->name('admin.sell_transac');
    Route::get('/transactions/{status}', 'AdminController@txnByStatus')->name('admin.transactions-status');
    Route::get('/transactions/agent/assigned', 'AdminController@assignedTransac')->name('admin.assigned-transactions');
    Route::get('/transactions/asset/{id}', 'AdminController@assetTransac')->name('admin.asset-transactions');

    Route::post('/edit-transactions', 'Admin\AssetTransactionController@editTransaction' )->name('admin.edit_transaction');
    Route::post('/asset-transactions', 'AdminController@assetTransactionsSortByDate')->name('admin.transactions-by-date');
    Route::get('/view-transaction/{id}/{uid}', 'AdminController@viewTransac')->name('admin.view-transaction');

    Route::get('/chat/{id}', 'ChatController@index')->name('admin.chat');
});

/* For Super Admins Only */
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin', 'super']  ], function () {
    Route::post('/cards', 'AdminController@addCard' )->name('admin.add_card');


    Route::post('/transactions', 'AdminController@addTransaction' )->name('admin.add_transaction');
    Route::GET('/delete-transaction/{id}', 'AdminController@deleteTransac');

    Route::GET('/delete-card/{id}', 'AdminController@deleteCard');
    Route::get('/cards', 'AdminController@cards')->name('admin.cards');
    Route::post('/wallet_id', 'AdminController@walletId' )->name('admin.wallet');


    Route::get('/verify', 'AdminController@verify')->name('admin.verify');
    Route::post('/verify', 'AdminController@verifyUser' )->name('admin.verify_user');

    Route::get('/verified-users', 'AdminController@verifiedUsers')->name('admin.verified-users');

    Route::get('/notifications', 'AdminController@notification')->name('admin.notification');
    Route::post('/notifications', 'AdminController@addNotification' )->name('admin.add_notification');
    Route::post('/edit-notifications', 'AdminController@editNotification' )->name('admin.edit_notification');
    Route::GET('/delete-notification/{id}', 'AdminController@deleteNotification');

    Route::post('/search', 'AdminController@searchUser' )->name('admin.search');
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin', 'manager'] ], function () {


    Route::get('/remove-agent/{id}', 'ChatAgentController@removeAgent');
});


/* for Senior accountant */
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'seniorAccountant'] ], function () {
    Route::get('/junior-accountants', 'AccountantController@juniorAccountants')->name('admin.accountants');
    Route::get('/accountant-action/{id}/{action}', 'AccountantController@action' )->name('accountant.action');
    Route::post('/add-junior-accountant', 'AccountantController@addJunior' )->name('accountant.add-junior');
    Route::post('/admin-naira-refund', 'NairaWalletController@adminNairaRefund' )->name('admin.naira-refund');

    Route::post('/clear-transfer-charges', 'AdminController@clearTransferCharges' )->name('admin.clear-transfer-charges');
    Route::post('/clear-sms-charges', 'AdminController@clearSmsCharges' )->name('admin.clear-sms-charges');

    Route::get('/query-transaction/{id}', 'NairaWalletController@query' )->name('admin.query-transaction');
    Route::post('/update-naira-transaction', 'NairaWalletController@updateStatus' )->name('admin.update-naira-transaction');
});

/* for super admin and all accountants */
Route::group(['prefix' => 'admin' , 'middleware' => ['auth', 'admin', 'accountant'] ], function () {
    Route::post('/admin-transfer', 'NairaWalletController@adminTransfer' )->name('admin.transfer');
    Route::post('/admin-refund', 'NairaWalletController@adminRefund' )->name('admin.refund');
    Route::get('/wallet-transactions/{id?}', 'AdminController@walletTransactions')->name('admin.wallet-transactions');
    Route::post('/wallet-transactions', 'AdminController@walletTransactionsSortByDate')->name('admin.wallet-transactions.sort.by.date');
    Route::get('/admin-wallet', 'AdminController@adminWallet')->name('admin.admin-wallet');

    Route::get('/users', 'AdminController@users')->name('admin.users');
    Route::get('/user/{id}/{email}', 'AdminController@user')->name('admin.user');

    Route::get('/chat-agents', 'ChatAgentController@chatAgents')->name('admin.chat_agents');
    Route::post('/chat-agents', 'ChatAgentController@addChatAgent' )->name('admin.add_chat_agent');
    Route::get('/change-agent/{id}/{action}', 'ChatAgentController@changeStatus');

    Route::any('/transfer-charges', 'AdminController@transferCharges')->name('admin.wallet-charges');
    Route::any('/old-transfer-charges', 'AdminController@oldTransferCharges')->name('admin.old-wallet-charges');
});


Route::group(['prefix' => 'db'], function () {
    Route::GET('/function', 'DatabaseController@transactions');
});


