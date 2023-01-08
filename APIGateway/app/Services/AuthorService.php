<?php

namespace App\Services;

use App\Contracts\AuthorServiceInterface;
use App\Traits\ConsumeExternalServiceTrait;
use GuzzleHttp\Exception\GuzzleException;

class AuthorService implements AuthorServiceInterface
{
    use ConsumeExternalServiceTrait;

    private string $baseUrl;

    private string $secret;

    public function __construct()
    {
        $this->baseUrl = config('services.authors.base_url');
        $this->secret = config('services.authors.secret');
    }


    /**
     * @return string
     * @throws GuzzleException
     */
    public function index(): string
    {
        return $this->sendRequest('GET', '/authors');
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    public function create(array $data): string
    {
        return $this->sendRequest('POST', '/authors', $data);
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    public function show(int $author): string
    {
        return $this->sendRequest('GET', "/authors/{$author}");
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    public function update(array $data, int $id): string
    {
        return $this->sendRequest('PUT', "/authors/{$id}", $data);
    }

    /**
     * @return void
     * @throws GuzzleException
     */
    public function delete(int $id): void
    {
        $this->sendRequest('DELETE', "/authors/{$id}");
    }
}
