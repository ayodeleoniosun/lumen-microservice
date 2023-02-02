<?php

namespace App\Services;

use App\Contracts\PostServiceInterface;
use App\Exceptions\PostExistException;
use App\Exceptions\UserAlreadyLikedPostException;
use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
use App\Models\Post\Post;
use App\Models\Post\PostLike;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class PostService implements PostServiceInterface
{
    public Post $post;
    public PostLike $postLike;

    /**
     * @param Post $post
     * @param PostLike $postLike
     */
    public function __construct(Post $post, PostLike $postLike)
    {
        $this->post = $post;
        $this->postLike = $postLike;
    }

    /**
     * Get all posts
     *
     * @return PostCollection
     */
    public function index(): PostCollection
    {
        return new PostCollection($this->post->with('likes')->get());
    }

    /**
     * Create new post
     *
     * @param array $data
     * @return Model
     * @throws Throwable
     */
    public function create(array $data): Model
    {
        $postExist = $this->post->whereUserId($data['user_id'])->whereTitle($data['title'])->exists();

        throw_if($postExist, PostExistException::class);

        return $this->post->create($data);
    }

    /**
     *  Show post details
     * @param int $post
     * @return PostResource
     */
    public function show(int $post): PostResource
    {
        return new PostResource($this->post->findOrFail($post));
    }

    /**
     *  Update post details
     * @param array $data
     * @param int $id
     * @return Model
     * @throws Throwable
     */
    public function update(array $data, int $id): Model
    {
        $post = $this->post->findOrFail($id);

        throw_if((int) $data['user_id'] !== $post->user_id, AuthorizationException::class);

        $post->title = $data['title'];
        $post->content = $data['content'];
        $post->save();

        return $post;
    }

    /**
     * @param int $user
     * @param int $id
     * @return Model
     * @throws UserAlreadyLikedPostException
     * @throws Throwable
     */
    public function like(int $user, int $id): Model
    {
        $hasLiked = $this->postLike->whereUserId($user)->wherePostId($id)->exists();

        throw_if($hasLiked, UserAlreadyLikedPostException::class);

        $post = $this->post->findOrFail($id);

        return $post->likes()->create([
            'user_id' => $user,
        ]);
    }

    /**
     *  Remove post details
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $post = $this->post->findOrFail($id);
        $post->delete();
    }
}
