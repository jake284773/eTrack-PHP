<?php

class UserController extends BaseController {

    protected $layout = 'layouts.article';

    public function login()
    {
        $this->layout->title = 'Login';
        $this->layout->content = View::make('user.login');
    }

    public function auth()
    {
        $credentials = Input::only(array('username', 'password'));
        $authAttempt = Auth::attempt($credentials);

        if ($authAttempt)
        {
            return Redirect::intended();
        }
        else
        {
            $error = "You've entered the wrong username or password. " .
                     "Please check your details and try again.";
            return Redirect::back()->with('error', $error);
        }
    }
}