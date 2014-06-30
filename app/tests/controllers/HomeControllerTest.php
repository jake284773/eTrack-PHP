<?php namespace eTrack\Tests\Controllers;

use eTrack\Models\Entities\User;
use eTrack\Tests\TestCase;

class HomeControllerTest extends TestCase {

    /**
     * Test that an unauthorised user gets redirected to the login page when
     * they try to access the home page.
     */
    public function testGuestRedirectToLogin()
    {
        $this->action('GET', 'eTrack\Controllers\HomeController@index');

        $this->assertRedirectedTo('user/login');
    }

    /**
     * Test that an authenticated admin user is shown the admin home page.
     */
    public function testHomeAdminUser()
    {
        $user = new User();
        $user->id = "admin";
        $user->full_name = "Administrator";
        $user->role = "Admin";
        $this->be($user);

        $this->action('GET', 'eTrack\Controllers\HomeController@index');
        $this->assertResponseOk();
        $this->assertViewHas('fullName', $user->full_name);
    }

    /**
     * Test that the applications throws an error if a user logs in with an
     * invalid user role.
     *
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testErrorWithInvalidRole()
    {
        $user = new User();
        $user->id = "test1";
        $user->full_name = "Test User";
        $user->role = "Unknown";
        $this->be($user);

        $this->action('GET', 'eTrack\Controllers\HomeController@index');
    }

}