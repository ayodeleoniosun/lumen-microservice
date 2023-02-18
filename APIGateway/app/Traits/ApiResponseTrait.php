<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

trait ApiResponseTrait
{
    public function success($data, string $message = '', int $statusCode = Response::HTTP_OK)
    {
        return FacadeResponse::json([
            'status' => 'success',
            'message' => $message,
            'data' => json_decode($data),
        ], $statusCode);
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

    public function errorMessage($data, int $statusCode = ResponseAlias::HTTP_BAD_REQUEST): JsonResponse
    {
        return FacadeResponse::json($data, $statusCode);
    }
}
