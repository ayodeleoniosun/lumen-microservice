<?php

namespace App\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as FacadeResponse;

trait ApiResponseTrait
{
    public function success($data, int $statusCode = Response::HTTP_OK)
    {
        return FacadeResponse::json([
            'status' => 'success',
            'data' => $data,
        ], $statusCode);
    }

    public function error($data, int $statusCode = Response::HTTP_BAD_REQUEST)
    {
        return FacadeResponse::json([
            'status' => 'error',
            'message' => $data['message'],
        ], $statusCode);
    }
}
