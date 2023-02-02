<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param Throwable $exception
     * @return Response|JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof HttpException) {
            $code = $exception->getStatusCode();
            $message = Response::$statusTexts[$code];

            return $this->error($message, $code);
        }

        if ($exception instanceof ModelNotFoundException) {
            $model = ucfirst(class_basename($exception->getModel()));

            return $this->error($model . ' not found', Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof AuthorizationException) {
            return $this->error($exception->getMessage(), Response::HTTP_FORBIDDEN);
        }

        if ($exception instanceof AuthenticationException) {
            return $this->error($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        if ($exception instanceof ValidationException) {
            return $this->error($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (env('APP_DEBUG')) {
            return parent::render($request, $exception);
        }

        //custom exceptions
        if ($exception instanceof InvalidLoginCredentialsException) {
            return $this->error('Invalid login credentials', Response::HTTP_UNAUTHORIZED);
        }

        return $this->error("Something went wrong. Try again later.", Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
