<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\KwtController;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        switch(true) {
            case $exception instanceof NotFoundHttpException:
                return KwtController::response([], __('Resource does not exist.'), [], 404);
                break;
            case $exception instanceof ModelNotFoundException:
                return KwtController::response([], __('Entry does not exist.'), [], 404);
                break;
            case $exception instanceof AuthenticationException:
                return KwtController::response([], __($exception->getMessage()), [], 401);
                break;
            case method_exists($exception, 'getStatusCode') && $exception->getStatusCode() == 405:
                return KwtController::response([], __('Method not allowed.'), [], $exception->getStatusCode());
                break;
            case $exception instanceof ValidationException:
                return KwtController::response([], __($exception->getMessage()), $exception->errors(), method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 400);
                break;
            default:
                if (in_array(env('APP_ENV'), ['development', 'local'])) {
                    return parent::render($request, $exception);
                }
                return KwtController::response([], __($exception->getMessage()), [], method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500);
                break;
        }
        
        
    }
}
