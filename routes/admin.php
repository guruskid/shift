<?php

Route::group(['middleware'=>'manager'], function(){
    Route::get('/rates', 'RateController@index')->name('admin.rates');
    Route::post('/card-combination/create', 'RateController@store')->name('admin.rate.add');

    Route::post('/card/create', 'CardController@store')->name('admin.card.create');
    Route::post('/edit-card', 'CardController@editCard' )->name('admin.card.edit');

    Route::post('/currency/store', 'CurrencyController@store')->name('admin.currency.store');

    Route::post('/card-type/store', 'PaymentMediumController@store')->name('admin.card-type.store');

    Route::post('/rate/update', 'RateController@update')->name('admin.rate.update');



    Route::get('/index', 'CurrencyController@index')->name('admin.currency.index');
});
