<?php namespace eTrack\Extensions\App;

use Config;
use Exception;
use Illuminate\Session\TokenMismatchException;
use Laracasts\Validation\FormValidationException;
use Log;
use Redirect;
use Response;
use View;

class ErrorHandler {

    public function handleException(Exception $exception, $code = 500)
    {
        Log::error($exception);

        if (! Config::get('app.debug'))
        {
            return $this->errorPage("eTrack has experienced an error", $code);
        }

        return null;
    }

    public function handleTokenMismatch(TokenMismatchException $exception)
    {
        Log::warning($exception);
        return $this->errorPage("Your browser sent an invalid request", 400);
    }

    public function handleMissing(Exception $exception)
    {
        Log::warning($exception);
        return $this->errorPage("eTrack can't find that page", 404);
    }

    public function handleMaintenance()
    {
        return $this->errorPage("eTrack is currently being updated", 503);
    }

    public function handleValidationFailure(FormValidationException $exception)
    {
        return Redirect::back()->withInput()->withErrors($exception->getErrors());
    }

    /**
     * Render an error page with the specified title and HTTP status code
     *
     * @param string $title Page title
     * @param integer $code HTTP status code
     * @return \Illuminate\Http\Response
     */
    public function errorPage($title, $code)
    {
        $layout = View::make('layouts.article');
        $layout->title = $title;
        $layout->content = View::make('errors.' . $code);
        return Response::make($layout, $code);
    }

}