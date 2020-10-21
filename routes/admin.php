<?php

Route::group(['middleware'=>'manager'], function(){
    Route::get('/rates', 'RateController@index')->name('admin.rates');
    Route::post('/rate/create', 'RateController@store')->name('admin.rate.add');

    Route::post('/card/create', 'CardController@store')->name('admin.card.create');
    Route::post('/edit-card', 'CardController@editCard' )->name('admin.card.edit');

    Route::post('/currency/store', 'CurrencyController@store')->name('admin.currency.store');

    Route::post('/card-type/store', 'PaymentMediumController@store')->name('admin.card-type.store');





    Route::get('/index', 'CurrencyController@index')->name('admin.currency.index');
});
