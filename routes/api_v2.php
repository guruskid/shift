<?php

use Illuminate\Http\Request;

//Register and login here and other routes that dont require authentication

// Route::get('test-route', 'testController@index');
Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::get('mail-check', 'AuthController@verificationCodeEmail');


Route::group(['middleware' => 'auth:api'], function () {

    //Authenticated routes here
    Route::post('/email-verification', 'AuthController@emailVerification');
    Route::post('/resend-code', 'AuthController@resendCode');


    Route::get('/dashboard', 'UserController@dashboard');

    //?Faq
    Route::get('/all-Faq', 'FaqApiController@index');
    Route::post('/view-Faq/{id}', 'FaqApiController@getFaq');

    //?TicketCategory
    Route::get('/all-categories', "TicketCategoryController@listofCategories");

    //?ticket
    Route::post('/add-ticket' ,'TicketController@createTicket');
    Route::get('/all-user-close-tickets', 'TicketController@closeTicketList');
    Route::get('/all-user-open-tickets', 'TicketController@openTicketList');

    //?messages
    Route::get('/ticket-messages/{ticketNo}', "ChatMessagesController@Messages");
    Route::post('/send-message', 'ChatMessagesController@sendMessage');
});

