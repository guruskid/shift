<?php

Route::group(['middleware' => ['auth:api', 'verified']], function () {

    //TRANSACTIONS
    Route::group(['prefix' => 'customerhappiness'], function () {
         Route::get('/', 'CustomerHappinessController@overView');

         Route::get('/query', 'CustomerHappinessController@queries');


        Route::get('/transactions', 'CustomerHappinessController@transactions');

        Route::get('/utility', 'CustomerHappinessController@Utility');

        Route::get('/status/{status}', 'CustomerHappinessController@sortByStatus');





        Route::get('/users', 'CustomerHappinessController@users');

        Route::get('/transaction/{id}', 'CustomerHappinessController@transPerUser');

      ;
    });

});
