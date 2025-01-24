<?php

use Illuminate\Http\Request;
use App\Http\Resources\ErrorResource;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Global exception handling for API requests
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                $statusCode = $e instanceof HttpException
                ? $e->getStatusCode()
                : ($e->getCode() ?: 500);

                // Use ErrorResource to format the response
                return (new ErrorResource($e))
                    ->response()
                    ->setStatusCode($statusCode);
                }
            return $e->render($request);
        });
    })
    ->create();
