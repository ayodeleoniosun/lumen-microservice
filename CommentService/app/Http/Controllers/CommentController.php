<?php

namespace App\Http\Controllers;

use App\Contracts\CommentServiceInterface;
use App\Http\Requests\CommentRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    use ApiResponseTrait;

    protected CommentServiceInterface $commentService;

    /**
     * @param CommentServiceInterface $commentService
     */
    public function __construct(CommentServiceInterface $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Return list of comments
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $response = $this->commentService->index();

        return $this->success($response);
    }

    /**
     * Create new comment
     *
     * @param CommentRequest $request
     * @return JsonResponse
     */
    public function store(CommentRequest $request): JsonResponse
    {
        $response = $this->commentService->create($request->validated());

        return $this->success($response, 'Comment successfully added', Response::HTTP_CREATED);
    }

    /**
     * get and show details of existing comment
     *
     * @param $comment
     * @return JsonResponse
     */
    public function show($comment): JsonResponse
    {
        $response = $this->commentService->show($comment);

        return $this->success($response);
    }

    /**
     * Update an existing comment
     *
     * @param CommentRequest $request
     * @param $comment
     * @return JsonResponse
     */
    public function update(CommentRequest $request, $comment): JsonResponse
    {
        $response = $this->commentService->update($request->validated(), $comment);

        return $this->success($response, 'Comment successfully updated');
    }

    /**
     * like existing comment
     *
     * @param Request $request
     * @param int $comment
     * @return JsonResponse
     */
    public function like(Request $request, int $comment): JsonResponse
    {
        $response = $this->commentService->like($request->user_id, $comment);

        return $this->success($response, 'Comment liked', Response::HTTP_CREATED);
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
