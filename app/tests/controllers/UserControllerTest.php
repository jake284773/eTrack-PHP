<?php namespace eTrack\Tests\Controllers;

use DB;
use eTrack\Tests\TestCase;
use Hash;

class UserControllerTest extends TestCase {

    public function testLoginView()
    {
        $this->client->request('GET', '/user/login');

        $this->assertResponseOk();
    }

    public function testAuthenticate()
    {
        $this->addTestUser();

        $crawler = $this->client->request('GET', '/user/login');

        $form = $crawler->selectButton('Log in')->form();

        $form->setValues([
            'userid' => 'admin',
            'password' => 'admin'
        ]);

        $this->client->submit($form);
        $this->assertRedirectedTo('/');
        $crawler = $this->client->followRedirect(true);
        $this->assertCount(1, $crawler->filter('h1:contains("Welcome Administrator")'));
    }

    public function testInvalidAuthenticate()
    {
        $this->addTestUser();

        $crawler = $this->client->request('GET', '/user/login');

        $form = $crawler->selectButton('Log in')->form();

        $form->setValues([
            'userid' => 'admin2',
            'password' => 'admin22'
        ]);

        $this->client->submit($form);
        $this->assertRedirectedTo('/user/login');
        $this->client->followRedirect(true);
        $this->assertSessionHas('authError');
    }

    public function testLogout()
    {
        $this->testAuthenticate();

        $this->client->request('GET', '/user/logout');
        $this->assertRedirectedTo('/user/login');

        $this->client->request('GET', '/');
        $this->assertRedirectedTo('/user/login');
    }

    private function addTestUser()
    {
        DB::table('user')->insert([
            'id'        => 'admin',
            'password'  => Hash::make('admin'),
            'email'     => 'admin@examplecollege.ac.uk',
            'full_name' => 'Administrator',
            'role'      => 'Admin'
        ]);
    }

}