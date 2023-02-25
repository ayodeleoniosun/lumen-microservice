<?php

namespace App\Http\Controllers;

use App\Contracts\PostServiceInterface;
use App\Http\Requests\PostRequest;
use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\Model;
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
     * @return PostCollection
     */
    public function index(): PostCollection
    {
        return $this->postService->index();
    }

    /**
     * Create new post
     *
     * @param PostRequest $request
     * @return Model
     */
    public function store(PostRequest $request): Model
    {
        return $this->postService->create($request->validated());
    }

    /**
     * get and show details of existing post
     *
     * @param int $post
     * @return PostResource
     */
    public function show(int $post): PostResource
    {
        return $this->postService->show($post);
    }

    /**
     * Update an existing post
     *
     * @param PostRequest $request
     * @param $post
     * @return Model
     */
    public function update(PostRequest $request, $post): Model
    {
        return $this->postService->update($request->validated(), $post);
    }

    /**
     * like existing post
     *
     * @param Request $request
     * @param int $post
     * @return Model
     */
    public function like(Request $request, int $post): Model
    {
        return $this->postService->like($request->user_id, $post);
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
