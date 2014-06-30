<?php namespace eTrack\Tests;

use Artisan;

abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase {

	protected $controllerNamespacePrefix = 'eTrack\Controllers\\';

    /**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;

		$testEnvironment = 'testing';

		return require __DIR__.'/../../bootstrap/start.php';
	}

    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate', ['seed']);
    }

}
