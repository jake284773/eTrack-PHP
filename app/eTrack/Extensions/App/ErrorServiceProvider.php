<?php namespace eTrack\Extensions\App;

use Exception;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\ServiceProvider;
use Laracasts\Validation\FormValidationException;

class ErrorServiceProvider extends ServiceProvider {

    /**
     * @var ErrorHandler
     */
    protected $errorHandler;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('errorHandler', function($app)
        {
            return new ErrorHandler($app);
        });
    }

    public function boot()
    {
        $this->registerErrorHandler();
        $this->registerMaintenanceHandler();
        $this->registerTokenMismatchHandler();
        $this->registerMissingHandler();
        $this->registerValidationHandler();

        parent::boot();
    }

    private function registerErrorHandler()
    {
        $app = $this->app;
        $this->app->error(function (Exception $exception, $code) use ($app)
        {
            return $app['errorHandler']->handleException($exception, $code);
        });
    }

    private function registerTokenMismatchHandler()
    {
        $app = $this->app;
        $this->app->error(function (TokenMismatchException $exception) use ($app)
        {
            return $app['errorHandler']->handleTokenMismatch($exception);
        });
    }

    private function registerMaintenanceHandler()
    {
        $app = $this->app;
        $this->app->down(function () use ($app)
        {
            return $app['errorHandler']->handleMaintenance();
        });
    }

    private function registerMissingHandler()
    {
        $app = $this->app;
        $this->app->missing(function(Exception $exception) use ($app)
        {
            return $app['errorHandler']->handleMissing($exception);
        });
    }

    private function registerValidationHandler()
    {
        $app = $this->app;
        $this->app->error(function(FormValidationException $exception) use($app)
        {
            return $app['errorHandler']->handleValidation($exception);
        });
    }

}