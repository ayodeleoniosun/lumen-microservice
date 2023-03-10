<?php

namespace App\Services;

use App\Contracts\CommentServiceInterface;
use App\Traits\ConsumeExternalServiceTrait;
use GuzzleHttp\Exception\GuzzleException;

class CommentService implements CommentServiceInterface
{
    use ConsumeExternalServiceTrait;

    private string $baseUrl;

    private string $secret;

    public function __construct()
    {
        $this->baseUrl = config('services.comments.base_url');
        $this->secret = config('services.comments.secret');
    }

    /**
     * @throws GuzzleException
     */
    public function index(int $post): string
    {
        return $this->sendRequest('GET', "/comments/posts/{$post}");
    }

    /**
     * @throws GuzzleException
     */
    public function store(array $data): string
    {
        return $this->sendRequest('POST', '/comments', $data);
    }

    /**
     * @throws GuzzleException
     */
    public function show(int $comments): string
    {
        return $this->sendRequest('GET', "/comments/{$comments}");
    }

    /**
     * @throws GuzzleException
     */
    public function update(array $data, int $id): string
    {
        return $this->sendRequest('PUT', "/comments/{$id}", $data);
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    public function like(array $data, int $id): string
    {
        return $this->sendRequest('POST', "/comments/{$id}/like", $data);
    }

    /**
     * @throws GuzzleException
     */
    public function delete(int $id): void
    {
        $this->sendRequest('GET', "/comments/delete/{$id}");
    }
}
