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
    /**
     * Get all posts
     *
     * @return PostCollection
     */
    public function index(): PostCollection
    {
        return new PostCollection(Post::with('likes')->get());
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
        $postExist = Post::whereUserId($data['user_id'])->whereTitle($data['title'])->exists();

        throw_if($postExist, PostExistException::class);

        return Post::create($data);
    }

    /**
     *  Show post details
     * @param int $post
     * @return PostResource
     */
    public function show(int $post): PostResource
    {
        return new PostResource(Post::findOrFail($post));
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
        $post = Post::findOrFail($id);

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
        $hasLiked = PostLike::whereUserId($user)->wherePostId($id)->exists();

        throw_if($hasLiked, UserAlreadyLikedPostException::class);

        $post = Post::findOrFail($id);

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
        $post = Post::findOrFail($id);
        $post->delete();
    }
}
