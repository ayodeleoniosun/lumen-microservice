<?php

namespace App\Services;

use App\Contracts\PostServiceInterface;
use App\Traits\ConsumeExternalServiceTrait;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;

class PostService implements PostServiceInterface
{
    use ConsumeExternalServiceTrait;

    private string $baseUrl;

    private string $secret;

    /**
     */
    public function __construct()
    {
        $this->baseUrl = config('services.posts.base_url');
        $this->secret = config('services.posts.secret');
    }

    /**
     * @return Collection
     * @throws GuzzleException
     */
    public function index(): string
    {
        return $this->sendRequest('GET', '/posts');
    }

    /**
     * @param array $data
     * @return string
     * @throws GuzzleException
     */
    public function store(array $data): string
    {
        return $this->sendRequest('POST', '/posts', $data);
    }

    /**
     * @param int $post
     * @return string
     * @throws GuzzleException
     */
    public function show(int $post): string
    {
        return $this->sendRequest('GET', "/posts/{$post}");
    }

    /**
     * @param array $data
     * @param int $id
     * @return string
     * @throws GuzzleException
     */
    public function update(array $data, int $id): string
    {
        return $this->sendRequest('PUT', "/posts/{$id}", $data);
    }

    /**
     * @param array $data
     * @param int $id
     * @return string
     * @throws GuzzleException
     */
    public function like(array $data, int $id): string
    {
        return $this->sendRequest('POST', "/posts/{$id}/like", $data);
    }

    /**
     * @param int $id
     * @return void
     * @throws GuzzleException
     */
    public function delete(int $id): void
    {
        $this->sendRequest('GET', "/posts/delete/{$id}");
    }
}
