<?php

Route::group(['middleware' => ['auth:api', 'verified']], function () {

    //TRANSACTIONS
    Route::group(['prefix' => 'customerhappiness'], function () {
         Route::get('/', 'CustomerHappinessController@overView');

         Route::get('/query', 'CustomerHappinessController@queries');


        Route::get('/transactions', 'CustomerHappinessController@transactions');

        Route::get('/utility', 'CustomerHappinessController@Utility');

        Route::get('/status/{status}', 'CustomerHappinessController@sortByStatus');

        Route::get('/filter/{type}', 'CustomerHappinessController@filterByType');

        Route::get('/filterutility/{type}', 'CustomerHappinessController@filterUtility');






        Route::get('/users', 'CustomerHappinessController@getUsers');

        Route::get('/transaction/{id}', 'CustomerHappinessController@transPerUser');

      ;
    });

});
