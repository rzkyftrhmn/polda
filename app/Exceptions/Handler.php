<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            if ($e instanceof ValidationException) {
                return response()->json(format_error('Validation error', $e->errors()), 422);
            }

            if ($e instanceof AuthenticationException) {
                return response()->json(format_error('Unauthenticated'), 401);
            }

            if ($e instanceof AuthorizationException) {
                return response()->json(format_error('Forbidden'), 403);
            }

            if ($e instanceof ModelNotFoundException) {
                return response()->json(format_error('Resource not found'), 404);
            }

            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
                $message = $e->getMessage() ?: match ($status) {
                    404 => 'Not Found',
                    405 => 'Method Not Allowed',
                    429 => 'Too Many Requests',
                    500 => 'Internal Server Error',
                    default => 'Error',
                };
                return response()->json(format_error($message), $status);
            }

            $message = config('app.debug') ? ($e->getMessage() ?: 'Internal Server Error') : 'Internal Server Error';
            return response()->json(format_error($message), 500);
        }

        return parent::render($request, $e);
    }
}
