<?php

class HomeController extends BaseController {

	public function index()
	{
		switch (Auth::user()->role)
        {
            case 'Admin':
                return View::make('home.admin');
            default:
                App::abort(404);
        }
	}

}
