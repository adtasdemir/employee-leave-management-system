<?php

namespace App\Exceptions;

use Throwable;
use App\Http\Resources\ErrorResource;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /*
    This code snippet defines a method render in a custom exception handler class. 
    It takes an HTTP request and a throwable exception as input, and returns an HTTP response.

    If the request expects a JSON response, it creates an ErrorResource instance with the exception, 
    sets the HTTP status code based on the exception type, and returns the response. 
    Otherwise, it delegates the rendering to the parent class.
    */

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Illuminate\Http\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception): \Illuminate\Http\Response
    {
        if ($request->expectsJson()) {
            $statusCode = $exception instanceof HttpException
                ? $exception->getStatusCode()
                : ($exception->getCode() ?: 500);

            return (new ErrorResource($exception))
                ->response()
                ->setStatusCode($statusCode);
        }

        return parent::render($request, $exception);
    }
}
