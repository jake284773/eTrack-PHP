<?php namespace eTrack\Controllers\Admin;

use BaseController;
use User;
use View;

class UserController extends BaseController {

    public function index()
    {
        $users = User::paginate(15);

        return View::make('admin.users.index', array('users' => $users));
    }

    public function create()
    {
        return View::make('admin.users.create');
    }

}
