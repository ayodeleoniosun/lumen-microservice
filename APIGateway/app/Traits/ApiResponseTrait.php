<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Laravel\Lumen\Http\ResponseFactory;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

trait ApiResponseTrait
{
    public function success($data, int $statusCode = ResponseAlias::HTTP_OK): Response|ResponseFactory
    {
        return response($data, $statusCode)->header('Content-Type', 'application/json');
    }

    public function deleted(int $statusCode = ResponseAlias::HTTP_NO_CONTENT): JsonResponse
    {
        return FacadeResponse::json([], $statusCode);
    }

    public function error(string $message, int $statusCode = ResponseAlias::HTTP_BAD_REQUEST): JsonResponse
    {
        return FacadeResponse::json([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
}
