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
        Route::post('sortfaq/{category}', 'FaqController@sortFaq');

         //Announcement

         Route::post('addannouncement', 'AnnouncementController@addNew');
         Route::get('allannouncement', 'AnnouncementController@getAllAnnouncement');
         Route::post('updateannouncement/{id}', 'AnnouncementController@update');
         Route::post('changestatus/{id}/{status}', 'AnnouncementController@updateStatus');
         Route::post('deleteannouncement/{id}', 'AnnouncementController@deleteAnnouncement');


         //Verification Section

         Route::get('usersdetails', 'VerificationController@viewAll');
         Route::get('usersfigures', 'VerificationController@usersStages');
         Route::post('limitupdate/{id}', 'VerificationController@verifyUser');



    });

});
