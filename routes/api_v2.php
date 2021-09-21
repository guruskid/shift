<?php

use Illuminate\Http\Request;


//Register and login here and other routes that dont require authentication


Route::group(['middleware' => 'auth:api'], function () {

    //Authenticated routes here

});
