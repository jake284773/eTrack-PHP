<?php

use eTrack\Forms\LoginForm;

class UserController extends BaseController {

    protected $layout = 'layouts.article';

    protected $loginForm;

    public function __construct(LoginForm $loginForm)
    {
        $this->loginForm = $loginForm;
    }

    public function login()
    {
        $this->layout->title = 'Login';
        $this->layout->content = View::make('user.login');
    }

    public function auth()
    {
        $credentials = Input::only(array('username', 'password'));
        $this->loginForm->validate($credentials);
        $this->loginForm->authenticate($credentials);
        return Redirect::intended();
    }
}