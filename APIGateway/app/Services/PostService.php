<?php

namespace App\Services;

use App\Contracts\PostServiceInterface;
use App\Traits\ConsumeExternalServiceTrait;
use GuzzleHttp\Exception\GuzzleException;

class PostService implements PostServiceInterface
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
        $this->baseUrl = config('services.posts.base_url');
        $this->secret = config('services.posts.secret');
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    public function index(): string
    {
        return $this->sendRequest('GET', '/posts');
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    public function create(array $data): string
    {
        return $this->sendRequest('POST', '/posts', $data);
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    public function show(int $post): string
    {
        return $this->sendRequest('GET', "/posts/{$post}");
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    public function update(array $data, int $id): string
    {
        return $this->sendRequest('PUT', "/posts/{$id}", $data);
    }

    /**
     * @return void
     * @throws GuzzleException
     */
    public function delete(int $id): void
    {
        $this->sendRequest('DELETE', "/posts/{$id}");
    }

    public function like(int $user, int $id): string
    {
        // TODO: Implement like() method.
    }
}
