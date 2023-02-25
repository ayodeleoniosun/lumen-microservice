<?php

namespace App\Contracts;

interface OauthServiceInterface
{
    public function generateToken($data): mixed;

    public function sendRequest($method, $requestUrl, array $params = [], array $headers = []): string;
}
