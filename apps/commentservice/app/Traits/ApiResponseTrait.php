<?php

namespace App\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as FacadeResponse;

trait ApiResponseTrait
{
    public function deleted(int $statusCode = Response::HTTP_NO_CONTENT)
    {
        return FacadeResponse::json([], $statusCode);
    }

    public function error(string $message, int $statusCode = Response::HTTP_BAD_REQUEST)
    {
        return FacadeResponse::json([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
}
