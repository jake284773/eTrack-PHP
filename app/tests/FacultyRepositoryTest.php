<?php namespace eTrack\Tests\Models\Repositories;


use App;
use eTrack\Models\Repositories\FacultyRepository;
use eTrack\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Mockery;

class FacultyRepositoryTest extends TestCase {

    protected $faculty;

    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->faculty = Mockery::mock('eTrack\Models\Entities\Faculty[findOrFail]');
        App::instance('eTrack\Models\Entities\Faculty', $this->faculty);
        $this->repository = new FacultyRepository($this->faculty);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testFind()
    {
        $this->faculty->id = 'CCDI';
        $this->faculty->name = 'Creative Cultural and Digital Industries';

        $this->faculty->shouldReceive('findOrFail')->once()
            ->with('CCDI', ['*'])
            ->andReturn($this->faculty);

        $faculty = $this->repository->find('CCDI');

        $this->assertInstanceOf('\eTrack\Models\Entities\Faculty',
            $faculty);
        $this->assertEquals('CCDI',
            $faculty->id);
        $this->assertEquals('Creative Cultural and Digital Industries',
            $faculty->name);
    }

    /**
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testFindFail()
    {
        $this->faculty->id = 'CCDI';
        $this->faculty->name = 'Creative Cultural and Digital Industries';

        $this->faculty->shouldReceive('findOrFail')->once()
            ->with('CCDI2', ['*'])
            ->andThrow('\Illuminate\Database\Eloquent\ModelNotFoundException');

        $this->repository->find('CCDI2');
    }
} 