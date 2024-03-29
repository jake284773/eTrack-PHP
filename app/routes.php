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

Route::get('/', array('before' => 'auth', 'as' => 'home', 'uses' => 'HomeController@index'));

Route::get('user/login', array('before' => 'guest', 'as' => 'user.login', 'uses' => 'UserController@login'));
Route::post('user/login', array('before' => 'guest|csrf', 'as' => 'user.auth', 'uses' => 'UserController@auth'));
