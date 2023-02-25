<?php

namespace App\Http\Controllers;

use App\Contracts\PostServiceInterface;
use App\Contracts\CommentServiceInterface;
use App\Traits\ApiResponseTrait;
use App\Traits\AuthUserTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;

class CommentController extends Controller
{
    use ApiResponseTrait, AuthUserTrait;

    public CommentServiceInterface $commentService;

    public PostServiceInterface $postService;

    /**
     * @param CommentServiceInterface $commentService
     * @param PostServiceInterface $postService
     */
    public function __construct(CommentServiceInterface $commentService, PostServiceInterface $postService)
    {
        $this->commentService = $commentService;
        $this->postService = $postService;
    }

    /**
     * Return list of comments
     *
     * @param int $post
     * @return JsonResponse
     */
    public function index(int $post): JsonResponse
    {
        return $this->success($this->commentService->index($post));
    }

    /**
     * Create new comment
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->postService->show($request->post_id);

        $payload = $this->attachUserToPayload($request);

        $response = $this->commentService->store($payload);

        return $this->success($response, "Comment successfully added", Response::HTTP_CREATED);
    }

    /**
     * get and show details of existing comment
     *
     * @param $comment
     * @return JsonResponse
     */
    public function show($comment): JsonResponse
    {
        return $this->success($this->commentService->show($comment));
    }

    /**
     * like existing post
     *
     * @param Request $request
     * @param int $comment
     * @return JsonResponse
     */
    public function like(Request $request, int $comment): JsonResponse
    {
        $payload = $this->attachUserToPayload($request);

        $response = $this->commentService->like($payload, $comment);

        return $this->success($response, "Comment liked", Response::HTTP_CREATED);
    }

    /**
     * Update an existing comment
     *
     * @param Request $request
     * @param $comment
     * @return JsonResponse
     */
    public function update(Request $request, $comment): JsonResponse
    {
        $payload = $this->attachUserToPayload($request);

        $response = $this->commentService->update($payload, $comment);

        return $this->success($response, "Comment successfully updated");
    }

    /**
     * Remove an existing comment
     *
     * @param $comment
     * @return JsonResponse
     */
    public function destroy($comment): JsonResponse
    {
        $this->commentService->delete($comment);

        return $this->deleted();
    }
}
