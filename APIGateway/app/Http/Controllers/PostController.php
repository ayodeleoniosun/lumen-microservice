<?php

namespace App\Http\Controllers;

use App\Contracts\PostServiceInterface;
use App\Contracts\AuthServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    use ApiResponseTrait;

    public PostServiceInterface $postService;

    public AuthServiceInterface $userService;

    /**
     * @param PostServiceInterface $postService
     */
    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Return list of posts
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->success($this->postService->index());
    }

    /**
     * Create new post
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $payload = $this->attachUserToPayload($request);

        $response = $this->postService->store($payload);

        return $this->success($response, 'Post successfully created', Response::HTTP_CREATED);
    }

    /**
     * get and show details of existing post
     *
     * @param $post
     * @return JsonResponse
     */
    public function show($post): JsonResponse
    {
        $response = $this->postService->show($post);

        return $this->success($response);
    }

    /**
     * Update an existing post
     *
     * @param Request $request
     * @param $post
     * @return JsonResponse
     */
    public function update(Request $request, $post): JsonResponse
    {
        $payload = $this->attachUserToPayload($request);

        $response = $this->postService->update($payload, $post);

        return $this->success($response, 'Post successfully updated');
    }

    /**
     * like existing post
     *
     * @param Request $request
     * @param int $post
     * @return JsonResponse
     */
    public function like(Request $request, int $post): JsonResponse
    {
        $payload = $this->attachUserToPayload($request);

        $response = $this->postService->like($payload, $post);

        return $this->success($response, 'Post liked');
    }

    /**
     * Remove an existing post
     *
     * @param $post
     * @return JsonResponse
     */
    public function destroy($post): JsonResponse
    {
        $getPost = json_decode($this->postService->show($post));

        if ($getPost->user_id !== auth()->user()->id) {
            return $this->error('You are not authorized to delete this post', Response::HTTP_FORBIDDEN);
        }

        $this->postService->delete($post);

        return $this->deleted();
    }

    protected function attachUserToPayload(Request $request): array {
        $userId = auth()->user()->id;
        $payload = $request->all();
        $payload['user_id'] = $userId;

        return $payload;
    }
}
