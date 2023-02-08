<?php

namespace App\Services;

use App\Contracts\UserServiceInterface;
use App\Traits\ConsumeExternalServiceTrait;
use GuzzleHttp\Exception\GuzzleException;

class UserService implements UserServiceInterface
{
    use ConsumeExternalServiceTrait;

    private string $baseUrl;

    private string $secret;

    /**
     */
    public function __construct()
    {
        $this->baseUrl = config('services.users.base_url');
        $this->secret = config('services.users.secret');
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    public function index(): string
    {
        return $this->sendRequest('GET', '/users');
    }

    /**
     * @param array $data
     * @return string
     * @throws GuzzleException
     */
    public function register(array $data): string
    {
        return $this->sendRequest('POST', '/register', $data);
    }

    /**
     * @param array $data
     * @return string
     * @throws GuzzleException
     */
    public function login(array $data): string
    {
        return $this->sendRequest('POST', '/login', $data);
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    public function show(int $user): string
    {
        return $this->sendRequest('GET', "/users/{$user}");
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    public function update(array $data, int $id): string
    {
        return $this->sendRequest('PUT', "/users/{$id}", $data);
    }
}
