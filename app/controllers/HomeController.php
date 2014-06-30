<?php namespace eTrack\Controllers;

use App;
use Auth;
use Redirect;
use View;

class HomeController extends BaseController {

	public function index()
	{
		if (! Auth::check())
            return Redirect::route('login');

        switch (Auth::user()->role)
        {
            case 'Admin':
                return View::make('home.admin', ['fullName' => Auth::user()->full_name]);
        }

        App::abort(500);
	}

}
