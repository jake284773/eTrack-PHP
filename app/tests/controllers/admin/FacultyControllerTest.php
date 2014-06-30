<?php namespace eTrack\Tests\Controllers\Admin;

use eTrack\Tests\TestCase;

class FacultyControllerTest extends TestCase {

    public function testIndex()
    {
        $this->client->request('GET', '/admin/faculties');

        $this->assertResponseOk();
    }

}

