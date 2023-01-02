<?php

namespace App\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as FacadeResponse;

trait ApiResponseTrait
{
    public function success($data, string $message = '', int $statusCode = Response::HTTP_OK)
    {
        return FacadeResponse::json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function error(string $message, int $statusCode = Response::HTTP_BAD_REQUEST)
    {
        return FacadeResponse::json([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
}
