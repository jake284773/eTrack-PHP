<?php

class UserController extends BaseController {

    public function login()
    {
        return View::make('user.login');
    }

    public function authenticate()
    {
        $credentials = array(
            'id' => Input::get('userid'),
            'password' => Input::get('password')
        );

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