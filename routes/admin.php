<?php

Route::group(['middleware'=>'manager'], function(){
    Route::get('/rates', 'RateController@index')->name('admin.rates');
    /* Route::post('/', 'RateController@index' )->name('admin.add_rate'); */


    Route::get('/index', 'CurrencyController@index')->name('admin.currency.index');
    Route::get('/create', 'CurrencyController@create')->name('admin.currency.create');
    Route::post('/store/{currency}', 'CurrencyController@store')->name('admin.currency.store');
});
