<?php

namespace App\Services;

use App\Contracts\BookServiceInterface;
use App\Traits\ConsumeExternalServiceTrait;
use GuzzleHttp\Exception\GuzzleException;

class BookService implements BookServiceInterface
{
    use ConsumeExternalServiceTrait;

    public string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.books.base_url');
    }

    /**
     * @throws GuzzleException
     */
    public function index(): string
    {
        return $this->sendRequest('GET', '/books');
    }

    /**
     * @throws GuzzleException
     */
    public function create(array $data): string
    {
        return $this->sendRequest('POST', '/books', $data);
    }

    /**
     * @throws GuzzleException
     */
    public function show(int $books): string
    {
        return $this->sendRequest('GET', "/books/{$books}");
    }

    /**
     * @throws GuzzleException
     */
    public function update(array $data, int $id): string
    {
        return $this->sendRequest('PUT', "/books/{$id}", $data);
    }

    /**
     * @throws GuzzleException
     */
    public function delete(int $id): void
    {
        $this->sendRequest('DELETE', "/books/{$id}");
    }
}
