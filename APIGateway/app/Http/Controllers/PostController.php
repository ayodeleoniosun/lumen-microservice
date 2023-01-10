<?php

namespace App\Http\Controllers;

use App\Contracts\PostServiceInterface;
use App\Contracts\UserServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;

class PostController extends Controller
{
    use ApiResponseTrait;

    public PostServiceInterface $postService;

    public UserServiceInterface $userService;

    /**
     * @param PostServiceInterface $postService
     * @param UserServiceInterface $userService
     */
    public function __construct(PostServiceInterface $postService, UserServiceInterface $userService)
    {
        $this->postService = $postService;
        $this->userService = $userService;
    }

    /**
     * Return list of posts
     *
     * @return Response|ResponseFactory
     */
    public function index(): Response|ResponseFactory
    {
        return $this->success($this->postService->index());
    }

    /**
     * Create new post
     *
     * @param Request $request
     * @return Response|ResponseFactory
     */
    public function store(Request $request): Response|ResponseFactory
    {
        $this->userService->show($request->user_id);

        return $this->success(
            $this->postService->create($request->all())
        );
    }

    /**
     * get and show details of existing post
     *
     * @param $post
     * @return Response|ResponseFactory
     */
    public function show($post): Response|ResponseFactory
    {
        return $this->success($this->postService->show($post));
    }

    /**
     * Update an existing post
     *
     * @param Request $request
     * @param $post
     * @return Response|ResponseFactory
     */
    public function update(Request $request, $post): Response|ResponseFactory
    {
        $this->userService->show($request->user_id);

        return $this->success($this->postService->update($request->all(), $post));
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
