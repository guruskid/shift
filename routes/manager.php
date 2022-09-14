<?php
Route::group(['middleware' => ['auth:api', 'verified']], function () {


    Route::group(['prefix' => 'manager'], function () {
         //FAQs
        Route::get('getall', 'FaqController@getAll');
        Route::post('addfaqcategory', 'FaqController@addFaqCategory');
        Route::get('viewfaqcategory', 'FaqController@viewCategory');
        Route::post('deletefaqcategory/{id}', 'FaqController@deleteFaqCategory');
        Route::post('addfaq', 'FaqController@addNewFaq');
        Route::post('deletefaq/{id}', 'FaqController@deleteAFaq');
        Route::post('updatefaq', 'FaqController@updateAFaq');

    });

});
