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
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
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

    /**
     * @param $request
     * @param Throwable $e
     * @return Response|JsonResponse|RedirectResponse|ResponseAlias
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response|JsonResponse|RedirectResponse|ResponseAlias
    {
        if ($request->expectsJson()) {
            if ($e instanceof RouteNotFoundException) {
                return $this->respondError(
                    error: $e->getMessage(),
                    statusCode: ResponseAlias::HTTP_NOT_FOUND
                );
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return $this->respondError(
                    error: $e->getMessage(),
                    statusCode: ResponseAlias::HTTP_METHOD_NOT_ALLOWED
                );
            }

            if ($e instanceof ThrottleRequestsException) {
                return $this->respondError(
                    error: $e->getMessage(),
                    statusCode: 429,
                );
            }

            if ($e instanceof ModelNotFoundException) {
                return $this->respondError(
                    error: __('errors.not-found', [
                        'attribute' => str_replace('App\\Models\\', '', $e->getModel())
                    ]),
                    statusCode: 404,
                );
            }

            if ($e instanceof ValidationException) {
                return $this->respondError(
                    error: __('errors.validation'),
                    statusCode: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                    errors: $e->errors(),
                );
            }
            if ($e instanceof QueryException) {
                return $this->respondError(
                    error: __('errors.query'),
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
            if ($e instanceof TransportException) {
                return $this->respondError(
                    error: __('errors.email-server'),
                    statusCode: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            return $this->respondError(
                error: __('errors.server'),
                statusCode: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                trace: $this->parseException($e)
            );
        }

        return parent::render($request, $e);
    }

    /**
     * Parse Exception to return exception trace if environment is not production
     *
     * @param $exception
     * @return array
     */
    protected function parseException($exception): array {

        if (config('app.env') !== 'production') {
            return [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'code' => $exception->getCode(),
                'trace' => $exception?->getTrace(),
            ];
        }

        return [];
    }
}
