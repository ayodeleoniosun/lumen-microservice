<?php

namespace App\Services;

use App\Contracts\AuthorServiceInterface;
use App\Traits\ConsumeExternalServiceTrait;
use GuzzleHttp\Exception\GuzzleException;

class AuthorService implements AuthorServiceInterface
{
    use ConsumeExternalServiceTrait;

    public string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.authors.base_url');
    }

    /**
     * @throws GuzzleException
     */
    public function index(): string
    {
        return $this->sendRequest('GET', '/authors');
    }

    /**
     * @throws GuzzleException
     */
    public function create(array $data): string
    {
        return $this->sendRequest('POST', '/authors', $data);
    }

    public function show(int $author): string
    {
        return $this->sendRequest('GET', '/authors/' . $author);
    }

    public function update(array $data, int $id): string
    {
        return $this->sendRequest('PUT', '/authors/' . $id, $data);
    }

    public function delete(int $id): void
    {
        $this->sendRequest('DELETE', '/authors/' . $id);
    }
}
