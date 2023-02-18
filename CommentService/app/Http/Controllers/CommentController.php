<?php

namespace App\Http\Controllers;

use App\Contracts\CommentServiceInterface;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\Model;
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
     * @param int $post
     * @return CommentCollection
     */
    public function index(int $post): CommentCollection
    {
        return $this->commentService->index($post);
    }

    /**
     * Create new comment
     *
     * @param CommentRequest $request
     * @return Model
     */
    public function store(CommentRequest $request): Model
    {
        return $this->commentService->create($request->validated());
    }

    /**
     * get and show details of existing comment
     *
     * @param $comment
     * @return CommentResource
     */
    public function show($comment): CommentResource
    {
        return $this->commentService->show($comment);
    }

    /**
     * Update an existing comment
     *
     * @param CommentRequest $request
     * @param $comment
     * @return Model
     */
    public function update(CommentRequest $request, $comment): Model
    {
        return $this->commentService->update($request->validated(), $comment);
    }

    /**
     * like existing comment
     *
     * @param Request $request
     * @param int $comment
     * @return Model
     */
    public function like(Request $request, int $comment): Model
    {
        return $this->commentService->like($request->user_id, $comment);
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
