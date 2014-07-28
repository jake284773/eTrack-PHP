<?php

class UsersTableSeeder extends Seeder {

	public function run()
	{
		$user = new User();

        $user->username = 'admin';
        $user->password = Hash::make('password');
        $user->email = 'admin@example.com';

        $user->save();
	}

}