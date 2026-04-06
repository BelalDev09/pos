<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register any exception handling callbacks for the application.
     */
    public function register(): void
    {
        //
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Check if exception is HttpException
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            $status = $exception->getStatusCode();

            switch ($status) {
                case 403:
                    return response()->view('backend.layout.errors.custom-403', [], 403);
                case 404:
                    return response()->view('backend.layout.errors.custom-404', [], 404);
                case 500:
                    return response()->view('backend.layout.errors.custom-500', [], 500);
                case 503:
                    return response()->view('backend.layout.errors.offline', [], 503);
            }
        }

        // Fallback: use Laravel default
        return parent::render($request, $exception);
    }
}
