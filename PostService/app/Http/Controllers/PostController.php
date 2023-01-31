<?php

namespace App\Http\Controllers;

use App\Contracts\PostServiceInterface;
use App\Http\Requests\PostRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    use ApiResponseTrait;

    protected PostServiceInterface $postService;

    /**
     * @param PostServiceInterface $postService
     */
    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Return list of posts
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $response = $this->postService->index();

        return $this->success($response);
    }

    /**
     * Create new post
     *
     * @param PostRequest $request
     * @return JsonResponse
     */
    public function store(PostRequest $request): JsonResponse
    {
        $response = $this->postService->create($request->validated());

        return $this->success($response, 'Post successfully created', Response::HTTP_CREATED);
    }

    /**
     * get and show details of existing post
     *
     * @param int $post
     * @return JsonResponse
     */
    public function show(int $post): JsonResponse
    {
        $response = $this->postService->show($post);

        return $this->success($response);
    }

    /**
     * Update an existing post
     *
     * @param PostRequest $request
     * @param $post
     * @return JsonResponse
     */
    public function update(PostRequest $request, $post): JsonResponse
    {
        $response = $this->postService->update($request->validated(), $post);

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
        $response = $this->postService->like($request->user_id, $post);

        return $this->success($response, 'Post liked', Response::HTTP_CREATED);
    }

    /**
     * Remove an existing post
     *
     * @param $post
     * @return JsonResponse
     */
    public function destroy($post): JsonResponse
    {
        $this->postService->delete($post);

        return $this->deleted();
    }
}
