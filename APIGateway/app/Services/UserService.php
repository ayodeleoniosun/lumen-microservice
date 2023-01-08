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
     * @param string $baseUrl
     * @param string $secret
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
     * @return string
     * @throws GuzzleException
     */
    public function create(array $data): string
    {
        return $this->sendRequest('POST', '/users', $data);
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

    /**
     * @return void
     * @throws GuzzleException
     */
    public function delete(int $id): void
    {
        $this->sendRequest('DELETE', "/users/{$id}");
    }
}
