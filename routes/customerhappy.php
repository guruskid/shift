<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api', 'verified', 'cors']], function () {

    //TRANSACTIONS
    Route::group(['prefix' => 'customerhappiness'], function () {
        Route::get('/', 'CustomerHappinessController@overView');


        Route::post('/query/create', 'CustomerHappinessController@newTicket');
        Route::get('/query', 'CustomerHappinessController@queries');
        Route::get('/query/{status}', 'CustomerHappinessController@querySort');
        Route::post('/queryclose/{id}', 'CustomerHappinessController@closeQuery');
        Route::get('/querysort/find', 'CustomerHappinessController@sortByDay');
        Route::post('/queryrange/find', 'CustomerHappinessController@sortByRange');

        Route::get('/select/{list}', 'CustomerHappinessController@getList');



        Route::get('/transactions', 'CustomerHappinessController@alltransaction');

        Route::get('/p2p', 'CustomerHappinessController@p2pTran');
        Route::get('/p2p/{status}', 'CustomerHappinessController@sortP2pbyStatus');

        Route::get('/utility', 'CustomerHappinessController@Utility');

        Route::get('/status/{status}', 'CustomerHappinessController@sortByStatus');

        Route::get('/filter/{type}', 'CustomerHappinessController@filterByType');

        Route::get('/filterutility/{type}', 'CustomerHappinessController@filterUtility');

        Route::get('/users', 'CustomerHappinessController@getUsers');

        Route::get('/search/{username}', 'CustomerHappinessController@searchUser');

        Route::get('/user/{id}', 'CustomerHappinessController@userInfo');

        Route::get('/transaction/{id}', 'CustomerHappinessController@transPerUser');

        Route::get('/trans', 'CustomerHappinessController@recentTransactions');

        ;
    });

});
