<?php namespace eTrack\Controllers;

use Auth;
use Input;
use Redirect;
use Session;
use View;

class UserController extends BaseController {

    public function login()
    {
        return View::make('user.login');
    }

    public function authenticate()
    {
        $credentials = [
            'id' => Input::get('userid'),
            'password' => Input::get('password')
        ];

        if (Auth::attempt($credentials)) {
            return Redirect::intended('/');
        }

        return Redirect::back()->with('authError', 'You\'ve entered the wrong ' .
            'user ID or password. Please check your details and try again.');
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();

        return Redirect::route('login');
    }

}