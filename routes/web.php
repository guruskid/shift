<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\SummaryController;
use App\Http\Controllers\LiveRateController;
use App\Jobs\RegistrationEmailJob;
use App\Mail\GeneralTemplateOne;
use App\Mail\UserRegistered;
use App\Mail\VerificationCodeMail;
use App\NairaTransaction;
//use Illuminate\Routing\Route;
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

Route::get('email', function () {
    $rad = rand(1000, 9999);
    $otpCode = rand(1000, 9999);
        // VerificationCode::create([
        //     'user_id' => $userId,
        //     'verification_code' => $otpCode
        // ]);
        $title = 'Email Verification Code1';
        $body = 'is your verification code, valid for 5 minutes. to keep your account safe, do not share this code with anyone.';
        $btn_text = '';
        $btn_url = '';

    return new VerificationCodeMail($rad, $title, $body, $btn_text, $btn_url);
});

Route::get('gm', function () {
    $rad = rand(1000, 9999);
    $otpCode = rand(1000, 9999);
        // VerificationCode::create([
        //     'user_id' => $userId,


        //     'verification_code' => $otpCode
        // ]);
        $title = 'Email Verification Code1';
        $body = 'Please upload any national approved identity verification document with your name.
        IDs accepted are; <br>
        National identity card,<br>
        NIMC slip,<br>
        International Passport, Permanent Voter’s card,<br>
        Driver’s license.<br>
';

$name = 'akdjfladfla';
        $btn_text = '';
        $btn_url = '';
        // $p = ;


        // $p = array('National identity card', 'skjfkfaf', 'jadfldfdf');
        $paragraph = array('National identity card','NIMC slip',
            'International Passport, Permanent Voter’s card'
        );

    return new GeneralTemplateOne($title, $body, $btn_text, $btn_url, $name);
});

//Done
Route::get('/etherwallet', function () {
    return view('newpages.ethereumwallet');
});


Route::get('/tetherwallet', function () {
    return view('newpages.tetherwallet');
});
//Done
Route::get('/etherwallet', function () {
    return view('newpages.ethereumwallet');
});


Route::get('/tetherwallet', function () {
    return view('newpages.tetherwallet');
});

Route::get('/airtocash', function () {
    return view('newpages.airtimetocash');
});


//Mobile done
Route::get('/newprofile', function () {
    return view('newpages.profile');
});

//mobile and web done
Route::get('/btc-transaction-hash', function () {
    return view('newpages.btc_payment_transaction2');
});


// mobile and tab screen done
Route::get('/smartbudget', function () {
    return view('newpages.smartbudget');
});


// Route::get('/paytv','BillsPaymentController@CableView')->name('user.paytv');

// Route::get('/paytv','BillsPaymentController@CableRechargeView')->name('user.paytv');


Route::get('/paytv', 'BillsPaymentController@CableView')->name('user.paytv');

//    return view('newpages.paytv');
//});

Route::get('/paybills', 'BillsPaymentController@electricityView')->name('newpages.paybills');

Route::get('/notifications', function () {
    return view('newpages.notifications');
});


//Mobile and tab screen done
Route::get('/recharge', function () {
    return view('newpages.rechargemenu');
});

//Mobile and Tab done
// Route::get('/buyairtime', function () {
//     return view('newpages.buyairtime');
// });

//Mobile and tab done
Route::get('/buydata', function () {
    return view('newpages.buydata');
});

//mobile and tab done
Route::get('/newlogin', function () {
    return view('newpages.newlogin');
});

//mobile and tab done
Route::get('/newsignup', function () {
    return view('newpages.newsignup');
});

// buy airtime with bitcoin
// Route::get('/bitcoin-airtime', function () {
//     return view('newpages.buyairtime');

// });













Route::get('test', function () {
    dd(LiveRateController::btcRate());
    /* $emailJob = (); */

        // dispatch(new RegistrationEmailJob('shean@gmail.com'));
    Mail::to('sheanwinston@gmail.com')->send(new UserRegistered() );
    return new UserRegistered();

    // dispatch(new RegistrationEmailJob('shean@gmail.com'));
    /* Mail::to('sheanwinston@gmail.com')->send(new UserRegistered() ); */

    /* $txn = NairaTransaction::where('reference', 'Ln1599637572')->first();
    return new App\Mail\WalletAlert($txn, 'Debit'); */
});

Route::get('/tested', 'HomeController@test')->name('tested');


Auth::routes(['verify' => true]);
Route::get('/', 'HomeController@index')->name('welcome');
Route::get('/home', 'HomeController@index')->name('home');


//Registration and verification routes
Route::group(['middleware' => ['auth', 'verified','frozenUserCheck']], function () {
    Route::get('/setup-bank-account', 'HomeController@setupBank')->name('user.setup-bank');
    Route::post('/setup-bank-account', 'HomeController@addUserBank')->name('signup.add-bank');
    Route::get('/verify-phone-number', 'HomeController@phoneVerification')->name('user.verify-phone');
    Route::get('/resend-otp', 'HomeController@resendOtp');

    //old users
    Route::get('/send-otp/{phone}/{country_id}', 'HomeController@sendOtp');
    Route::post('/verify-phone', 'HomeController@verifyPhone')->name('user.verify-phone-number');

    //BVN verification
    Route::view('/verify-bvn', 'auth.bvn')->name('user.verify-bvn');
    Route::get('/send-bvn-otp/{bvn}', 'HomeController@sendBvnOtp');
    Route::post('/verify-bvn-otp', 'HomeController@verifyBvnOtp')->name('user.verify-bvn-otp');

    //Upload ID Card
    Route::post('/upload-id-card', 'VerificationController@uploadId')->name('user.upload-id');
    Route::post('/upload-address', 'VerificationController@uploadAddress')->name('user.upload-address');
});

Route::view('/disabled', 'disabled')->name('disabled');

Route::view('/frozen', 'frozenuser')->name('frozenUser');

/* Upload Transaction image */
Route::post('/transacion-image', 'PopController@add')->name('transaction.add-image');

/* Add Admin middleware */
Route::post('/get-bank-details', 'NairaWalletController@acctDetails');

/* Callbacks */
Route::post('/naira/recieve-funds-dhfhshd', 'NairaWalletController@callback')->name('recieve-funds.callback');
Route::post('/naira/recharge/dhfhd-q23-nfnd-dnf', 'BillsPaymentController@rechargeCallback')->name('recharge-card.callback');
Route::post('/naira/electricity/dddsfhd-q23-nfnd-dnf', 'BillsPaymentController@electricityCallback')->name('electricity.callback');

/* Bitcoin Wallet Callback */
Route::post('/wallet-webhook', 'BitcoinWalletController@webhook')->name('user.wallet-webhook');


Route::group(['prefix' => 'user', 'middleware' => ['auth', 'verified', 'checkName','frozenUserCheck'] ], function () {


    /* ajax calls */
    Route::POST('/add_transaction', 'UserController@addTransaction');
    /* Profile Ajax functions */
    Route::post('update-profile', "UserController@updateProfile");
    Route::post('update-bank', "UserController@updateBank"); /* its for new bank details */
    Route::get('/get-bank/{id}', 'UserController@getBank');
    Route::get('/delete-bank/{id}', 'UserController@deleteBank');
    Route::get('/read-not/{id}', 'UserController@readNot');


    Route::get('/user-details/{email}', 'UserController@getUser');

    /* ajax ends here */

    Route::get('/', 'UserController@dashboard')->name('user.dashboard');
    Route::get('/rates', 'UserController@rates')->name('user.rates');
    Route::get('account', 'UserController@account')->name('user.profile');
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
    // Route::post('/notifications', 'UserController@filtermonth')->name('filtermonth');

    Route::POST('/notification-switch', 'UserController@notificationSetting');

    Route::get('/portfolio', 'PortfolioController@view')->name('user.portfolio');
    Route::get('/wallet', 'PortfolioController@nairaWallet')->name('user.naira-wallet');
    Route::POST('/naira-wallet/create', 'NairaWalletController@create')->name('user.create-naira');
    Route::POST('/naira-wallet/password', 'NairaWalletController@changePassword')->name('user.update-naira-password');
    Route::POST('/transfer-funds', 'NairaWalletController@transfer')->name('user.transfer');

    Route::get('/pay-bills', 'BillsPaymentController@view')->name('user.bills');
    Route::post('/get-dec-user', 'BillsPaymentController@getUser');
    Route::post('/get-tv-packages', 'BillsPaymentController@getPackages');
    // Route::view('/paytv', 'newpages.smartbudget')->name('user.paytv');
    // Route::get('/paytv', 'BillsPaymentController@disabledView')->name('user.paytv');
    Route::get('/paytv','BillsPaymentController@CableRechargeView')->name('user.paytv');
    Route::post('/paytv', 'BillsPaymentController@rechargeCable')->name('user.paytv');

    // Route::view('/airtime', 'newpages.buyairtime')->name('user.recharge'); //for naira wallet Airtime view
    Route::get('/airtime', 'BillsPaymentController@nairaRate')->name('user.airtime'); // for the naira rate for the bitcoin

    Route::post('/airtime', 'BillsPaymentController@buyAirtime')->name('user.airtime'); // the post for the naira wallet
    Route::post('/bitcoin-airtime', 'BillsPaymentController@bitcoinAirtime')->name('user.bitcoin-airtime'); // the post for the bitcoin wallet
    Route::get('/data', 'BillsPaymentController@buyDataView')->name('user.data');
    Route::post('/buy-data', 'BillsPaymentController@buyData')->name('user.buy-data');
    Route::get('/discounted-airtime', 'BillsPaymentController@disabledView')->name('user.discount-airtime');
    Route::get('/airtime-to-cash', 'BillsPaymentController@disabledView')->name('user.airtime-to-cash');
    Route::post('/airtime-to-cash', 'BillsPaymentController@airtimeToCash')->name('user.airtime-to-cash');
    // Route::get('/electricity', 'BillsPaymentController@disabledView')->name('user.electricity');
    Route::get('/electricity', 'BillsPaymentController@electricityRechargeView')->name('user.electricity');
    Route::post('/get-elect-user', 'BillsPaymentController@getElectUser');

    Route::post('/electricity', 'BillsPaymentController@payElectricityVtpass')->name('user.pay-electricity');
    Route::post('/get-variations/{serviveId}', 'BillsPaymentController@getVariations');
    Route::post('/get-merchant/{serviveId}/{billercode}', 'BillsPaymentController@merchantVerify');

    /* Routes for the new calculator */
    Route::get('/assets/{asset_type?}', 'TradeController@assets')->name('user.assets');
    Route::get('/trade/{trade_type}/{card_id}/{card_name}', 'TradeController@assetRates')->name('user.asset.rate');
    Route::view('/gift-card-calculator', 'user.gift_card_calculator');
    Route::post('/trade', 'TradeController@trade')->name('user.trade-gift-card');
    Route::post('/trade-crypto', 'TradeController@tradeCrypto')->name('user.trade-crypto');

    /* Bitcoin  */
    Route::get('/bitcoin-wallet', 'BtcWalletController@wallet')->name('user.bitcoin-wallet');
    Route::post('/sell-bitcoin', 'BtcWalletController@sell')->name('user.sell-bitcoin');
    // Route::get('/get-bitcoin-ngn', 'BtcWalletController@getbitcoinNgn'); deprecated
    Route::post('/bitcoin-wallet-create', 'BtcWalletController@create')->name('user.bitcoin-wallet.create');

    Route::post('/trade-bitcoin', 'BitcoinWalletController@trade')->name('user.trade-bitcoin');
    Route::post('/send-bitcoin', 'BtcWalletController@send')->name('user.send-bitcoin');
    Route::get('/bitcoin-fees/{address}/{fees}', 'BtcWalletController@fees')->name('user.bitcoin-fees');

    Route::prefix('ethereum')->group(function () {
        Route::post('/create', 'EthWalletController@create')->name('ethereum.create');
        Route::get('/wallet', 'EthWalletController@wallet')->name('user.ethereum-wallet');
        Route::get('/fees/{address}/{amount}', 'EthWalletController@fees')->name('user.ethereum-fees');
        Route::post('/send', 'EthWalletController@send')->name('ethereum.send');
        Route::get('/trade', 'EthWalletController@trade')->name('ethereum.trade');
        Route::post('/sell', 'EthWalletController@sell')->name('ethereum.sell');
    });

    Route::prefix('tron')->group(function () {
        Route::post('/create', 'TronWalletController@create')->name('tron.create');
        Route::get('/wallet', 'TronWalletController@wallet')->name('user.tron-wallet');
        Route::get('/fees/{address}/{amount}', 'TronWalletController@fees')->name('user.tron-fees');
        Route::post('/send', 'TronWalletController@send')->name('tron.send');
        Route::get('/trade', 'TronWalletController@trade')->name('tron.trade');
        Route::post('/sell', 'TronWalletController@sell')->name('tron.sell');
    });

    Route::prefix('usdt')->group(function () {
        Route::post('/create', 'UsdtController@create')->name('usdt.create');
        Route::get('/wallet', 'UsdtController@wallet')->name('user.usdt-wallet');
        Route::get('/fees/{address}/{amount}', 'UsdtController@fees')->name('user.usdt-fees');
        Route::post('/send', 'UsdtController@send')->name('usdt.send');
        Route::get('/trade', 'UsdtController@trade')->name('usdt.trade');
        Route::post('/sell', 'UsdtController@sell')->name('usdt.sell');
        Route::post('/buy', 'UsdtController@buy')->name('usdt.buy');
    });

    /* Route::get('/user-bitcoin-balance', 'BitcoinWalletController@btc_balance')->name('user.bitcoin-wallet'); */
});





Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
    /* ajax calls */
    Route::GET('/get-user/{email}', 'AdminController@getUser');
    Route::GET('/dashboard', 'AdminController@dashboard')->name('admin.chinese_dashboard');
    Route::GET('/chinese-dashboards', 'ChineseController@dashboard')->name('admin.chinese_dashboard_page');

    // Route::GET('/payout-transactions', 'AdminController@payoutTransactions')->name('admin.payout_transactions');
    // Route::GET('/payout-history', 'AdminController@payOutHistory')->name('admin.payout_history');

    // To be move to super Admin dashboard later
    // Route::GET('/payout-history', 'ChineseController@payouthistory')->name('admin.payout_history');
    ////////////
    Route::GET('/get-transaction-count', 'AdminController@countTransaction');
    Route::GET('/get-rate/{id}', 'AdminController@getRate');
    Route::GET('/get-transac/{id}', 'AdminController@getTransac');
    Route::GET('/get-card/{id}', 'AdminController@getCard');
    Route::GET('/get-notification/{id}', 'AdminController@getNotification');
    Route::GET('/update-transaction/{id}/{status}', 'AdminController@updateTransaction');  //to accept or decline a new transaction

    /* ajax ends here */
    Route::get('/', 'AdminController@dashboard')->name('admin.dashboard');
    Route::get('/transactions', 'AdminController@transactions')->name('admin.transactions');



    Route::get('/transactions', 'AdminController@transactions')->name('admin.transactions');
    Route::post('/search-transactions','AdminController@search_tnx')->name('admin.search-tnxs');

    Route::get('/transactions/buy', 'AdminController@buyTransac')->name('admin.buy_transac');
    Route::get('/transactions/sell', 'AdminController@sellTransac')->name('admin.sell_transac');
    Route::get('/transactions/{status}', 'AdminController@txnByStatus')->name('admin.transactions-status');
    Route::get('/transactions/agent/assigned', 'AdminController@assignedTransac')->name('admin.assigned-transactions');
    Route::get('/transactions/asset/{id}', 'AdminController@assetTransac')->name('admin.asset-transactions');
    Route::post('/edit-transactions', 'Admin\AssetTransactionController@editTransaction')->name('admin.edit_transaction');
    Route::post('/asset-transactions', 'AdminController@assetTransactionsSortByDate')->name('admin.transactions-by-date');
    Route::get('/view-transaction/{id}/{uid}', 'AdminController@viewTransac')->name('admin.view-transaction');

    Route::get('/chat/{id}', 'ChatController@index')->name('admin.chat');
    Route::get('/accountant-summary/{month?}/{day?}','Admin\SummaryController@summaryhomepage')->name('admin.junior-summary');
    Route::get('/accountant-summary/{month}/{day}/{category}','Admin\SummaryController@summary_tnx_category')->name('admin.junior-summary-details');
    Route::any('/sort-accountant-summary','Admin\SummaryController@sorting')->name('admin.junior-summary-sort-details');
});


Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin', 'chinese']], function () {
    /* ajax calls */
    Route::GET('/chinese-dashboard', 'AdminController@dashboard')->name('admin.chinese_dashboard');
    Route::GET('/chinese-admin', 'ChineseController@chineseAdminUser')->name('admin.chinese_admins');
    Route::post('/add-chinese-admin', 'ChineseController@addChineseAdmin')->name('admin.chinese_add_admin');
    Route::get('/admin-action/{id}/{action}', 'ChineseController@action')->name('admin.chinese_add_admin.action');
    Route::GET('/payout-transactions/{type?}', 'AdminController@payoutTransactions')->name('admin.payout_transactions');
    Route::GET('/payout-history', 'AdminController@payOutHistory')->name('admin.payout_history');
    Route::post('/admin-transfer-chinese/{id}', 'Admin\AssetTransactionController@payTransactionChinese' )->name('admin.transfer-chinese');
    // To be move to super Admin dashboard later
});


/* For Super Admins Only */
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin', 'super', 'chinese']], function () {
    Route::post('/cards', 'AdminController@addCard')->name('admin.add_card');
    Route::get('/referral', 'Admin\ReferralSettingsController@index')->name('admin.referral');
    Route::post('/change-referral-status', 'Admin\ReferralSettingsController@changeStatus')->name('admin.change_referral_status');
    Route::post('/change-referral-percentage', 'Admin\ReferralSettingsController@changePercentage')->name('admin.set_referral_percentage');
    Route::post('/set-referral', 'Admin\ReferralSettingsController@setReferral')->name('admin.set_referral');

    // Route::GET('/payout-transactions', 'AdminController@payoutTransactions')->name('admin.payout_transactions');
    // Route::GET('/payout-history', 'AdminController@payOutHistory')->name('admin.payout_history');
    Route::POST('/payout', 'AdminController@payout')->name('admin.payout');

    Route::post('/transactions', 'AdminController@addTransaction')->name('admin.add_transaction');

    Route::GET('/delete-transaction/{id}', 'AdminController@deleteTransac');

    Route::GET('/delete-card/{id}', 'AdminController@deleteCard');
    Route::get('/cards', 'AdminController@cards')->name('admin.cards');
    Route::post('/wallet_id', 'AdminController@walletId')->name('admin.wallet');



    Route::get('/verify', 'AdminController@verify')->name('admin.verify');
    Route::post('/verify', 'AdminController@verifyUser')->name('admin.verify_user');


    Route::get('/verified-users', 'AdminController@verifiedUsers')->name('admin.verified-users');

    Route::get('/notifications', 'AdminController@notification')->name('admin.notification');
    Route::post('/notifications', 'AdminController@addNotification')->name('admin.add_notification');
    Route::post('/edit-notifications', 'AdminController@editNotification')->name('admin.edit_notification');
    Route::GET('/delete-notification/{id}', 'AdminController@deleteNotification');

    Route::post('/search', 'AdminController@searchUser')->name('admin.search');

    Route::get('/general-settings', 'GeneralSettings@index')->name('admin.general_settings');
    Route::post('/general-settings', 'GeneralSettings@updateConfig')->name('admin.general_settings');
    Route::post('/update-setting', 'GeneralSettings@updateSettings');
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin', 'manager']], function () {


    Route::get('/remove-agent/{id}', 'ChatAgentController@removeAgent');

    Route::get('/faq', 'FaqController@index')->name('admin.faq');
    Route::post('/faqs', 'FaqController@addFaq')->name('admin.newfaq');
    Route::get('/edit-faq/{id}/{title}', 'FaqController@editFaqView')->name('admin.edit-faq');
    Route::POST('/edit-faq', 'FaqController@updateFaq')->name('admin.updatefaq');
    Route::GET('/delete-faq/{id}/{title}', 'FaqController@deleteFaq')->name('admin.deletefaq');
    Route::GET('/download-database', 'AdminController@downloadUserDb')->name('admin.userdb');
    Route::POST('/download-database-search', 'AdminController@downloadUserDbsearch')->name('admin.userdbsearch');
    Route::GET('/verification-limit', 'Admin\VerificationLimitController@index')->name('admin.verification_limit');
    Route::POST('/update-verification-limit', 'Admin\VerificationLimitController@addLimit')->name('admin.add_verification_limit');
    Route::GET('/image-slider', 'Admin\ImageSliderController@index')->name('admin.image_slider');
    Route::POST('/upload-image-slider', 'Admin\ImageSliderController@upload')->name('admin.upload_image_slider');
    Route::POST('/update-image-slider', 'Admin\ImageSliderController@updateImage')->name('admin.update_image_slider');
    Route::GET('/delete-image-slider/{id}', 'Admin\ImageSliderController@deleteImage')->name('admin.delete_image_slider');

});


/* for Senior accountant */
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'seniorAccountant']], function () {
    Route::get('/junior-accountants', 'AccountantController@juniorAccountants')->name('admin.accountants');
    Route::get('/accountant-action/{id}/{action}', 'AccountantController@action')->name('accountant.action');
    Route::post('/add-junior-accountant', 'AccountantController@addJunior')->name('accountant.add-junior');
    Route::post('/admin-naira-refund', 'NairaWalletController@adminNairaRefund')->name('admin.naira-refund');

    Route::post('/clear-transfer-charges', 'AdminController@clearTransferCharges')->name('admin.clear-transfer-charges');
    Route::post('/clear-sms-charges', 'AdminController@clearSmsCharges')->name('admin.clear-sms-charges');


});

/* for super admin and all accountants */
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin', 'accountant']], function () {
    Route::get('/query-transaction/{id}', 'NairaWalletController@query')->name('admin.query-transaction');
    Route::post('/update-naira-transaction', 'NairaWalletController@updateStatus')->name('admin.update-naira-transaction');

    Route::post('/admin-refund', 'NairaWalletController@adminRefund')->name('admin.refund');
    Route::get('/wallet-transactions/{id?}', 'AdminController@walletTransactions')->name('admin.wallet-transactions');
    Route::post('/wallet-transactions', 'AdminController@walletTransactionsSortByDate')->name('admin.wallet-transactions.sort.by.date');
    Route::get('/admin-wallet', 'AdminController@adminWallet')->name('admin.admin-wallet');

    Route::any('/users', 'AdminController@users')->name('admin.users');
    Route::any('/users/search', 'AdminController@user_search')->name('admin.user-search');
    Route::get('/user/{id}/{email}', 'AdminController@user')->name('admin.user');


    Route::get('/chat-agents', 'ChatAgentController@chatAgents')->name('admin.chat_agents');
    Route::post('/chat-agents', 'ChatAgentController@addChatAgent')->name('admin.add_chat_agent');
    Route::get('/change-agent/{id}/{action}', 'ChatAgentController@changeStatus');

    Route::any('/transfer-charges', 'AdminController@transferCharges')->name('admin.wallet-charges');
    Route::any('/old-transfer-charges', 'AdminController@oldTransferCharges')->name('admin.old-wallet-charges');

    Route::get('/utility-transactions', 'Admin\UtilityTransactions@index')->name('admin.utility-transactions');
    Route::post('/utility-transactions-requery/{tranx}', 'Admin\UtilityTransactions@requery')->name('admin.utility-requery');
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin', 'accountantManager']], function () {

Route::any('/users', 'AdminController@users')->name('admin.users');
Route::any('/users/search', 'AdminController@user_search')->name('admin.user-search');
Route::get('/user/{id}/{email}', 'AdminController@user')->name('admin.user');

});

/* Customer Happiness routes*/
// Route::group([ 'prefix' => 'admin', 'middleware' =>['auth', 'customerHappiness']],function(){
//     Route::get('/Overview', 'customerHappinessController@index')->name('customerHappiness.overview');
//     Route::get('/transactions', 'customerHappinessController@getTransactions')->name('customerHappiness.transactions');
//     Route::get('/Chat/{status}', 'customerHappinessController@chatDetails')->name('customerHappiness.chatdetails');
//     Route::post('/Chat', 'customerHappinessController@chat')->name('customerHappiness.chat');

//     Route::post('/logout','customerHappinessController@logout')->name('customerHappiness.logout');


// });

Route::group([ 'prefix' => 'customerhappiness', 'middleware' =>['auth', 'customerHappiness']],function(){
    Route::get('/homepage', 'CustomerHappinessController@index')->name('customerHappiness.homepage');
    Route::get('/Chat/{status?}/{ticketNo?}', 'CustomerHappinessController@chatDetails')->name('customerHappiness.chatdetails');
    Route::post('/Chat', 'CustomerHappinessController@chat')->name('customerHappiness.chat');


    //? transactions
    Route::get('/transactions', 'CustomerHappinessController@transactions')->name('customerHappiness.transactions');
    Route::get('/user/{id}/{email}', 'CustomerHappinessController@user')->name('customerHappiness.user');
    Route::get('/transactions/buy', 'CustomerHappinessController@buyTransac')->name('customerHappiness.buy_transac');
    Route::get('/transactions/sell', 'CustomerHappinessController@sellTransac')->name('customerHappiness.sell_transac');
    Route::get('/transactions/asset/{id}', 'CustomerHappinessController@assetTransac')->name('customerHappiness.asset-transactions');

    Route::get('/wallet-transactions/{id?}', 'CustomerHappinessController@walletTransactions')->name('customerHappiness.wallet-transactions');
    Route::post('/wallet-transactions', 'CustomerHappinessController@walletTransactionsSortByDate')->name('customerHappiness.wallet-transactions.sort.by.date');
    Route::get('/utility-transactions', 'CustomerHappinessController@UtilityTnx')->name('customerHappiness.utility-transactions');

    Route::get('/transactions/{status}', 'CustomerHappinessController@txnByStatus')->name('customerHappiness.transactions-status');
    Route::post('/asset-transactions', 'CustomerHappinessController@assetTransactionsSortByDate')->name('customerHappiness.transactions-by-date');

    Route::any('/search-transactions','CustomerHappinessController@search_tnx')->name('customerHappiness.search-tnxs');


    Route::get('/accountant-summary/{month?}/{day?}','Admin\SummaryController@summaryhomepage')->name('ch.junior-summary');
    Route::get('/accountant-summary/{month}/{day}/{category}','Admin\SummaryController@summary_tnx_category')->name('ch.junior-summary-details');
    Route::any('/sort-accountant-summary','Admin\SummaryController@sort_tnx')->name('ch.junior-summary-sort-details');

});
