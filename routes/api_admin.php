<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['auth:api', 'verified', 'super']], function () {

    //UTILITIES
    Route::group(['prefix' => 'utility-transaction'], function () {
        Route::GET('/airtime',  'UtilityController@airtime');
        Route::POST('/utilities-by-date-search',  'UtilityController@utilitiesSearch');
        Route::GET('/data',  'UtilityController@data');
        Route::GET('/power',  'UtilityController@power');
        Route::GET('/cable',  'UtilityController@cable');
    });
});
