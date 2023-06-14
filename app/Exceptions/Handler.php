<?php

namespace App\Exceptions;

use App\Traits\HasApiResponse;
use Error;
use HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use \Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use HasApiResponse;

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

    public function render($request, Throwable $e): Response|JsonResponse|RedirectResponse|ResponseAlias
    {
        if ($request->expectsJson()) {
            Log::info($e);

            if ($e instanceof MethodNotAllowedHttpException) {
                return $this->respondError(
                    error: $e->getMessage(),
                    statusCode: ResponseAlias::HTTP_METHOD_NOT_ALLOWED
                );
            }

            if ($e instanceof ThrottleRequestsException) {
                return $this->respondError(
                    error: 'Too Many Requests,Please Slow Down',
                    statusCode: 429,
                );
            }

            if ($e instanceof ModelNotFoundException) {
                return $this->respondError(
                    error: str_replace('App\\Models\\', '', $e->getModel()) . ' not found',
                    statusCode: 404,
                );
            }

            if ($e instanceof ValidationException) {
                return $this->respondError(
                    error: "Failed Validation",
                    statusCode: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                    errors: $e->errors(),
                );
            }
            if ($e instanceof QueryException) {
                return $this->respondError(
                    error: 'There was issue with the Query',
                    statusCode: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                    trace: $this->parseException($e)
                );
            }
            if ($e instanceof NotFoundHttpException) {
                return $this->respondError(
                    error: $e->getMessage(),
                    statusCode: ResponseAlias::HTTP_NOT_FOUND
                );
            }

            return $this->respondError(
                error: "Unexpected Exception. Try later",
                statusCode: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                trace: $this->parseException($e)
            );
        }

        return parent::render($request, $e);
    }

    protected function parseException($exception): array {
        if (config('app.env') !== 'production') {
            return [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'code' => $exception->getCode(),
                'trace' => $exception->getTrace(),
            ];
        }

        return [];
    }
}
