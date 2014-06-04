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

Route::get('user/login', array('as' => 'login', 'uses' => 'UserController@login'));
Route::post('user/login', array('as' => 'auth', 'uses' => 'UserController@authenticate'));
Route::get('user/logout', array('as' => 'logout', 'uses' => 'UserController@logout'));

Route::group(array('before' => 'auth'), function ()
{
    Route::get('/', array('as' => 'home', 'uses' => 'HomeController@index'));
//    Route::resource('profile', 'ProfileController', array('only' => array('index', 'store')));
});

Route::group(array('before' => 'auth|admin', 'prefix' => 'admin', 'namespace' => 'eTrack\Controllers\Admin'), function ()
{
    Route::resource('users', 'UserController');
    Route::get('users/delete/{id}', array('as' => 'admin.users.delete_confirm', 'uses' => 'UserController@deleteConfirm'));
});


