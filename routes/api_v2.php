<?php

use Illuminate\Http\Request;


//Register and login here and other routes that dont require authentication

// Route::get('test-route', 'testController@index');
Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');


Route::group(['middleware' => 'auth:api'], function () {

    //Authenticated routes here

});
