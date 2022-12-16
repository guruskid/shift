<?php

//Register and login here and other routes that dont require authentication

// Route::get('test-route', 'testController@index');
// Route::post('register/{refcode?}', 'AuthController@register');

use App\Http\Controllers\ApiV2\ReferralController;


Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::get('mail-check', 'AuthController@verificationCodeEmail');

Route::post('forgot-password', 'AuthController@checkForgotPasswordEmail');
Route::post('resend-forgot-password-opt', 'AuthController@checkForgotPasswordEmail');
Route::post('validate-forgot-password-opt', 'AuthController@checkForgotPasswordOtp');
Route::post('change-password', 'AuthController@changePassword');

//User DB
Route::get('/user-db', 'UserDbController@getNameAndEmail');
Route::POST('/add-data', 'UserDbController@addUser');

//blog view and categories
Route::get('/all-blogs/{type?}', 'Admin\ContentController@loadBlogView');
Route::get('/all-blogs-categories', 'Admin\ContentController@loadCategories');
Route::get('/blog/{id}', 'Admin\ContentController@loadSingleBlog');

Route::group(['middleware' => ['auth:api', 'frozenUserCheckApi']], function () {

    //Authenticated routes here
    Route::post('/email-verification', 'AuthController@emailVerification');
    Route::post('/resend-code', 'AuthController@resendCode');

    // Reset password
    Route::post('/update-password', 'AuthController@updatePassword');

    // Level 1 OTP Phone Verification
    Route::post('/send-otp', 'AuthController@sendOtp');
    Route::post('/resend-otp', 'AuthController@resendOtp');
    Route::post('/verify-phone', 'AuthController@verifyPhone');

    // Level 2 and 3 verification
    Route::post('/upload-idcard', 'UserController@uploadId');
    Route::post('/upload-address', 'UserController@uploadAddress');

    Route::post('/update-dp', 'UserController@updateDp');

    Route::get('/dashboard', 'UserController@dashboard');
    Route::get('/naria-wallet-balance', 'UserController@nairaWalletBalance');
    Route::get('/net-wallet-balance', 'UserController@netWalletBalance');
    Route::get('/dashboard-transaction-summary', 'UserController@summary');
    Route::get('/profile', 'UserController@profile');
    Route::post('/update-birthday', 'UserController@updateBirthday');

    //Bank STUFF

    Route::get('/bank-list', 'UserController@listOfBanks');
    Route::get('/user-banks', 'UserController@userAccounts');
    Route::post('/add-bank-account', 'UserController@addBankAccount');
    Route::post('/delete-bank-account/{id}', 'UserController@deleteBankAccount');

    //New Bank Stuff

    Route::post('/verify-user-bank', 'UserController@verifyBankName');
    Route::post('/add-user-bank-account', 'UserController@addBankAccDetails');



    //Delete A user

    Route::post('/delete-user', 'UserController@deleteUserAccount');

    //Get user Verification Limit

    Route::get('/user-verify-limit', 'UserController@userVerification');

    //User Notification

    Route::get('/user-notifier', 'UserController@userNotify');
    Route::post('/clear-notification', 'UserController@clearAllNotify');
    Route::post('/mark-all', 'UserController@markAllNotify');
    Route::get('/notifier/{id}', 'UserController@newNotify');

    Route::get('/crypto-transaction', 'UserController@crypto');

    Route::get('/all-balance', 'UserController@allBalance');

    // Referral
    Route::post('/create_referral_code', 'ReferralController@create');
    Route::get('/get_referral_balance', 'ReferralController@getBalance');
    Route::post('/sell_referral_btc', 'ReferralController@sell');
    Route::post('/referral_status', 'ReferralController@referralSystemStatus');
    Route::get('/referral-transactions', 'ReferralController@referralTransactions');
    Route::post('/withdraw-referral-bonus', 'ReferralController@withdrawReferralBonus');
    Route::get('/my-referrers', 'ReferralController@myReferrers');
    Route::get('/get-referrers-link', 'ReferralController@getReferralLink');
    Route::get("/get-referrers-code", [ReferralController::class, "getReferralCode"]);

    // Transactions
    Route::GET('/bitcoin-transactions', 'TransactionController@bitcoinWalletTransactions');
    Route::GET('/naira-transactions', 'TransactionController@nairaTransactions');
    Route::GET('/giftcard-transactions', 'TransactionController@allCardTransactions');
    Route::GET('/utility-transactions', 'TransactionController@utilityTransactions');

    //?Faq
    Route::get('/all-Faq', 'FaqApiController@index');
    Route::get('/view-Faq/{id}', 'FaqApiController@getFaq');
    Route::post('/add-Faq', 'FaqApiController@addFaq');
    Route::post('/update-faq', 'FaqApiController@updateFaq');
    Route::get('/delete-faq/{id}', 'FaqApiController@deleteFaq');

    //?TicketCategory
    Route::post('/add-new-category', 'TicketCategoryController@addCategory');
    Route::post('/add-new-subcategory', 'TicketCategoryController@addSubCategory');
    Route::get('/all-categories', "TicketCategoryController@listofCategories");

    //?ticket
    Route::post('/add-ticket', 'TicketController@createTicket');
    Route::get('/all-user-close-tickets', 'TicketController@closeTicketList');
    Route::get('/all-user-open-tickets', 'TicketController@openTicketList');

    //?messages
    Route::get('/ticket-messages/{ticketNo}', "ChatMessagesController@Messages");
    Route::post('/send-message', 'ChatMessagesController@sendMessage');


    //CRYPTO APIS
    Route::prefix('crypto')->group(function () {
        Route::get('/currencies', 'CryptoController@index');
        Route::post('/create', 'CryptoController@create');
        Route::post('/sell', 'CryptoController@sell');
        Route::post('/buy', 'CryptoController@buy');
        Route::post('/send', 'CryptoController@send');
        Route::get('/transactions/{currency_id}', 'CryptoController@transactions');
        Route::get('/transactionByType', 'CryptoController@cryptoTransactionByType');
    });

    Route::prefix('home')->group(function () {
        Route::get('/', 'HomeController@index');
    });

    Route::prefix('transactions')->group(function () {
        Route::get('/', 'TransactionController@AllUserTransactions');
        Route::get('/show', 'TransactionController@showUserTransaction');
    });


});
