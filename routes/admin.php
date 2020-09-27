<?php

Route::group(['prefix'=>'currency'], function(){

    Route::get('/index', 'CurrencyController@index')->name('admin.currency.index');
    Route::get('/create', 'CurrencyController@create')->name('admin.currency.create');
    Route::post('/store/{currency}', 'CurrencyController@store')->name('admin.currency.store');
});