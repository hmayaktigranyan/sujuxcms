<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the Closure to execute when that URI is requested.
  |
 */

Route::get('/', function() {
    return View::make('hello');
});


Route::post('user/login', 'UserController@postLogin');

# User RESTful Routes (Login, Logout, Register, etc)
Route::controller('user', 'UserController');

Route::group(array('prefix' => 'admin', 'before' => 'auth'), function() {
    Route::get('profile', array('uses' => 'AdminController@profile'));
    Route::any('profileupdate', array('uses' => 'AdminController@profileUpdate'));
    
    
    Route::resource('entity', 'AdminEntityController');
    Route::get('entity/{id}/delete', array('uses' => 'AdminEntityController@confirmDestroy'));
    Route::any('entity/fields/{id}', array('uses' => 'AdminEntityController@fields'));
    Route::any('entity/fieldsupdate/{id}', array('uses' => 'AdminEntityController@fieldsUpdate'));
    Route::any('entity/fieldsorder/{id}', array('uses' => 'AdminEntityController@fieldsOrder'));
    Route::any('entity/fieldsorderupdate/{id}', array('uses' => 'AdminEntityController@fieldsOrderUpdate'));
    Route::any('entity/fields/{id}', array('uses' => 'AdminEntityController@fields'));
    Route::any('entity/fieldsdetails/{id}', array('uses' => 'AdminEntityController@fieldsDetails'));
    Route::any('entity/fieldsdetailsupdate/{id}', array('uses' => 'AdminEntityController@fieldsDetailsUpdate'));
   
    Route::resource('language', 'AdminLanguageController');
    Route::get('language/{id}/delete', array('uses' => 'AdminLanguageController@confirmDestroy'));

    Route::resource('taxonomy', 'AdminTaxonomyController');
    Route::get('taxonomy/{id}/delete', array('uses' => 'AdminTaxonomyController@confirmDestroy'));
    Route::any('taxonomy/terms/{id}', array('uses' => 'AdminTaxonomyController@terms'));
    Route::any('taxonomy/termsupdate/{id}', array('uses' => 'AdminTaxonomyController@termsUpdate'));
    Route::any('taxonomy/termsorder/{id}', array('uses' => 'AdminTaxonomyController@termsOrder'));
    Route::any('taxonomy/termsorderupdate/{id}', array('uses' => 'AdminTaxonomyController@termsOrderUpdate'));

    Route::any('object/create/{id}', array('uses' => 'AdminObjectController@create'));
    Route::any('object/store/{id}', array('uses' => 'AdminObjectController@store'));
    Route::any('object/{id}', array('uses' => 'AdminObjectController@index'));
    Route::any('object/show/{id}', array('uses' => 'AdminObjectController@show'));
    Route::any('object/edit/{id}', array('uses' => 'AdminObjectController@edit'));
    Route::any('object/update/{id}', array('uses' => 'AdminObjectController@update'));
    Route::any('object/delete/{id}', array('uses' => 'AdminObjectController@confirmDestroy'));
    Route::any('object/destroy/{id}', array('uses' => 'AdminObjectController@destroy'));
    Route::any('object/translate/{id}/{language}', array('uses' => 'AdminObjectController@translate'));


    # Admin Dashboard
    Route::controller('/', 'AdminDashboardController');
});
