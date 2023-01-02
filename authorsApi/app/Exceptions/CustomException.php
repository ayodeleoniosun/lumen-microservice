<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

class CustomException extends Exception
{
    use ApiResponseTrait;

    protected $message;

    protected int $statusCode;

    public function __construct(string $message, int $statusCode = 400)
    {
        parent::__construct();
        $this->message = $message;
        $this->statusCode = $statusCode;
    }

    public function render(): JsonResponse
    {
        return $this->error($this->message, $this->statusCode);
    }
}
