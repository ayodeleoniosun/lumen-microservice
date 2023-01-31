<?php

namespace App\Http\Controllers;

use App\Contracts\PostServiceInterface;
use App\Contracts\CommentServiceInterface;
use App\Contracts\UserServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;

class CommentController extends Controller
{
    use ApiResponseTrait;

    public CommentServiceInterface $commentService;

    public PostServiceInterface $postService;

    public UserServiceInterface $userService;

    /**
     * @param CommentServiceInterface $commentService
     * @param PostServiceInterface $postService
     * @param UserServiceInterface $userService
     */
    public function __construct(CommentServiceInterface $commentService, PostServiceInterface $postService, UserServiceInterface $userService)
    {
        $this->commentService = $commentService;
        $this->postService = $postService;
        $this->userService = $userService;
    }

    /**
     * Return list of comments
     *
     * @return Response|ResponseFactory
     */
    public function index(int $post): Response|ResponseFactory
    {
        return $this->success($this->commentService->index($post));
    }

    /**
     * Create new comment
     *
     * @param Request $request
     * @return Response|ResponseFactory
     */
    public function store(Request $request): Response|ResponseFactory
    {
        $this->userService->show($request->user_id);

        $this->postService->show($request->post_id);

        return $this->success($this->commentService->store($request->all()));
    }

    /**
     * get and show details of existing comment
     *
     * @param $comment
     * @return Response|ResponseFactory
     */
    public function show($comment): Response|ResponseFactory
    {
        return $this->success($this->commentService->show($comment));
    }

    /**
     * like existing post
     *
     * @param Request $request
     * @param int $comment
     * @return Response|ResponseFactory
     */
    public function like(Request $request, int $comment): Response|ResponseFactory
    {
        $this->userService->show($request->user_id);

        return $this->success($this->commentService->like($request->all(), $comment));
    }

    /**
     * Update an existing comment
     *
     * @param Request $request
     * @param $comment
     * @return Response|ResponseFactory
     */
    public function update(Request $request, $comment): Response|ResponseFactory
    {
        $this->userService->show($request->user_id);

        return $this->success($this->commentService->update($request->all(), $comment));
    }

    /**
     * Remove an existing comment
     *
     * @param $comment
     * @return JsonResponse
     */
    public function destroy($comment): \Illuminate\Http\JsonResponse
    {
        $this->commentService->delete($comment);

        return $this->deleted();
    }
}
