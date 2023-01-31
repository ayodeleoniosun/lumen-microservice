<?php

namespace App\Services;

use App\Contracts\PostServiceInterface;
use App\Exceptions\PostExistException;
use App\Exceptions\UserAlreadyLikePostException;
use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
use App\Models\Post\Post;
use App\Models\Post\PostLike;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;

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
     * @return Model
     * @throws PostExistException
     */
    public function create(array $data): Model
    {
        $postExist = Post::where([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
        ])->exists();

        if ($postExist) {
            throw new PostExistException();
        }

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
     * @return Model
     * @throws AuthorizationException
     */
    public function update(array $data, int $id): Model
    {
        $post = Post::findOrFail($id);

        if ((int) $data['user_id'] !== $post->user_id) {
            throw new AuthorizationException();
        }

        $post->title = $data['title'];
        $post->content = $data['content'];
        $post->save();

        return $post;
    }

    /**
     * @param int $user
     * @param int $id
     * @return Model
     * @throws UserAlreadyLikePostException
     */
    public function like(int $user, int $id): Model
    {
        $hasLiked = PostLike::where([
            'user_id' => $user,
            'post_id' => $id,
        ])->exists();

        if ($hasLiked) {
            throw new UserAlreadyLikePostException();
        }

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
