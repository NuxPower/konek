<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * Report or log an exception.
     */
    public function report(Throwable $exception): void
    {
        // Add custom reporting here (e.g., Sentry, Bugsnag)
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            $status = 500;
            $message = 'Server Error';
            if ($exception instanceof ModelNotFoundException) {
                $status = 404;
                $message = 'Resource not found.';
            } elseif ($exception instanceof AuthorizationException) {
                $status = 403;
                $message = 'This action is unauthorized.';
            } elseif ($exception instanceof AuthenticationException) {
                $status = 401;
                $message = 'Unauthenticated.';
            } elseif ($exception instanceof HttpException) {
                $status = $exception->getStatusCode();
                $message = $exception->getMessage() ?: $message;
            }
            return response()->json([
                'message' => $message,
            ], $status);
        }

        // Custom error views for web
        if ($exception instanceof ModelNotFoundException) {
            return response()->view('errors.404', [], 404);
        }
        if ($exception instanceof AuthorizationException) {
            return response()->view('errors.403', ['message' => $exception->getMessage()], 403);
        }
        if ($exception instanceof HttpException && $exception->getStatusCode() === 500) {
            return response()->view('errors.500', [], 500);
        }

        return parent::render($request, $exception);
    }
} 